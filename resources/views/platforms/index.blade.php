@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Available Platforms</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($platforms as $platform)
            <tr>
                <td>{{ $platform->id }}</td>
                <td>{{ $platform->name }}</td>
                <td>{{ $platform->type }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
