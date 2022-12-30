<?php

namespace App\Http\Controllers;

use App\Models\Post;
use GrahamCampbell\ResultType\Success;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\CssSelector\Node\HashNode;

class PostController extends Controller
{    
    public function index() 
    {
        // get posts
        $posts = Post::latest()->paginate(5);
        
        // rende view with posts
      return view('posts.index', compact('posts'));
    }
    public function create()
     {
        return view ('posts.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        // uploade image
        $image = $request->file('image');
        $image -> storeAs('public/posts', $image->hashName());

        // create post
        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
        ]);
        // redirect to index
return redirect()->route('posts.index')->with(['succes' => 'data berhasil di simpan']);
    }
    public function edit(Post $post) 
    {
        return view('posts.edit', compact('post'));
        
    }
 public function update(Request $request, Post $post)
    {
        // validate form
 $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
if ($request->hasFile('image')) {
            //upload new image
            $image = $request->file('image');
            $image -> storeAs('public/posts', $image->hashName());
            //delete old image
            Storage::delete('public/posts/'.$post->image);

            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content
            ]);

        }else{
            // update posts withhout image
            $post->update([
                'title' => $request->title,
                'content' => $request->content
            ]);
        }
        return redirect('posts.index')->with(['succes' => 'data berhasil di edit']);
    }
    public function destroy(Post $post)
    {
        // delet image
        Storage::delete('public/posts/'. $post->image);
        // delete posts
        $post->delete();

        // redirect to index
        return redirect()->route('posts.index')->with(['succes' => 'data berhasil di hapus']);

    }

}