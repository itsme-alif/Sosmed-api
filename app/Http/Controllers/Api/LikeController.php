<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\like;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = like::query();
        $query->orderBy('id', 'asc');
        $like = $query->paginate(10);

        if ($like->isEmpty()) {
            return response()->json(['message' => 'Data Like Masih Kosong!'], 404);
        }

        return new DataResource(true, 'List Data like', $like);
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
            'like'     => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = like::create([
            'like'     => $request->like,
            'post_id'     => $request->post_id,
        ]);

        return new DataResource(true, 'Anda Telah Menyukai Postingan Tersebut!', $post);
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

        // $like = like::find($id);
        // if (!$like) {
        //     return response()->json(['message' => 'Komentar Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        // }

        // $like->update([
        //     'like' => $request->like,
        // ]);

        // return new DataResource(true, 'Data like Berhasil Diubah!', $like);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = like::find($id);
        if (!$delete) {
            return response()->json(['message' => 'Data Yang Anda Cari Tidak Ada'], 404);
        }
        $delete->delete();
        return new DataResource(true, 'Anda Tidak Lagi Menyukai Postingan Tersebut', null);
    }
}
