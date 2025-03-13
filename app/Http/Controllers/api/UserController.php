<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        try {
            $id = auth()->user()->id;

            $user = User::findOrFail($id);
            $roles = $user->roles->map(function ($item) {
                return $item->name;
            });
            $permissions = $user->getPermissionsViaRoles();
            $permissions = $permissions->map(function ($item) {
                return $item->name;
            });

            $rolesAndPermissions = $user->roles->map(function ($item) {
                $permissions = $item->permissions->map(function ($item) {
                    return $item->name;
                });
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'permissions' => $permissions
                ];
            });

            $unit = $user->unit ? $user->unit->nama : '-';
            $divisi = $user->divisi ? $user->divisi->nama : '-';
            $departemen = $user->departemen ? $user->departemen->nama : '-';
            $jabatan = $user->jabatan ? $user->jabatan->nama : '-';

            $response = [
                'nip' => $user->nip,
                'nama' => $user->nama,
                'unit' => $unit,
                'divisi' => $divisi,
                'departemen' => $departemen,
                'jabatan' => $jabatan,
                'role' => $roles,
                'permissions' => $permissions,
                'roleAndPermissions' => $rolesAndPermissions
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data user login.',
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            $code = 500;
            if ($e->getCode()) {
                $code = $e->getCode();
            }
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function permissions(Request $request)
    {
        try {
            $id = auth()->user()->id;

            $user = User::findOrFail($id);
            $permissions = $user->getPermissionsViaRoles();

            $response = [
                'nip' => $user->nip,
                'nama' => $user->nama,
                'jabatan' => $user->jabatan,
                'permissions' => $permissions
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data user login.',
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            $code = 500;
            if ($e->getCode()) {
                $code = $e->getCode();
            }
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = new User();
            $user->nama = $request->nama;
            $user->nip = $request->nip;
            $user->password = bcrypt($request->password);
            $user->is_active = $request->is_active;
            $user->unit_id = $request->unit_id;
            $user->divisi_id = $request->divisi_id;
            $user->departemen_id = $request->departemen_id;
            $user->jabatan_id = $request->jabatan_id;
            $user->save();

            foreach ($request->roles as $row) {
                $role = Role::findById($row);
                $user->assignRole($role->name);
            }


            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User berhasil disimpan.',
                'data' => $user
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->deleted = 1;
            $user->deleted_at = now()->toDateTimeLocalString();
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User berhasil dihapus.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $user->nama = $request->nama;
            $user->nip = $request->nip;
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }
            $user->is_active = $request->is_active;
            $user->unit_id = $request->unit_id;
            $user->divisi_id = $request->divisi_id;
            $user->departemen_id = $request->departemen_id;
            $user->jabatan_id = $request->jabatan_id;
            $user->save();

            foreach ($request->roles as $row) {
                $role = Role::findById($row);
                $user->assignRole($role->name);
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'User berhasil diperbarui.',
                'data' => $user
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function find($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->unit = $user->unit->nama ?? '-';
            $user->divisi = $user->divisi->nama ?? '-';
            $user->departemen = $user->departemen->nama ?? '-';
            $user->jabatan = $user->jabatan->nama ?? '-';
            $user->roles = $user->roles;

            return response()->json([
                'status' => true,
                'message' => 'User ditemukan.',
                'data' => $user
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);

            $data = User::where('deleted', '0')->paginate($perPage);

            $response = collect($data->items())->map(function ($item) {
                return [
                    "id" => $item->id,
                    "nip" => $item->nip,
                    "nama" => $item->nama,
                    "is_active" => $item->is_active === '1' ? true : false,
                    "unit_id" => $item->unit_id,
                    "divisi_id" => $item->divisi_id,
                    "departemen_id" => $item->departemen_id,
                    "jabatan_id" => $item->jabatan_id,
                    'unit' => $item->unit->nama ?? '-',
                    'divisi' => $item->divisi->nama ?? '-',
                    'departemen' => $item->departemen->nama ?? '-',
                    'jabatan' => $item->jabatan->nama ?? '-',
                    'roles' => $item->roles->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'nama' => $item->name
                        ];
                    })
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Data user berhasil diambil.',
                'data' => $response,
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'total' => $data->total(),
                    'per_page' => $data->perPage(),
                    'last_page' => $data->lastPage(),
                    'next_page_url' => $data->nextPageUrl(),
                    'prev_page_url' => $data->previousPageUrl(),
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
