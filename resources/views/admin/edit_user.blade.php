@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Nutzer bearbeiten</h1>
    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="max-w-md bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-gray-700">ID</label>
            <input type="text" value="{{ $user->id }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input type="text" value="{{ $user->name }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">E-Mail</label>
            <input type="text" value="{{ $user->email }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Rolle</label>
            <select name="useroll" class="w-full border rounded px-3 py-2">
                <option value="user" @if($user->useroll === 'user') selected @endif>User</option>
                <option value="admin" @if($user->useroll === 'admin') selected @endif>Admin</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-900 text-white px-4 py-2 rounded hover:bg-yellow-400 hover:text-blue-900">Speichern</button>
    </form>
</div>
@endsection
