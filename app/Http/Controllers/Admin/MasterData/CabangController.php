<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CabangController extends Controller
{
    public function index()
    {
        return view('admin.master-data.cabang.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $branches = Branch::select('branches.*');

            return DataTables::of($branches)
                ->addIndexColumn()
                ->addColumn('employee_count', function($branch) {
                    $count = $branch->employees()->count();
                    return '<span class="badge badge-secondary">' . $count . ' Pegawai</span>';
                })
                ->addColumn('action', function($branch) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="editBranch(\'' . e($branch->id_branch) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-delete" 
                                    onclick="deleteBranch(\'' . e($branch->id_branch) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('branch_name', function($branch) {
                    return '<strong>' . e($branch->branch_name) . '</strong>';
                })
                ->editColumn('created_at', function($branch) {
                    return $branch->created_at->format('d/m/Y H:i');
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where('branch_name', 'like', "%{$search}%");
                    }
                })
                ->rawColumns(['branch_name', 'employee_count', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'branch_name' => 'required|string|unique:branches,branch_name|max:255',
            ], [
                'branch_name.required' => 'Nama cabang wajib diisi',
                'branch_name.unique' => 'Nama cabang sudah digunakan',
                'branch_name.max' => 'Nama cabang maksimal 255 karakter'
            ]);

            $branch = Branch::create([
                'branch_name' => $validated['branch_name'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil ditambahkan!',
                'data' => $branch
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Store branch error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $branch = Branch::with('employees')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $branch
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cabang tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $branch = Branch::findOrFail($id);

            $validated = $request->validate([
                'branch_name' => ['required', 'string', 'max:255', Rule::unique('branches')->ignore($branch->id_branch, 'id_branch')],
            ], [
                'branch_name.required' => 'Nama cabang wajib diisi',
                'branch_name.unique' => 'Nama cabang sudah digunakan',
                'branch_name.max' => 'Nama cabang maksimal 255 karakter'
            ]);

            $branch->update([
                'branch_name' => $validated['branch_name'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil diupdate!',
                'data' => $branch
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Update branch error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $branch = Branch::findOrFail($id);
            
            // Check if branch has associated employees
            $employeeCount = $branch->employees()->count();
            if ($employeeCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cabang tidak dapat dihapus karena masih memiliki {$employeeCount} pegawai. Hapus atau pindahkan pegawai terlebih dahulu."
                ], 422);
            }
            
            $branch->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cabang berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
