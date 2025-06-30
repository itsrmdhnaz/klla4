<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PegawaiController extends Controller
{
    public function index()
    {
        return view('admin.master-data.pegawai.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $employees = Employee::with('branches')->select('employees.*');

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('branches', function($employee) {
                    return $employee->branches->map(function($branch) {
                        return '<span class="badge badge-secondary">' 
                               . e($branch->branch_name) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('status', function($employee) {
                    $badgeClass = $employee->is_active ? 'badge badge-success' : 'badge badge-danger';
                    $statusText = $employee->is_active ? 'Aktif' : 'Tidak Aktif';
                    
                    return '<span class="' . $badgeClass . '">' . $statusText . '</span>';
                })
                ->addColumn('action', function($employee) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" 
                                    class="btn-action btn-edit" 
                                    onclick="editEmployee(\'' . e($employee->nip) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                    class="btn-action btn-delete" 
                                    onclick="deleteEmployee(\'' . e($employee->nip) . '\')"
                                    data-bs-toggle="tooltip" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->editColumn('nip', function($employee) {
                    return '<code>' . e($employee->nip) . '</code>';
                })
                ->editColumn('nama_pegawai', function($employee) {
                    return '<strong>' . e($employee->nama_pegawai) . '</strong>';
                })
                ->editColumn('email', function($employee) {
                    return '<a href="mailto:' . e($employee->email) . '" class="text-decoration-none">' 
                           . e($employee->email) . '</a>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && !empty($request->get('search')['value'])) {
                        $search = $request->get('search')['value'];
                        $query->where(function($q) use ($search) {
                            $q->where('nip', 'like', "%{$search}%")
                              ->orWhere('nama_pegawai', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    }
                })
                ->rawColumns(['nip', 'nama_pegawai', 'email', 'branches', 'status', 'action'])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            // Debug log untuk melihat request data
            \Log::info('Store request data:', $request->all());

            $validated = $request->validate([
                'nip' => 'required|string|unique:employees,nip|max:20',
                'nama_pegawai' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'branches' => 'required|array|min:1',
                'branches.*' => 'exists:branches,id_branch'
            ], [
                'nip.required' => 'NIP wajib diisi',
                'nip.unique' => 'NIP sudah digunakan',
                'nama_pegawai.required' => 'Nama pegawai wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'branches.required' => 'Cabang wajib dipilih',
                'branches.array' => 'Cabang harus berupa array',
                'branches.min' => 'Minimal pilih 1 cabang',
                'branches.*.exists' => 'Cabang yang dipilih tidak valid'
            ]);

            $employee = new \App\Models\Employee();
            $employee->nip = $validated['nip'];
            $employee->nama_pegawai = $validated['nama_pegawai'];
            $employee->email = $validated['email'];
            $employee->is_active = true;
            $employee->save();

            // Attach branches using foreach to ensure 'id' is filled for pivot
            if (!empty($validated['branches'])) {
                foreach ($validated['branches'] as $branchId) {
                    // Avoid duplicate entry for (employee_nip, branch_id)
                    $exists = \App\Models\EmployeeBranch::where('employee_nip', $employee->nip)
                        ->where('branch_id', $branchId)
                        ->exists();
                    if (!$exists) {
                        \App\Models\EmployeeBranch::create([
                            'employee_nip' => $employee->nip,
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            } else {
                throw new \Exception('Cabang wajib dipilih');
            }

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil ditambahkan!',
                'data' => $employee->load('branches')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Store employee error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($nip)
    {
        try {
            $employee = Employee::with('branches')->findOrFail($nip);
            
            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $nip)
    {
        \DB::beginTransaction();
        try {
            $employee = \App\Models\Employee::findOrFail($nip);

            // Debug log untuk melihat request data
            \Log::info('Update request data:', $request->all());

            $validated = $request->validate([
                'nama_pegawai' => 'required|string|max:255',
                'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('employees')->ignore($employee->nip, 'nip')],
                'branches' => 'required|array|min:1',
                'branches.*' => 'exists:branches,id_branch'
            ], [
                'nama_pegawai.required' => 'Nama pegawai wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'branches.required' => 'Cabang wajib dipilih',
                'branches.array' => 'Cabang harus berupa array',
                'branches.min' => 'Minimal pilih 1 cabang',
                'branches.*.exists' => 'Cabang yang dipilih tidak valid'
            ]);

            $employee->nama_pegawai = $validated['nama_pegawai'];
            $employee->email = $validated['email'];
            $employee->save();

            // Sync branches, handle possible error
            if (!empty($validated['branches'])) {
                // Remove all then re-add to ensure pivot 'id' is filled
                $employee->branches()->detach();
                foreach ($validated['branches'] as $branchId) {
                    // Avoid duplicate entry for (employee_nip, branch_id)
                    $exists = \App\Models\EmployeeBranch::where('employee_nip', $employee->nip)
                        ->where('branch_id', $branchId)
                        ->exists();
                    if (!$exists) {
                        \App\Models\EmployeeBranch::create([
                            'employee_nip' => $employee->nip,
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            } else {
                throw new \Exception('Cabang wajib dipilih');
            }

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil diupdate!',
                'data' => $employee->load('branches')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Update employee error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($nip)
    {
        \DB::beginTransaction();
        try {
            $employee = \App\Models\Employee::findOrFail($nip);
            $employee->branches()->detach();
            $employee->delete();

            \DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBranches()
    {
        $branches = Branch::select('id_branch', 'branch_name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $branches
        ]);
    }
}
