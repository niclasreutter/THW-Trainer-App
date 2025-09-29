@extends('layouts.app')
@section('title', 'Nutzerverwaltung - THW Trainer Admin')

@section('content')
    <div class="max-w-7xl mx-auto p-6">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-blue-800">👥 Nutzerverwaltung</h1>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                ← Zurück zum Dashboard
            </a>
        </div>
        
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        
        <!-- Statistiken -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="text-3xl">👥</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">{{ $users->count() }}</div>
                        <div class="text-sm text-gray-600">Gesamt Benutzer</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="text-3xl">👑</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">{{ $users->where('useroll', 'admin')->count() }}</div>
                        <div class="text-sm text-gray-600">Administratoren</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="text-3xl">🎓</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-blue-800">{{ $users->where('useroll', 'user')->count() }}</div>
                        <div class="text-sm text-gray-600">Benutzer</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Benutzertabelle -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-blue-800 text-white">
                <h2 class="text-xl font-semibold">Benutzer bearbeiten</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-Mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rolle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="contents">
                                @csrf
                                @method('PUT')
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $user->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="name" value="{{ $user->name }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="email" name="email" value="{{ $user->email }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select name="useroll" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="user" @if($user->useroll === 'user') selected @endif>🎓 Benutzer</option>
                                        <option value="admin" @if($user->useroll === 'admin') selected @endif>👑 Administrator</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                                style="display: inline-flex !important; visibility: visible !important; opacity: 1 !important; background-color: #2563eb !important; color: white !important; padding: 8px 12px !important; border-radius: 8px !important; font-weight: 500 !important; z-index: 10 !important;"
                                                title="Änderungen speichern">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Speichern
                                        </button>
                                    </form>
                                    
                                        <a href="{{ route('admin.users.progress.edit', $user->id) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-yellow-300 text-sm leading-4 font-medium rounded-lg text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                                           title="Fortschritt bearbeiten">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            Fortschritt
                                        </a>
                                        
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-lg text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                                    title="Benutzer löschen"
                                                    onclick="return confirm('Benutzer {{ $user->name }} wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!')">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="mt-8 flex justify-between items-center">
            <a href="{{ route('admin.questions.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                📝 Zur Fragenverwaltung
            </a>
            
            <div class="text-sm text-gray-600">
                Gesamt: {{ $users->count() }} Benutzer
            </div>
        </div>
    </div>
@endsection
