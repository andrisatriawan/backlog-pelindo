<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
