<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\comments;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = comments::query();
        $query->orderBy('id', 'asc');
        $comments = $query->paginate(10);

        if ($comments->isEmpty()) {
            return response()->json(['message' => 'Data Komentar Masih Kosong!'], 404);
        }

        return new DataResource(true, 'List Data Komentar', $comments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment'     => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = comments::create([
            'comment'     => $request->comment,
            'post_id'     => $request->post_id,
        ]);

        return new DataResource(true, 'Komentar Anda Berhasil Ditambahkan!', $post);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $comments = comments::find($id);
        if (!$comments) {
            return response()->json(['message' => 'Komentar Tidak Dapat Di Temukan'], 404);
        }

        $comments->update([
            'comment' => $request->comment,
        ]);

        return new DataResource(true, 'Komentar Anda Berhasil Di Ubah!', $comments);
    }

    public function destroy(string $id)
    {
        $delete = comments::find($id);
        if (!$delete) {
            return response()->json(['message' => 'Komentar Tidak Dapat Di Temukan'], 404);
        }
        $delete->delete();
        return new DataResource(true, 'Komentar Anda Berhasil Dihapus Dari Postingan Tersebut', null);
    }
}
