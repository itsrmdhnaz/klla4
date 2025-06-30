<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Spreadsheet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class SpreadsheetController extends Controller
{
    public function index()
    {
        return view('admin.master-data.spreadsheet.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $spreadsheets = Spreadsheet::select('spreadsheets.*');

            return DataTables::of($spreadsheets)
                ->addIndexColumn()
                ->addColumn('status', function($spreadsheet) {
                    $statusClass = $spreadsheet->is_active ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $spreadsheet->is_active ? 'Aktif' : 'Tidak Aktif';
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->addColumn('credentials_status', function($spreadsheet) {
                    $hasCredentials = $spreadsheet->hasValidCredentials();
                    $statusClass = $hasCredentials ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $hasCredentials ? 'Valid' : 'Tidak Valid';
                    
                    return '<span class="' . $statusClass . '">' . $statusText . '</span>';
                })
                ->addColumn('action', function($spreadsheet) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="editSpreadsheet(\'' . e($spreadsheet->id) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-delete" 
                                    onclick="deleteSpreadsheet(\'' . e($spreadsheet->id) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('name', function($spreadsheet) {
                    return '<strong>' . e($spreadsheet->name) . '</strong>';
                })
                ->editColumn('spreadsheet_url', function($spreadsheet) {
                    return $spreadsheet->spreadsheet_url ? 
                        '<a href="' . e($spreadsheet->spreadsheet_url) . '" target="_blank" class="text-decoration-none">' 
                        . '<i class="fas fa-external-link-alt"></i> View Sheet</a>' : '-';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                              ->orWhere('spreadsheet_id', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['name', 'spreadsheet_url', 'status', 'credentials_status', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:spreadsheets,name',
                'spreadsheet_id' => 'required|string|unique:spreadsheets,spreadsheet_id',
                'spreadsheet_url' => 'nullable|url',
                'description' => 'nullable|string',
                'service_account_email' => 'nullable|email',
                'credentials_file' => 'required|file|mimes:json|max:2048'
            ], [
                'name.required' => 'Nama spreadsheet wajib diisi',
                'spreadsheet_id.required' => 'Spreadsheet ID wajib diisi',
                'spreadsheet_id.unique' => 'Spreadsheet ID sudah digunakan',
                'spreadsheet_url.url' => 'Format URL tidak valid',
                'service_account_email.email' => 'Format email tidak valid',
                'credentials_file.required' => 'File credentials wajib diupload',
                'credentials_file.mimes' => 'File credentials harus berformat JSON',
                'credentials_file.max' => 'Ukuran file maksimal 2MB'
            ]);

            // Validate credentials file content
            $credentialsContent = file_get_contents($request->file('credentials_file')->getRealPath());
            $credentials = json_decode($credentialsContent, true);
            
            if (!$credentials || !isset($credentials['type']) || $credentials['type'] !== 'service_account') {
                return response()->json([
                    'success' => false,
                    'message' => 'File credentials tidak valid. Pastikan file adalah service account JSON.'
                ], 422);
            }

            $spreadsheet = Spreadsheet::create([
                'name' => $validated['name'],
                'spreadsheet_id' => $validated['spreadsheet_id'],
                'spreadsheet_url' => $validated['spreadsheet_url'],
                'description' => $validated['description'],
                'service_account_email' => $validated['service_account_email'] ?? $credentials['client_email'] ?? null,
            ]);

            // Store credentials securely
            if (!$spreadsheet->storeCredentials($credentials)) {
                $spreadsheet->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan credentials'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Spreadsheet berhasil ditambahkan!',
                'data' => $spreadsheet
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store spreadsheet error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $spreadsheet = Spreadsheet::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $spreadsheet
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Spreadsheet tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $spreadsheet = Spreadsheet::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:spreadsheets,name,' . $spreadsheet->id,
                'spreadsheet_id' => ['required', 'string', Rule::unique('spreadsheets')->ignore($spreadsheet->id)],
                'spreadsheet_url' => 'nullable|url',
                'description' => 'nullable|string',
                'service_account_email' => 'nullable|email',
                'credentials_file' => 'nullable|file|mimes:json|max:2048'
            ], [
                'name.required' => 'Nama spreadsheet wajib diisi',
                'spreadsheet_id.required' => 'Spreadsheet ID wajib diisi',
                'spreadsheet_id.unique' => 'Spreadsheet ID sudah digunakan',
                'spreadsheet_url.url' => 'Format URL tidak valid',
                'service_account_email.email' => 'Format email tidak valid',
                'credentials_file.mimes' => 'File credentials harus berformat JSON',
                'credentials_file.max' => 'Ukuran file maksimal 2MB'
            ]);

            $spreadsheet->update([
                'name' => $validated['name'],
                'spreadsheet_id' => $validated['spreadsheet_id'],
                'spreadsheet_url' => $validated['spreadsheet_url'],
                'description' => $validated['description'],
                'service_account_email' => $validated['service_account_email'],
            ]);

            // Update credentials if new file uploaded
            if ($request->hasFile('credentials_file')) {
                $credentialsContent = file_get_contents($request->file('credentials_file')->getRealPath());
                $credentials = json_decode($credentialsContent, true);
                
                if (!$credentials || !isset($credentials['type']) || $credentials['type'] !== 'service_account') {
                    return response()->json([
                        'success' => false,
                        'message' => 'File credentials tidak valid. Pastikan file adalah service account JSON.'
                    ], 422);
                }

                if (!$spreadsheet->storeCredentials($credentials)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan credentials'
                    ], 500);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Spreadsheet berhasil diupdate!',
                'data' => $spreadsheet
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update spreadsheet error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $spreadsheet = Spreadsheet::findOrFail($id);
            
            // Delete credentials file
            if ($spreadsheet->credentials_path) {
                Storage::disk('credentials')->delete($spreadsheet->credentials_path);
            }
            
            $spreadsheet->delete();

            return response()->json([
                'success' => true,
                'message' => 'Spreadsheet berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
