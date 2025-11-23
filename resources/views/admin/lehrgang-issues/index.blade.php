@extends('layouts.app')
@section('title', 'Lehrgang Fehlermeldungen - Admin')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">🐛 Lehrgang Fehlermeldungen</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-semibold">← Zurück</a>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-4">
            <div class="text-sm text-blue-600 font-semibold">Gesamt</div>
            <div class="text-2xl font-bold text-blue-900">{{ $totalIssues }}</div>
        </div>
        <div class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4">
            <div class="text-sm text-red-600 font-semibold">Offen</div>
            <div class="text-2xl font-bold text-red-900">{{ $openIssues }}</div>
        </div>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg p-4">
            <div class="text-sm text-yellow-600 font-semibold">In Bearbeitung</div>
            <div class="text-2xl font-bold text-yellow-900">{{ $inReviewIssues }}</div>
        </div>
        <div class="bg-green-50 border-l-4 border-green-500 rounded-r-lg p-4">
            <div class="text-sm text-green-600 font-semibold">Gelöst</div>
            <div class="text-2xl font-bold text-green-900">{{ $resolvedIssues }}</div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'all']) }}" 
               class="px-4 py-2 rounded-lg font-semibold transition {{ $status === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                Alle
            </a>
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'open']) }}" 
               class="px-4 py-2 rounded-lg font-semibold transition {{ $status === 'open' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                Offen
            </a>
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'in_review']) }}" 
               class="px-4 py-2 rounded-lg font-semibold transition {{ $status === 'in_review' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                In Bearbeitung
            </a>
            <a href="{{ route('admin.lehrgang-issues.index', ['status' => 'resolved']) }}" 
               class="px-4 py-2 rounded-lg font-semibold transition {{ $status === 'resolved' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                Gelöst
            </a>
        </div>
    </div>

    <!-- Issues Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($issues->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Frage</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Lehrgang</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Meldungen</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900 max-w-xs line-clamp-2">
                                    {{ Str::limit($issue->lehrgangQuestion->frage, 50) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">ID: {{ $issue->lehrgangQuestion->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-700">{{ $issue->lehrgangQuestion->lehrgang->lehrgang }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ $issue->report_count }}x
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                    @if($issue->status === 'open') bg-red-100 text-red-800
                                    @elseif($issue->status === 'in_review') bg-yellow-100 text-yellow-800
                                    @elseif($issue->status === 'resolved') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.lehrgang-issues.show', $issue) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-semibold">
                                    Anschauen →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $issues->links() }}
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <div class="text-2xl mb-2">✓</div>
                <p>Keine Fehlermeldungen in dieser Kategorie.</p>
            </div>
        @endif
    </div>
</div>
@endsection
