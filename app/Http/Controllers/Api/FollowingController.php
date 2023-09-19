<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Following;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class FollowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Following::query();
        $query->orderBy('id', 'asc');
        $follow = $query->paginate(10);

        if ($follow->isEmpty()) {
            return response()->json(['message' => 'Data Following Masih Kosong!'], 404);
        }

        return new DataResource(true, 'List Data following', $follow);
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

        $post = Following::create([
            'follow'     => $request->follow,
            'profile_id'     => $request->profile_id,
        ]);

        return new DataResource(true, 'Anda Berhasil Menjadi Followers Baru Akun Tersebut!', $post);
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

        // $follow = Following::find($id);
        // if (!$follow) {
        //     return response()->json(['message' => 'Data Yang Anda Cari Tidak Ada'], 404);
        // }

        // $follow->update([
        //     'follow' => $request->follow,
        // ]);

        // return new DataResource(true, '!', $follow);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Following::find($id);
        if (!$delete) {
            return response()->json(['message' => 'Data Following Yang Anda Cari Tidak Anda'], 404);
        }
        $delete->delete();
        return new DataResource(true, 'Anda Telah Berhenti Mengikuti Akun Tersebut', null);
    }
}
