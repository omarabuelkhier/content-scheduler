@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center">My Platforms</h1>

    @if ($platforms->isEmpty())
    <p class="text-center">No platforms are attached to your posts.</p>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($platforms as $platform)
            <tr>
                <td>{{ $platform->id }}</td>
                <td>{{ $platform->name }}</td>
                <td>{{ $platform->type }}</td>
                <td>
                    <form action="{{ route('platforms.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="platform_id" value="{{ $platform->id }}">
                        <button type="submit" class="btn btn-danger">Detach</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
