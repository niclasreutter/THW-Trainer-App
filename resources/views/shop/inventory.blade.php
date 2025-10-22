@extends('layouts.app')

@section('title', 'Mein Inventar')
@section('description', 'Verwalte deine gekauften Cosmetics und aktiviere sie für das Leaderboard')

@section('content')
    <div class="py-6 md:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-blue-800 mb-2">🎒 Mein Inventar</h1>
                        <p class="text-base text-gray-700">Verwalte deine Cosmetics</p>
                    </div>
                    <div class="text-left md:text-right">
                        <a href="{{ route('shop.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 md:px-6 py-2 rounded-lg font-semibold transition shadow-md inline-block">
                            🛍️ Zurück zum Shop
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-lg shadow-sm">
                    <p class="font-bold">✅ Erfolg!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded-lg shadow-sm">
                    <p class="font-bold">❌ Fehler!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Aktive Cosmetics -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-blue-800 mb-4">🌟 Aktuell Aktiv</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border-2 border-blue-200">
                        <h3 class="font-semibold text-blue-900 mb-2">Namensfarbe</h3>
                        @if($user->activeNameColor)
                            <div class="font-bold text-lg mb-2" style="{{ $user->activeNameColor->data['css'] ?? '' }}">
                                {{ $user->activeNameColor->name }}
                            </div>
                            <div class="text-sm text-gray-600">{{ $user->activeNameColor->description }}</div>
                        @else
                            <div class="text-gray-500 italic">Keine aktiv</div>
                        @endif
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border-2 border-green-200">
                        <h3 class="font-semibold text-green-900 mb-2">Effekt</h3>
                        @if($user->activeEffect)
                            <div class="font-bold text-lg mb-2 text-gray-800">{{ $user->activeEffect->name }}</div>
                            <div class="text-sm text-gray-600">{{ $user->activeEffect->description }}</div>
                        @else
                            <div class="text-gray-500 italic">Kein Effekt aktiv</div>
                        @endif
                    </div>

                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border-2 border-yellow-200">
                        <h3 class="font-semibold text-yellow-900 mb-2">Emoji</h3>
                        @if($user->activeEmoji)
                            <div class="text-3xl mb-2">{{ $user->activeEmoji->data['emoji'] ?? '' }}</div>
                            <div class="font-bold text-gray-800">{{ $user->activeEmoji->name }}</div>
                        @else
                            <div class="text-gray-500 italic">Kein Emoji aktiv</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Namensfarben -->
            @if($nameColors->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-blue-800 mb-4 flex items-center">
                        <span class="text-2xl md:text-3xl mr-2 md:mr-3">✨</span>
                        Meine Namensfarben
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($nameColors as $item)
                            <div class="border-2 rounded-lg p-4 {{ $user->active_name_color_id == $item->id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-blue-400' }} transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-base md:text-lg" style="{{ $item->data['css'] ?? '' }}">{{ $item->name }}</h3>
                                    @if($user->active_name_color_id == $item->id)
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">✓ Aktiv</span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-4">{{ $item->description }}</p>
                                
                                <!-- Vorschau -->
                                <div class="bg-gray-50 rounded-lg p-3 mb-4 border border-gray-200">
                                    <div class="text-xs text-gray-500 mb-1">Vorschau:</div>
                                    <div class="font-bold text-center" style="{{ $item->data['css'] ?? '' }}">{{ $user->name }}</div>
                                </div>
                                
                                <form action="{{ route('shop.equip', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="{{ $user->active_name_color_id == $item->id ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white px-3 md:px-4 py-2 rounded-lg font-semibold text-xs md:text-sm transition w-full shadow-md">
                                        {{ $user->active_name_color_id == $item->id ? 'Deaktivieren' : 'Aktivieren' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Effekte -->
            @if($effects->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-blue-800 mb-4 flex items-center">
                        <span class="text-2xl md:text-3xl mr-2 md:mr-3">🌟</span>
                        Meine Effekte
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($effects as $item)
                            <div class="border-2 rounded-lg p-4 {{ $user->active_effect_id == $item->id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-blue-400' }} transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-base md:text-lg text-gray-800">{{ $item->name }}</h3>
                                    @if($user->active_effect_id == $item->id)
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">✓ Aktiv</span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-4">{{ $item->description }}</p>
                                
                                <form action="{{ route('shop.equip', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="{{ $user->active_effect_id == $item->id ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white px-3 md:px-4 py-2 rounded-lg font-semibold text-xs md:text-sm transition w-full shadow-md">
                                        {{ $user->active_effect_id == $item->id ? 'Deaktivieren' : 'Aktivieren' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Emojis -->
            @if($emojis->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-blue-800 mb-4 flex items-center">
                        <span class="text-2xl md:text-3xl mr-2 md:mr-3">🎪</span>
                        Meine Emojis
                    </h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                        @foreach($emojis as $item)
                            <div class="border-2 rounded-lg p-3 md:p-4 {{ $user->active_emoji_id == $item->id ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-blue-400' }} transition-all">
                                <div class="text-center mb-2 md:mb-3">
                                    <div class="text-4xl md:text-5xl mb-1 md:mb-2">{{ $item->data['emoji'] ?? '❓' }}</div>
                                    <h3 class="font-bold text-sm md:text-base text-gray-800">{{ $item->name }}</h3>
                                    @if($user->active_emoji_id == $item->id)
                                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full mt-2 inline-block">✓ Aktiv</span>
                                    @endif
                                </div>
                                
                                <form action="{{ route('shop.equip', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="{{ $user->active_emoji_id == $item->id ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white px-3 py-2 rounded-lg font-semibold text-xs transition w-full shadow-md">
                                        {{ $user->active_emoji_id == $item->id ? 'Deaktivieren' : 'Aktivieren' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if($nameColors->count() == 0 && $effects->count() == 0 && $emojis->count() == 0)
                <div class="bg-white rounded-lg shadow-md p-8 md:p-12 text-center">
                    <div class="text-5xl md:text-6xl mb-4">🎒</div>
                    <h2 class="text-xl md:text-2xl font-bold text-blue-800 mb-2">Dein Inventar ist leer</h2>
                    <p class="text-gray-600 mb-6">Besuche den Shop und kaufe deine ersten Cosmetics!</p>
                    <a href="{{ route('shop.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 md:px-8 py-2 md:py-3 rounded-lg font-semibold transition inline-block shadow-md">
                        🛍️ Zum Shop
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
