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
        <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
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
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="text-3xl">✅</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-green-800">{{ $users->whereNotNull('email_verified_at')->count() }}</div>
                        <div class="text-sm text-gray-600">E-Mail bestätigt</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="text-3xl">❌</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-red-800">{{ $users->whereNull('email_verified_at')->count() }}</div>
                        <div class="text-sm text-gray-600">E-Mail nicht bestätigt</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="text-3xl">📧</div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold text-green-800">{{ $users->where('email_consent', true)->count() }}</div>
                        <div class="text-sm text-gray-600">E-Mail-Zustimmung</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Benutzertabelle -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-blue-800 text-white">
                <h2 class="text-xl font-semibold">Benutzerverwaltung</h2>
            </div>
            
            <!-- Desktop Tabelle (nur auf großen Bildschirmen) -->
            <div id="desktop-table" class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rolle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Online</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-Mail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-Mail Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors" id="user-row-{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $user->id }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($user->useroll === 'admin')
                                    <span class="text-2xl" title="Administrator">👑</span>
                                @else
                                    <span class="text-2xl" title="Benutzer">🎓</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($user->is_online ?? false)
                                    <span class="text-2xl" title="🟢 Online (letzte Session: {{ $user->updated_at->diffForHumans() }}, letzte Lern-Aktivität: {{ $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date)->diffForHumans() : 'Nie' }})">🟢</span>
                                @else
                                    <span class="text-2xl" title="🔴 Offline (letzte Session: {{ $user->updated_at->diffForHumans() }}, letzte Lern-Aktivität: {{ $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date)->diffForHumans() : 'Nie' }})">🔴</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Bestätigt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ❌ Nicht bestätigt
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="toggleUserDetails({{ $user->id }})" 
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                            title="Details anzeigen/verbergen">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        Details
                                    </button>
                                    
                                    <a href="{{ route('admin.users.progress.edit', $user->id) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-yellow-300 text-sm leading-4 font-medium rounded-lg text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                                       style="background-color: #fbbf24 !important; color: #1e3a8a !important; border-color: #f59e0b !important; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1) !important;"
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
                                                style="background-color: #ef4444 !important; color: white !important; border-color: #dc2626 !important; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4), 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1) !important;"
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
                        
                        <!-- Aufklappbare Details -->
                        <tr id="user-details-{{ $user->id }}" class="hidden bg-gray-50">
                            <td colspan="7" class="px-6 py-4">
                                <div class="bg-white rounded-lg p-6 shadow-sm border">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Benutzerdetails</h3>
                                    
                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                                <input type="text" name="name" value="{{ $user->name }}" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                                                <input type="email" name="email" value="{{ $user->email }}" 
                                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Rolle</label>
                                                <select name="useroll" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                    <option value="user" @if($user->useroll === 'user') selected @endif>🎓 Benutzer</option>
                                                    <option value="admin" @if($user->useroll === 'admin') selected @endif>👑 Administrator</option>
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Registriert am</label>
                                                <div class="px-3 py-2 bg-gray-100 rounded-lg text-sm text-gray-700">
                                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Letzte Aktivität</label>
                                                <div class="px-3 py-2 bg-gray-100 rounded-lg text-sm text-gray-700">
                                                    {{ $user->updated_at->format('d.m.Y H:i') }}
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail Status</label>
                                                <div class="px-3 py-2 bg-gray-100 rounded-lg text-sm">
                                                    @if($user->email_verified_at)
                                                        <span class="text-green-700">✅ Bestätigt am {{ $user->email_verified_at->format('d.m.Y H:i') }}</span>
                                                    @else
                                                        <span class="text-red-700">❌ Nicht bestätigt</span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail-Zustimmung</label>
                                                <div class="px-3 py-2 bg-gray-100 rounded-lg text-sm">
                                                    @if($user->email_consent)
                                                        <span class="text-green-700">📧 Zustimmung erteilt
                                                            @if($user->email_consent_at)
                                                                am {{ $user->email_consent_at->format('d.m.Y H:i') }}
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-gray-700">📧 Keine Zustimmung erteilt</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex justify-end pt-4">
                                            <button type="submit" 
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                                    style="background-color: #2563eb !important; color: white !important; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1) !important;">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Änderungen speichern
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Karten (nur auf kleinen Bildschirmen) -->
            <div id="mobile-cards" class="block md:hidden">
                <div class="p-4 space-y-4">
                    @foreach($users as $user)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <!-- Header mit ID, Rolle, Online-Status -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    ID: {{ $user->id }}
                                </span>
                                @if($user->useroll === 'admin')
                                    <span class="text-2xl" title="Administrator">👑</span>
                                @else
                                    <span class="text-2xl" title="Benutzer">🎓</span>
                                @endif
                                @if($user->is_online ?? false)
                                    <span class="text-2xl" title="🟢 Online (letzte Session: {{ $user->updated_at->diffForHumans() }}, letzte Lern-Aktivität: {{ $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date)->diffForHumans() : 'Nie' }})">🟢</span>
                                @else
                                    <span class="text-2xl" title="🔴 Offline (letzte Session: {{ $user->updated_at->diffForHumans() }}, letzte Lern-Aktivität: {{ $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date)->diffForHumans() : 'Nie' }})">🔴</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Name und E-Mail -->
                        <div class="mb-4">
                            <div class="text-lg font-semibold text-gray-900 mb-1">{{ $user->name }}</div>
                            <div class="text-sm text-gray-600 mb-2">{{ $user->email }}</div>
                            <div class="flex flex-wrap gap-2">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ E-Mail bestätigt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ❌ E-Mail nicht bestätigt
                                    </span>
                                @endif
                                
                                @if($user->email_consent)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        📧 Zustimmung erteilt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        📧 Keine Zustimmung
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Aufklappbare Details -->
                        <div id="mobile-details-{{ $user->id }}" class="hidden mb-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm border">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">Details</h4>
                                
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" name="name" value="{{ $user->name }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                                        <input type="email" name="email" value="{{ $user->email }}" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Rolle</label>
                                        <select name="useroll" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="user" @if($user->useroll === 'user') selected @endif>🎓 Benutzer</option>
                                            <option value="admin" @if($user->useroll === 'admin') selected @endif>👑 Administrator</option>
                                        </select>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-3 text-sm">
                                        <div>
                                            <div class="text-gray-600">Registriert:</div>
                                            <div class="font-medium">{{ $user->created_at->format('d.m.Y') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600">Letzte Aktivität:</div>
                                            <div class="font-medium">{{ $user->updated_at->format('d.m.Y') }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm">
                                        <div class="text-gray-600 mb-1">E-Mail-Zustimmung:</div>
                                        <div class="font-medium">
                                            @if($user->email_consent)
                                                <span class="text-green-700">📧 Zustimmung erteilt
                                                    @if($user->email_consent_at)
                                                        am {{ $user->email_consent_at->format('d.m.Y H:i') }}
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-gray-700">📧 Keine Zustimmung erteilt</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="pt-3">
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                                style="background-color: #2563eb !important; color: white !important; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1) !important;">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Änderungen speichern
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Aktionen -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <button onclick="toggleMobileDetails({{ $user->id }})" 
                                    class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Details
                            </button>
                            
                            <a href="{{ route('admin.users.progress.edit', $user->id) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-yellow-300 text-sm font-medium rounded-lg text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors"
                               style="background-color: #fbbf24 !important; color: #1e3a8a !important; border-color: #f59e0b !important; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1) !important;">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Fortschritt
                            </a>
                            
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex items-center justify-center px-3 py-2 border border-red-300 text-sm font-medium rounded-lg text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                        style="background-color: #ef4444 !important; color: white !important; border-color: #dc2626 !important; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4), 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1) !important;"
                                        onclick="return confirm('Benutzer {{ $user->name }} wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
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

    <script>
        function toggleUserDetails(userId) {
            const detailsRow = document.getElementById('user-details-' + userId);
            const button = event.target.closest('button');
            const icon = button.querySelector('svg path');
            
            if (detailsRow.classList.contains('hidden')) {
                // Details anzeigen
                detailsRow.classList.remove('hidden');
                icon.setAttribute('d', 'M5 15l7-7 7 7'); // Pfeil nach oben
                button.innerHTML = button.innerHTML.replace('Details', 'Verbergen');
            } else {
                // Details verbergen
                detailsRow.classList.add('hidden');
                icon.setAttribute('d', 'M19 9l-7 7-7-7'); // Pfeil nach unten
                button.innerHTML = button.innerHTML.replace('Verbergen', 'Details');
            }
        }
        
        function toggleMobileDetails(userId) {
            const detailsDiv = document.getElementById('mobile-details-' + userId);
            const button = event.target.closest('button');
            const icon = button.querySelector('svg path');
            
            if (detailsDiv.classList.contains('hidden')) {
                // Details anzeigen
                detailsDiv.classList.remove('hidden');
                icon.setAttribute('d', 'M5 15l7-7 7 7'); // Pfeil nach oben
                button.innerHTML = button.innerHTML.replace('Details', 'Verbergen');
            } else {
                // Details verbergen
                detailsDiv.classList.add('hidden');
                icon.setAttribute('d', 'M19 9l-7 7-7-7'); // Pfeil nach unten
                button.innerHTML = button.innerHTML.replace('Verbergen', 'Details');
            }
        }
    </script>

@endsection
