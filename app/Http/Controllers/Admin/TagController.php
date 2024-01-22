<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = Tag::paginate(4);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Tag $tags)
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|max:255',
        ]);

        if ($validated->fails()) {
            return redirect()->route('tags.create')->withErrors($validated)->withInput();
        }

        Tag::create([
            'title' => $request->title,
        ]);


        return redirect()->route('tags.index')->with('success', 'Тег добавлена');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $data = $request->only('title');
        $tag->update($data);

        return redirect()->route('tags.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        if ($tag->posts->count()) {
            return redirect()->route('tags.index')->with('error', 'Ошибка! У тега есть пост');
        }
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Тег успешно удален');
    }
}
