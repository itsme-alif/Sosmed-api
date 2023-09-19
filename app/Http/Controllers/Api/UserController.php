<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\DataResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('name')) {
            $name = $request->input('name');
            $query->where('name', 'like', '%' . $name . '%');
        }

        $query->orderBy('id', 'asc');

        $users = $query->paginate(10);

        if ($users->isEmpty()) {
            return response()->json(['message' => 'User Dengan Nama Tersebut Tidak Dapat Ditemukan'], 404);
        }

        return new DataResource(true, 'List Data User', $users);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return new DataResource(true, 'Data User Berhasil Ditambahkan!', $post);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detail = User::with('Profile')->find($id);

        if (!$detail) {
            return response()->json(['message' => 'User Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        }

        return new DataResource(true, 'User Yang Anda Cari Berhasil Ditemukan!', $detail);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'old_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        }

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Password Lama Yang Anda Masukkan Salah'], 422);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return new DataResource(true, 'Data User Berhasil Diubah!', $user);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = User::find($id);
        if (!$delete) {
            return response()->json(['message' => 'User Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        }
        $delete->delete();
        return new DataResource(true, 'Akun Anda Berhasil Dihapus', null);
    }
}
