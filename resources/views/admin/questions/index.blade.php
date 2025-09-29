@extends('layouts.app')
@section('title', 'Fragenverwaltung - THW Trainer Admin')

@section('content')
    <div class="max-w-7xl mx-auto p-4 lg:p-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 lg:mb-8 gap-4">
            <h1 class="text-2xl lg:text-3xl font-bold text-blue-800">📝 Fragenverwaltung</h1>
            <div class="flex flex-col sm:flex-row gap-2 lg:gap-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm">
                    👥 Nutzerverwaltung
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                    ← Zurück zum Dashboard
                </a>
            </div>
        </div>
        
        <!-- Status Messages -->
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
        <div class="mb-6 lg:mb-8 grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="text-2xl lg:text-3xl">📝</div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-lg lg:text-2xl font-bold text-blue-800">{{ $questions->count() }}</div>
                        <div class="text-xs lg:text-sm text-gray-600">Gesamt Fragen</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="text-2xl lg:text-3xl">📚</div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-lg lg:text-2xl font-bold text-blue-800">{{ $questions->pluck('lernabschnitt')->unique()->count() }}</div>
                        <div class="text-xs lg:text-sm text-gray-600">Lernabschnitte</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="text-2xl lg:text-3xl">🏆</div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-lg lg:text-2xl font-bold text-blue-800">{{ $questions->where('lernabschnitt', $questions->groupBy('lernabschnitt')->keys()->first())->count() }}</div>
                        <div class="text-xs lg:text-sm text-gray-600">Größter Abschnitt</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6">
                <div class="flex items-center">
                    <div class="text-2xl lg:text-3xl">🎯</div>
                    <div class="ml-3 lg:ml-4">
                        <div class="text-lg lg:text-2xl font-bold text-blue-800">{{ $questions->max('id') ?? '0' }}</div>
                        <div class="text-xs lg:text-sm text-gray-600">Höchste ID</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Neue Frage hinzufügen -->
        <div class="mb-6 lg:mb-8 bg-white rounded-lg shadow-md p-4 lg:p-6">
            <h2 class="text-lg lg:text-xl font-semibold text-blue-800 mb-4">➕ Neue Frage hinzufügen</h2>
            <form action="{{ route('admin.questions.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lernabschnitt</label>
                        <input type="text" name="lernabschnitt" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                               placeholder="z.B. 1" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nummer</label>
                        <input type="number" name="nummer" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                               placeholder="z.B. 1" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lösung</label>
                        <select name="loesung" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                            <option value="">Lösung wählen</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="A,B">A,B</option>
                            <option value="A,C">A,C</option>
                            <option value="B,C">B,C</option>
                            <option value="A,B,C">A,B,C</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors text-sm">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Hinzufügen
                            </span>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Frage</label>
                    <textarea name="frage" rows="2" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                              placeholder="Fragentext eingeben..." required></textarea>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Antwort A</label>
                        <textarea name="antwort_a" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                  placeholder="Antwort A eingeben..." required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Antwort B</label>
                        <textarea name="antwort_b" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                  placeholder="Antwort B eingeben..." required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Antwort C</label>
                        <textarea name="antwort_c" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                  placeholder="Antwort C eingeben..." required></textarea>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- View Toggle -->
        <div class="mb-4 flex justify-between items-center">
            <h2 class="text-lg lg:text-xl font-semibold text-blue-800">Vorhandene Fragen bearbeiten</h2>
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button id="cardViewBtn" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm transition-all lg:hidden">
                    📱 Karten
                </button>
                <button id="tableViewBtn" class="px-3 py-1 text-sm font-medium text-gray-700 transition-all hidden lg:block lg:bg-white lg:rounded-md lg:shadow-sm">
                    📊 Tabelle
                </button>
            </div>
        </div>
        
        <!-- Mobile Card View -->
        <div id="cardView" class="space-y-6 lg:hidden">
            @foreach($questions as $q)
            <div class="bg-white rounded-lg shadow-md p-4 lg:p-6 hover:shadow-lg transition-shadow duration-200">
                <form action="{{ route('admin.questions.update', $q) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex flex-col space-y-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 w-fit">
                                ID: {{ $q->id }}
                            </span>
                            <div class="flex space-x-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Lernabschnitt</label>
                                    <input type="text" name="lernabschnitt" value="{{ $q->lernabschnitt }}" 
                                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Nummer</label>
                                    <input type="number" name="nummer" value="{{ $q->nummer }}" 
                                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <!-- Question -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frage</label>
                        <textarea name="frage" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ $q->frage }}</textarea>
                    </div>
                    
                    <!-- Answers -->
                    <div class="space-y-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Antwort A</label>
                            <textarea name="antwort_a" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ $q->antwort_a }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Antwort B</label>
                            <textarea name="antwort_b" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ $q->antwort_b }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Antwort C</label>
                            <textarea name="antwort_c" rows="2" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ $q->antwort_c }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Solution and Actions -->
                    <div class="pt-4 border-t border-gray-200 space-y-4">
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium text-gray-700">Lösung</label>
                            <select name="loesung" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="A" @if($q->loesung === 'A') selected @endif>A</option>
                                <option value="B" @if($q->loesung === 'B') selected @endif>B</option>
                                <option value="C" @if($q->loesung === 'C') selected @endif>C</option>
                                <option value="A,B" @if($q->loesung === 'A,B') selected @endif>A,B</option>
                                <option value="A,C" @if($q->loesung === 'A,C') selected @endif>A,C</option>
                                <option value="B,C" @if($q->loesung === 'B,C') selected @endif>B,C</option>
                                <option value="A,B,C" @if($q->loesung === 'A,B,C') selected @endif>A,B,C</option>
                            </select>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-6">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm"
                                    style="background-color: #16a34a; border: none; margin-right: 12px;"
                                    onmouseover="this.style.backgroundColor='#15803d'"
                                    onmouseout="this.style.backgroundColor='#16a34a'"
                                    title="Änderungen speichern">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Speichern
                            </button>
                </form>
                            <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm"
                                        style="background-color: #dc2626; border: none; margin-left: 0;"
                                        onmouseover="this.style.backgroundColor='#b91c1c'"
                                        onmouseout="this.style.backgroundColor='#dc2626'"
                                        title="Frage löschen"
                                        onclick="return confirm('Frage {{ $q->id }} wirklich löschen?')">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Löschen
                                </button>
                            </form>
                        </div>
                    </div>
            </div>
            @endforeach
        </div>
        
        <!-- Desktop Table View -->
        <div id="tableView" class="hidden bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-blue-800 text-white">
                <h3 class="text-xl font-semibold">Fragenliste</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LA</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nr</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frage</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antwort A</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antwort B</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Antwort C</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lösung</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($questions as $q)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <form action="{{ route('admin.questions.update', $q) }}" method="POST" class="contents">
                                @csrf
                                @method('PATCH')
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $q->id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="text" name="lernabschnitt" value="{{ $q->lernabschnitt }}" 
                                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="number" name="nummer" value="{{ $q->nummer }}" 
                                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500" />
                                </td>
                                <td class="px-4 py-3">
                                    <textarea name="frage" rows="2" 
                                              class="w-full min-w-[200px] px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $q->frage }}</textarea>
                                </td>
                                <td class="px-4 py-3">
                                    <textarea name="antwort_a" rows="2" 
                                              class="w-full min-w-[150px] px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $q->antwort_a }}</textarea>
                                </td>
                                <td class="px-4 py-3">
                                    <textarea name="antwort_b" rows="2" 
                                              class="w-full min-w-[150px] px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $q->antwort_b }}</textarea>
                                </td>
                                <td class="px-4 py-3">
                                    <textarea name="antwort_c" rows="2" 
                                              class="w-full min-w-[150px] px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">{{ $q->antwort_c }}</textarea>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <select name="loesung" class="w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <option value="A" @if($q->loesung === 'A') selected @endif>A</option>
                                        <option value="B" @if($q->loesung === 'B') selected @endif>B</option>
                                        <option value="C" @if($q->loesung === 'C') selected @endif>C</option>
                                        <option value="A,B" @if($q->loesung === 'A,B') selected @endif>A,B</option>
                                        <option value="A,C" @if($q->loesung === 'A,C') selected @endif>A,C</option>
                                        <option value="B,C" @if($q->loesung === 'B,C') selected @endif>B,C</option>
                                        <option value="A,B,C" @if($q->loesung === 'A,B,C') selected @endif>A,B,C</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                                                title="Änderungen speichern">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                            </form>
                                        
                                        <form action="{{ route('admin.questions.destroy', $q) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                                                    title="Frage löschen"
                                                    onclick="return confirm('Frage {{ $q->id }} wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden!')">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
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
        
        <!-- Footer Info -->
        <div class="mt-6 lg:mt-8 text-center text-sm text-gray-600">
            Gesamt: {{ $questions->count() }} Fragen in {{ $questions->pluck('lernabschnitt')->unique()->count() }} Lernabschnitten
        </div>
    </div>

    <!-- JavaScript für View Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cardView = document.getElementById('cardView');
            const tableView = document.getElementById('tableView');
            const cardViewBtn = document.getElementById('cardViewBtn');
            const tableViewBtn = document.getElementById('tableViewBtn');
            
            // Default: Show appropriate view based on screen size
            function updateView() {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    // Desktop: Show table by default
                    cardView.classList.add('hidden');
                    tableView.classList.remove('hidden');
                    if (tableViewBtn) {
                        tableViewBtn.classList.add('bg-white', 'rounded-md', 'shadow-sm');
                        tableViewBtn.classList.remove('text-gray-500');
                    }
                    if (cardViewBtn) {
                        cardViewBtn.classList.remove('bg-white', 'rounded-md', 'shadow-sm');
                        cardViewBtn.classList.add('text-gray-500');
                    }
                } else {
                    // Mobile: Show cards by default
                    cardView.classList.remove('hidden');
                    tableView.classList.add('hidden');
                    if (cardViewBtn) {
                        cardViewBtn.classList.add('bg-white', 'rounded-md', 'shadow-sm');
                        cardViewBtn.classList.remove('text-gray-500');
                    }
                    if (tableViewBtn) {
                        tableViewBtn.classList.remove('bg-white', 'rounded-md', 'shadow-sm');
                        tableViewBtn.classList.add('text-gray-500');
                    }
                }
            }
            
            // Toggle functions
            if (cardViewBtn) {
                cardViewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Force show cards, hide table
                    cardView.classList.remove('hidden');
                    tableView.classList.add('hidden');
                    cardViewBtn.classList.add('bg-white', 'rounded-md', 'shadow-sm');
                    cardViewBtn.classList.remove('text-gray-500');
                    if (tableViewBtn) {
                        tableViewBtn.classList.remove('bg-white', 'rounded-md', 'shadow-sm');
                        tableViewBtn.classList.add('text-gray-500');
                    }
                });
            }
            
            if (tableViewBtn) {
                tableViewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Force show table, hide cards
                    cardView.classList.add('hidden');
                    tableView.classList.remove('hidden');
                    tableViewBtn.classList.add('bg-white', 'rounded-md', 'shadow-sm');
                    tableViewBtn.classList.remove('text-gray-500');
                    if (cardViewBtn) {
                        cardViewBtn.classList.remove('bg-white', 'rounded-md', 'shadow-sm');
                        cardViewBtn.classList.add('text-gray-500');
                    }
                });
            }
            
            // Initial setup and resize handler
            updateView();
            window.addEventListener('resize', updateView);
        });
    </script>
@endsection