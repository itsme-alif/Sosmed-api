<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Followers;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class FollowersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Followers::query();
        $query->orderBy('id', 'asc');
        $follow = $query->paginate(10);

        if ($follow->isEmpty()) {
            return response()->json(['message' => 'Data Followers Masih Kosong!'], 404);
        }

        return new DataResource(true, 'List Data follow', $follow);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'follow'     => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Followers::create([
            'follow'     => $request->follow,
            'profile_id'     => $request->profile_id,
        ]);

        return new DataResource(true, 'Anda Berhasil Menjadi Followers Akun Tersebut!', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

        // $follow = Followers::find($id);
        // if (!$follow) {
        //     return response()->json(['message' => 'Followers dengan ID tersebut tidak ada!'], 404);
        // }

        // $follow->update([
        //     'follow' => $request->follow,
        // ]);

        // return new DataResource(true, 'Data follow Berhasil Diubah!', $follow);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Followers::find($id);
        if (!$delete) {
            return response()->json(['message' => 'Followers dengan ID tersebut tidak ada!'], 404);
        }
        $delete->delete();
        return new DataResource(true, 'Akun dengan ID tersebut berhasil anda hapus dari followers anda!', null);
    }
}
