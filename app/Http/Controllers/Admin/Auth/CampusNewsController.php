<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\CampusNews;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CampusNewsController extends Controller
{
    /**
     * Display a listing of the campus news.
     */
    public function index()
    {
        $news = CampusNews::with('author')
            ->orderBy('publish_date', 'desc')
            ->paginate(15);

        return view('admin.campus-news.index', compact('news'));
    }

    /**
     * Show the form for creating a new news item.
     */
    public function create()
    {
        return view('admin.campus-news.create');
    }

    /**
     /**
      * Store a newly created news item in storage.
      */
     public function store(Request $request)
     {
         $validated = $request->validate([
             'title' => 'required|string|max:255',
             'content' => 'required|string',
             'excerpt' => 'nullable|string|max:500',
             'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
             'publish_date' => 'required|date',
             'category' => 'required|in:academic,events,sports,research,announcement,achievement',
             'status' => 'required|in:published,archived',
             'is_featured' => 'boolean',
             'tags' => 'nullable|string',
             'meta_title' => 'nullable|string|max:255',
             'meta_description' => 'nullable|string|max:500',
         ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image'), 'campus-news');
        }

        // Process tags
        if ($request->filled('tags')) {
            $validated['tags'] = $request->tags;
        }

        // Set author information
        $validated['author_id'] = Auth::id();
        $validated['author_name'] = Auth::user()->name;

        $news = CampusNews::create($validated);

        return redirect()
            ->route('admin.campus-news.index')
            ->with('success', 'Campus news created successfully.');
    }

    /**
     * Display the specified news item.
     */
    public function show(CampusNews $campusNews)
    {
        $campusNews->load('author');
        return view('admin.campus-news.show', compact('campusNews'));
    }

    /**
     * Show the form for editing the specified news item.
     */
    public function edit(CampusNews $campusNews)
    {
        return view('admin.campus-news.edit', compact('campusNews'));
    }

    /**
     * Update the specified news item in storage.
     */
    public function update(Request $request, CampusNews $campusNews)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'publish_date' => 'required|date',
            'category' => 'required|in:academic,events,sports,research,announcement,achievement',
            'status' => 'required|in:published,archived',
            'is_featured' => 'boolean',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($campusNews->featured_image) {
                Storage::delete($campusNews->featured_image);
            }
            $validated['featured_image'] = $this->uploadImage($request->file('featured_image'), 'campus-news');
        }

        // Handle image removal
        if ($request->input('remove_featured_image') == '1') {
            if ($campusNews->featured_image) {
                Storage::delete($campusNews->featured_image);
            }
            $validated['featured_image'] = null;
        }

        // Process tags
        if ($request->filled('tags')) {
            $validated['tags'] = $request->tags;
        }

        $campusNews->update($validated);

        return redirect()
            ->route('admin.campus-news.index')
            ->with('success', 'Campus news updated successfully.');
    }

    /**
     * Remove the specified news item from storage.
     */
    public function destroy(CampusNews $campusNews)
    {
        // Delete associated images
        if ($campusNews->featured_image) {
            Storage::delete($campusNews->featured_image);
        }

        $campusNews->delete();

        return redirect()
            ->route('admin.campus-news.index')
            ->with('success', 'Campus news deleted successfully.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(CampusNews $campusNews)
    {
        $campusNews->update([
            'is_featured' => !$campusNews->is_featured
        ]);

        return back()->with('success', 'Featured status updated successfully.');
    }

    /**
     * Update the status of the specified news item.
     */
    public function updateStatus(Request $request, CampusNews $campusNews)
    {
        $request->validate([
            'status' => 'required|in:published,archived'
        ]);

        $campusNews->update([
            'status' => $request->status
        ]);

        $statusText = ucfirst($request->status);
        return back()->with('success', "News {$statusText} successfully.");
    }

    /**
     * Upload image to storage
     */
    private function uploadImage($image, $directory)
    {
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        return $image->storeAs($directory, $filename, 'public');
    }
}
