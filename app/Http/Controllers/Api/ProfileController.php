<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Http\Resources\DataResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Profile::query();

        if ($request->has('username')) {
            $username = $request->input('username');
            $query->where('username', 'like', '%' . $username . '%');
        }

        $query->orderBy('id', 'asc');

        $users = $query->paginate(10);

        if ($users->isEmpty()) {
            return response()->json(['message' => 'Profile Dengan Nama Tersebut Tidak Dapat Ditemukan'], 404);
        }

        return new DataResource(true, 'List Data Profile', $users);
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
            'username'      => 'required|min:4|max:13|unique:profiles',
            'phone_number'  => 'required|min:14|numeric|unique:profiles',
            'first_name'    => 'required',
            'date_of_birth' => 'required|date',
            'image'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::latest()->first();

        if (!$user) {
            return response()->json(['message' => 'Anda Harus Mempunyai Akun Terlebih Dahulu Jika ingin Membuat Profile.'], 400);
        }

        $user_id = $user->id;

        if ($user){
            $image = $request->file('image');
            $image->storeAs('public/profile', $image->hashName());

            $post = Profile::create([
                'username'      => $request->username,
                'phone_number'  => $request->phone_number,
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'image'         => $image->hashName(),
                'user_id'       => $user_id
            ]);
        }

        return new DataResource(true, 'Data Profile Berhasil Ditambahkan!', $post);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detail = Profile::withCount('followers','following','post')->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Profile Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        }

        return new DataResource(true, 'Profile Yang Anda Cari Telah Ditemukan!', $detail);
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

        $profile = Profile::find($id);
        if ($request->hasFile('image')) {

            $image = $request->file('image');
            $image->storeAs('public/profile', $image->hashName());
            Storage::delete('public/profile/'.basename($profile->image));
            $profile->update([
                'image'        => $image->hashName(),
            ]);

        } elseif ($request->has('phone_number')) {

            $validator = Validator::make($request->all(), [
                'phone_number'  => 'required|min:14|numeric|unique:profiles',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $profile->update([
                'phone_number'  => $request->phone_number,
            ]);
        } elseif ($request->has('first_name')) {

            $validator = Validator::make($request->all(), [
                'first_name'  => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $profile->update([
                'first_name'  => $request->first_name,
            ]);
        } elseif ($request->has('last_name')) {

            $profile->update([
                'last_name'  => $request->last_name,
            ]);

        } elseif ($request->has('date_of_birth')) {

            $validator = Validator::make($request->all(), [
                'date_of_birth'  => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $profile->update([
                'date_of_birth'  => $request->date_of_birth,
            ]);

        } else {

            $validator = Validator::make($request->all(), [
                'username'      => 'required|min:4|max:13|unique:profiles',
                'phone_number'  => 'required|min:14|numeric|unique:profiles',
                'first_name'    => 'required',
                'date_of_birth' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $profile->update([
                'username'      => $request->username,
                'phone_number'  => $request->phone_number,
                'first_name'    => $request->first_name,
                'date_of_birth' => $request->date_of_birth,
            ]);
        }

        return new DataResource(true, 'Data Profile Anda Berhasil Diubah!', $profile);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Profile::find($id);
        if (!$delete) {
            return response()->json(['message' => 'Profile Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        }
        Storage::delete('public/profile/'.basename($delete->image));
        $delete->delete();
        return new DataResource(true, 'Profile Anda Berhasil Dihapus', null);
    }
}
