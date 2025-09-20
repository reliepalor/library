@extends('layouts.admin')

@section('title', 'View Campus News')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-lg border-b border-blue-200/40 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $news->title }}</h1>
                        <p class="text-gray-600">View and manage campus news details</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.campus-news.edit', $news) }}"
                       class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit News</span>
                    </a>
                    <a href="{{ route('admin.campus-news.index') }}"
                       class="bg-gray-600 text-white px-6 py-3 rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m0 0l-7 7"/>
                        </svg>
                        <span>Back to News</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Featured Image -->
                @if($news->featured_image)
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg overflow-hidden border border-blue-200/40">
                    <img src="{{ asset('storage/' . $news->featured_image) }}"
                         alt="{{ $news->title }}"
                         class="w-full h-64 md:h-80 object-cover">
                </div>
                @endif

                <!-- Content -->
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-8 border border-blue-200/40">
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($news->content)) !!}
                    </div>
                </div>

                <!-- Tags -->
                @if($news->tags)
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-6 border border-blue-200/40">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $news->tags) as $tag)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- News Details -->
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-6 border border-blue-200/40">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        News Details
                    </h3>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($news->status) {
                                    'published' => 'bg-green-100 text-green-800',
                                    'draft' => 'bg-yellow-100 text-yellow-800',
                                    'archived' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                    {{ ucfirst($news->status) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($news->category) {
                                    'academic' => 'bg-blue-100 text-blue-800',
                                    'events' => 'bg-green-100 text-green-800',
                                    'sports' => 'bg-orange-100 text-orange-800',
                                    'research' => 'bg-purple-100 text-purple-800',
                                    'achievement' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                } }}">
                                    {{ ucfirst($news->category) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Publish Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $news->publish_date->format('M d, Y \a\t h:i A') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Views</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($news->views_count) }} views</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Featured</dt>
                            <dd class="mt-1">
                                @if($news->is_featured)
                                    <span class="inline-flex items-center text-green-600">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Yes
                                    </span>
                                @else
                                    <span class="text-gray-500">No</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $news->created_at->format('M d, Y \a\t h:i A') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $news->updated_at->format('M d, Y \a\t h:i A') }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Excerpt -->
                @if($news->excerpt)
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-6 border border-blue-200/40">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excerpt
                    </h3>
                    <p class="text-gray-700">{{ $news->excerpt }}</p>
                </div>
                @endif

                <!-- SEO Information -->
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-6 border border-blue-200/40">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        SEO Information
                    </h3>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Title</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $news->meta_title ?: $news->title }}</dd>
                        </div>

                        @if($news->meta_description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Meta Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $news->meta_description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white/80 backdrop-blur-lg rounded-xl shadow-lg p-6 border border-blue-200/40">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.campus-news.update-status', $news) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $news->status == 'published' ? 'draft' : 'published' }}">
                            <button type="submit"
                                    class="w-full bg-gradient-to-r {{ $news->status == 'published' ? 'from-yellow-600 to-orange-600 hover:from-yellow-700 hover:to-orange-700' : 'from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700' }} text-white px-4 py-2 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $news->status == 'published' ? 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                                </svg>
                                <span>{{ $news->status == 'published' ? 'Unpublish' : 'Publish' }}</span>
                            </button>
                        </form>

                        <button onclick="confirmDelete({{ $news->id }})"
                                class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white px-4 py-2 rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center space-x-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Delete News</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(newsId) {
    if (confirm('Are you sure you want to delete this news article? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/campus-news/${newsId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
