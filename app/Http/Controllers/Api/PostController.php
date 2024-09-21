<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    // Get Data
    public function index()
    {
        // Get all posts
        $posts = Post::latest()->paginate(5);

        // Return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    // Insert Data
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Create post
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
        ]);

        // Return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    // Get Data by Id
    public function show($id)
    {
        // Find post by ID
        $post = Post::find($id);

        // Return single post as a resource
        return new PostResource(true, 'Detail Data Post!', $post);
    }

    // Update Data
    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find post by ID
        $post = Post::find($id);

        // Check if image is not empty
        if ($request->hasFile('image')) {

            // Upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Delete old image
            Storage::delete('public/posts/' . basename($post->image));

            // Update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        } else {

            // Update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
            ]);
        }

        // Return response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    // Delete Data
    public function destroy($id)
    {

        // Find post by ID
        $post = Post::find($id);

        // Delete image
        Storage::delete('public/posts/'.basename($post->image));

        // Delete post
        $post->delete();

        // Return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}
