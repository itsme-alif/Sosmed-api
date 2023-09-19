<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\DataResource;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();
        $query->orderBy('id', 'asc');
        $profiles = $query->paginate(10);

        if ($profiles->isEmpty()) {
            return response()->json(['message' => 'Data Postingan Masih Kosong!'], 404);
        }

        return new DataResource(true, 'List Data Post', $profiles);
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
            'caption'    => 'required',
            'images.*'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $profile = Profile::latest()->first();

        if (!$profile) {
            return response()->json(['message' => 'Akun Anda Harus Mempunyai Profile Jika Ingin Memposting Sesuatu.'], 400);
        }

        $profile_id = $profile->id;

        if ($profile_id) {
            $uploadedImages = [];

            if ($request->hasFile('images')) { 
                foreach ($request->file('images') as $image) {
                    $customName = uniqid() .'.' . $image->getClientOriginalExtension();
                    $image->storeAs('public/post', $customName);
                    $uploadedImages[] = $customName;
                }
            }

            $post = Post::create([
                'caption'    => $request->caption,
                'images'     => json_encode($uploadedImages),
                'profile_id' => $profile_id,
            ]);
        }

        return new DataResource(true, 'Anda Berhasil Membagikan Postingan Baru!', $post);
    }




    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detail = Post::with('comments')->withCount('like','comments')->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Post Dengan Id Tersebut Tidak Dapat Ditemukan'], 404);
        }

        return new DataResource(true, 'Postingan Yang Anda Cari Telah Ditemukan!', $detail);

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
            'caption' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $post = Post::find($id);
            $post->update([
                'caption'      => $request->caption,
            ]);

        return new DataResource(true, 'Caption Postingan Anda Telah Diubah!', $post);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = Post::find($id);

        if (!$delete) {
            return response()->json(['message' => 'Data Yang Anda Cari Tidak Dapat Ditemukan'], 404);
        }

        $images = json_decode($delete->images);

        if ($images && is_array($images)) {
            foreach ($images as $image) {
                $imagePath = 'public/post/' . $image;

                if (Storage::exists($imagePath)) {
                    Storage::delete($imagePath);
                }
            }
        }

        $delete->delete();

        return new DataResource(true, 'Postingan Anda Berhasil Di Hapus', null);
    }


}
