<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'content' => 'required|string',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('images', 'public');
        // 'public' here is the disk name defined in config/filesystems.php
    }

    Post::create([
        'title' => $request->input('title'),
        'content' => $request->input('content'),
        'image' => $imagePath,
        'user_id' => Auth::id(),
    ]);

    return redirect()->route('posts.index');
}

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Delete old image file if it exists
        if ($post->image && Storage::exists($post->image)) {
            Storage::delete($post->image);
        }
    
        $imagePath = null;
    
        if ($request->hasFile('image')) {
            // Store the new image file
            $imagePath = $request->file('image')->store('images', 'public');
        }
    
        // Update title, content, and image path
        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image' => $imagePath,
        ]);
    
        return redirect()->route('posts.index');
    }
    

    public function destroy(Post $post)
    {
        // Delete the associated image file if it exists
        if ($post->image) {
            Storage::delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index');
    }

    public function like(Post $post)
    {
        $post->likes()->create([
            'user_id' => auth()->id(),
            'type' => 'like',
        ]);

        return back();
    }

    public function dislike(Post $post)
    {
        $post->dislikes()->create([
            'user_id' => auth()->id(),
            'type' => 'dislike',
        ]);

        return back();
    }

}


