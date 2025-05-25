@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->content }}</p>
    <p>Status: {{ $post->status }}</p>
    <p>Platforms: {{ $post->platforms->pluck('name')->join(', ') }}</p>
    <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">Edit</a>
    <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
</div>
@endsection
