<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for assigning a role to a user.
     */
    public function showAssignRoleForm($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('users.assign-role', compact('user', 'roles'));
    }

    /**
     * Assign a role to a user.
     */
    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = User::findOrFail($id);

        // Detach all existing roles
        $user->roles()->detach();

        // Attach the selected role
        $role = Role::findOrFail($request->role_id);
        $user->roles()->attach($role);

        return redirect()->route('users.index')->with('success', 'Role assigned successfully!');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Assign role if provided
        if ($request->role_id) {
            $role = Role::findOrFail($request->role_id);
            $user->roles()->attach($role);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing a user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        $userData = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        // Update password only if provided
        if ($request->password) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        // Update role
        $user->roles()->detach();
        if ($request->role_id) {
            $role = Role::findOrFail($request->role_id);
            $user->roles()->attach($role);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent deletion of current user
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Remove a role from a user.
     */
    public function removeRole($id)
    {
        $user = User::findOrFail($id);

        // Detach all roles
        $user->roles()->detach();

        return redirect()->route('users.index')->with('success', 'Role removed successfully!');
    }
}
