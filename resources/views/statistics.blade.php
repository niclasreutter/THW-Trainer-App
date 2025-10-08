@extends('layouts.app')

@section('title', 'Statistiken - THW Trainer')
@section('description', 'Öffentliche Statistiken über alle beantworteten Fragen im THW-Trainer. Sehen Sie, welche Fragen am häufigsten richtig oder falsch beantwortet wurden.')

@section('content')
<style>
    .gradient-blue { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
    .gradient-green { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
    .gradient-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .gradient-yellow { background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%); }
    .card-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .hover-scale { transition: transform 0.2s ease-in-out; }
    .hover-scale:hover { transform: scale(1.02); }
</style>

<div class="max-w-7xl mx-auto p-6">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-blue-800 mb-2">📊 THW-Trainer Statistiken</h1>
        <p class="text-gray-600">Anonyme Statistiken über alle beantworteten Fragen</p>
    </div>

    <!-- Gesamt-Statistiken KPI Karten -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <!-- Gesamt beantwortet -->
        <div class="rounded-xl p-6 text-white hover-scale cursor-pointer"
             style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4), 0 0 20px rgba(37, 99, 235, 0.3), 0 0 40px rgba(37, 99, 235, 0.1); border-radius: 12px;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Gesamt beantwortet</p>
                    <p class="text-3xl font-bold">{{ number_format($totalAnswered) }}</p>
                    <p class="text-blue-100 text-sm">Fragen</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-chart-bar text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Richtig beantwortet -->
        <div class="rounded-xl p-6 text-white hover-scale cursor-pointer"
             style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4), 0 0 20px rgba(34, 197, 94, 0.3), 0 0 40px rgba(34, 197, 94, 0.1); border-radius: 12px;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Richtig beantwortet</p>
                    <p class="text-3xl font-bold">{{ number_format($totalCorrect) }}</p>
                    <p class="text-green-100 text-sm">{{ $successRate }}% Erfolgsrate</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Falsch beantwortet -->
        <div class="rounded-xl p-6 text-white hover-scale cursor-pointer"
             style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4), 0 0 20px rgba(239, 68, 68, 0.3), 0 0 40px rgba(239, 68, 68, 0.1); border-radius: 12px;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Falsch beantwortet</p>
                    <p class="text-3xl font-bold">{{ number_format($totalWrong) }}</p>
                    <p class="text-red-100 text-sm">{{ $errorRate }}% Fehlerrate</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Erfolgsrate -->
        <div class="rounded-xl p-6 text-white hover-scale cursor-pointer"
             style="background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%); color: #1e40af; box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4), 0 0 20px rgba(251, 191, 36, 0.3), 0 0 40px rgba(251, 191, 36, 0.1); border-radius: 12px;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-800 text-sm font-medium">Erfolgsrate</p>
                    <p class="text-3xl font-bold">{{ $successRate }}%</p>
                    <p class="text-blue-800 text-sm">aller Antworten</p>
                </div>
                <div class="bg-blue-800 bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-trophy text-2xl text-blue-800"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Lernabschnitt-Statistiken -->
    @if($sectionStats->isNotEmpty())
    <div class="bg-white rounded-xl p-6 card-shadow mb-12">
        <div class="flex items-center mb-6">
            <i class="fas fa-book text-blue-600 text-2xl mr-3"></i>
            <h2 class="text-2xl font-bold text-gray-800">Statistik nach Lernabschnitten</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($sectionStats as $stat)
                @php
                    $sectionNames = [
                        1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
                        2 => 'Arbeitssicherheit und Gesundheitsschutz', 
                        3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
                        4 => 'Arbeiten mit Leitern',
                        5 => 'Stromerzeugung und Beleuchtung',
                        6 => 'Metall-, Holz- und Steinbearbeitung',
                        7 => 'Bewegen von Lasten',
                        8 => 'Arbeiten am und auf dem Wasser',
                        9 => 'Einsatzgrundlagen',
                        10 => 'Grundlagen der Rettung und Bergung'
                    ];
                    $sectionName = $sectionNames[$stat->lernabschnitt] ?? 'Unbekannt';
                @endphp
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-200">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800 text-sm">{{ $stat->lernabschnitt }}. {{ $sectionName }}</h3>
                        </div>
                        <span class="ml-2 font-bold text-lg {{ $stat->success_rate >= 80 ? 'text-green-600' : ($stat->success_rate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $stat->success_rate }}%
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-600 mb-2">
                        <span>{{ number_format($stat->total_attempts) }} Versuche</span>
                        <div class="flex gap-3">
                            <span class="text-green-600 font-semibold">✓ {{ number_format($stat->correct_count) }}</span>
                            <span class="text-red-600 font-semibold">✗ {{ number_format($stat->wrong_count) }}</span>
                        </div>
                    </div>
                    <!-- Stacked Progress Bar (Grün/Rot Verteilung) -->
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden flex">
                        <div class="h-3 transition-all duration-500" 
                             style="width: {{ $stat->success_rate }}%; background-color: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.6), 0 0 16px rgba(16, 185, 129, 0.3);"
                             title="Richtig: {{ $stat->success_rate }}%"></div>
                        <div class="h-3 transition-all duration-500" 
                             style="width: {{ 100 - $stat->success_rate }}%; background-color: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.6), 0 0 16px rgba(239, 68, 68, 0.3);"
                             title="Falsch: {{ 100 - $stat->success_rate }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top 10 Schwierigste Fragen -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
        <!-- Top 10 Falsch -->
        <div class="bg-white rounded-xl p-6 card-shadow">
            <div class="flex items-center mb-6">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                <h2 class="text-2xl font-bold text-gray-800">Top 10 Schwierigste Fragen</h2>
            </div>
            
            @if($topWrongQuestionsWithDetails->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>Noch nicht genügend Daten verfügbar</p>
                    <p class="text-sm">(mindestens 5 Versuche pro Frage erforderlich)</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($topWrongQuestionsWithDetails as $index => $item)
                        <div class="border-l-4 border-red-500 bg-red-50 rounded-r-lg p-4 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-start space-x-3 flex-1">
                                    <span class="font-bold text-red-600 text-lg">{{ $index + 1 }}.</span>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium">{{ Str::limit($item['question']->frage, 120) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Lernabschnitt {{ $item['question']->lernabschnitt }}</p>
                                    </div>
                                </div>
                                <div class="ml-2 text-right">
                                    <div class="font-bold text-xl text-red-600">{{ $item['error_rate'] }}%</div>
                                    <div class="text-xs text-gray-500">Fehlerrate</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-600 mt-2">
                                <span>{{ number_format($item['total_attempts']) }} Versuche</span>
                                <div class="flex gap-2">
                                    <span class="text-red-600">✗ {{ number_format($item['wrong_count']) }}</span>
                                    <span class="text-green-600">✓ {{ number_format($item['correct_count']) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Top 10 Richtig -->
        <div class="bg-white rounded-xl p-6 card-shadow">
            <div class="flex items-center mb-6">
                <i class="fas fa-star text-green-500 text-2xl mr-3"></i>
                <h2 class="text-2xl font-bold text-gray-800">Top 10 Einfachste Fragen</h2>
            </div>
            
            @if($topCorrectQuestionsWithDetails->isEmpty())
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-2"></i>
                    <p>Noch nicht genügend Daten verfügbar</p>
                    <p class="text-sm">(mindestens 5 Versuche pro Frage erforderlich)</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($topCorrectQuestionsWithDetails as $index => $item)
                        <div class="border-l-4 border-green-500 bg-green-50 rounded-r-lg p-4 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-start space-x-3 flex-1">
                                    <span class="font-bold text-green-600 text-lg">{{ $index + 1 }}.</span>
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800 font-medium">{{ Str::limit($item['question']->frage, 120) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Lernabschnitt {{ $item['question']->lernabschnitt }}</p>
                                    </div>
                                </div>
                                <div class="ml-2 text-right">
                                    <div class="font-bold text-xl text-green-600">{{ $item['success_rate'] }}%</div>
                                    <div class="text-xs text-gray-500">Erfolgsrate</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-600 mt-2">
                                <span>{{ number_format($item['total_attempts']) }} Versuche</span>
                                <div class="flex gap-2">
                                    <span class="text-green-600">✓ {{ number_format($item['correct_count']) }}</span>
                                    <span class="text-red-600">✗ {{ number_format($item['wrong_count']) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-6 mb-6">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 text-xl mr-3 mt-1"></i>
            <div>
                <h3 class="font-bold text-blue-800 mb-2">ℹ️ Über diese Statistiken</h3>
                <p class="text-sm text-blue-700 mb-2">
                    Diese Statistiken basieren auf anonymen Daten aller Nutzer (angemeldet und Gäste). 
                    Es werden keine persönlichen Informationen gespeichert - nur ob eine Frage richtig oder falsch beantwortet wurde.
                </p>
                <p class="text-sm text-blue-700">
                    Fragen in den Top-10-Listen benötigen mindestens 5 Versuche, um aussagekräftige Statistiken zu liefern.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

