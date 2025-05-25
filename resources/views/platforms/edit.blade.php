@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Edit Post</h1>
    <form action="{{ route('posts.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $post->title }}" required>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control" rows="5" required>{{ $post->content }}</textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ $post->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="platforms" class="form-label">Platforms</label>
            <select name="platforms[]" id="platforms" class="form-select" multiple>
                @foreach ($platforms as $platform)
                <option value="{{ $platform->id }}" {{ $post->platforms->contains($platform->id) ? 'selected' : '' }}>
                    {{ $platform->name }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>
@endsection
