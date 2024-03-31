<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return UserResource::collection($users);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->assignRole($request->role);

        return response()->json(['message' => 'Role assigned successfully.']);
    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->givePermissionTo($request->permission);

        return response()->json(['message' => 'Permission assigned successfully.']);
    }

    public function getUserPermissions(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $permissions = $user->getPermissionsViaRoles();

        return response()->json(['permissions' => $permissions]);
    }
}
