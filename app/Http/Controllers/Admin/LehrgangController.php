<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lehrgang;
use App\Models\LehrgangQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LehrgangController extends \App\Http\Controllers\Controller
{
    /**
     * Zeige alle Lehrgänge
     */
    public function index()
    {
        $lehrgaenge = Lehrgang::withCount(['questions', 'users'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.lehrgaenge.index', [
            'lehrgaenge' => $lehrgaenge,
        ]);
    }

    /**
     * Zeige Create-Form
     */
    public function create()
    {
        return view('admin.lehrgaenge.create');
    }

    /**
     * Speichere neuen Lehrgang
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lehrgang' => ['required', 'string', 'max:255', 'unique:lehrgaenge,lehrgang'],
            'beschreibung' => ['required', 'string', 'max:1000'],
        ]);

        $validated['slug'] = Str::slug($validated['lehrgang']);

        $lehrgang = Lehrgang::create($validated);

        return redirect()
            ->route('admin.lehrgaenge.show', $lehrgang->id)
            ->with('success', "Lehrgang '{$lehrgang->lehrgang}' erstellt!");
    }

    /**
     * Zeige Lehrgang mit Import/Edit-Optionen
     */
    public function show($lehrgaenge)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgaenge);
        $lehrgang->load('questions');
        
        // Gruppiere Fragen nach Lernabschnitt
        $questionsBySection = $lehrgang->questions
            ->groupBy('lernabschnitt')
            ->sortKeys();

        return view('admin.lehrgaenge.show', [
            'lehrgang' => $lehrgang,
            'questionsBySection' => $questionsBySection,
        ]);
    }

    /**
     * Zeige Edit-Form
     */
    public function edit($lehrgaenge)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgaenge);
        return view('admin.lehrgaenge.edit', [
            'lehrgang' => $lehrgang,
        ]);
    }

    /**
     * Update Lehrgang
     */
    public function update(Request $request, $lehrgaenge)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgaenge);
        $validated = $request->validate([
            'lehrgang' => ['required', 'string', 'max:255', 'unique:lehrgaenge,lehrgang,' . $lehrgang->id],
            'beschreibung' => ['required', 'string', 'max:1000'],
        ]);

        $validated['slug'] = Str::slug($validated['lehrgang']);

        $lehrgang->update($validated);

        return redirect()
            ->route('admin.lehrgaenge.show', $lehrgang->id)
            ->with('success', 'Lehrgang aktualisiert!');
    }

    /**
     * Lösche Lehrgang
     */
    public function destroy($lehrgaenge)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgaenge);
        $name = $lehrgang->lehrgang;
        $lehrgang->delete();

        return redirect()
            ->route('admin.lehrgaenge.index')
            ->with('success', "Lehrgang '{$name}' gelöscht!");
    }

    /**
     * Import CSV-Datei
     */
    public function importCSV(Request $request, $lehrgang)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgang);
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // Max 5MB
        ]);

        try {
            $file = $request->file('csv_file');
            $tempPath = $file->getPathname();

            // Lese CSV
            $rows = $this->parseCSV($tempPath);
            
            if (empty($rows)) {
                throw new \Exception('CSV-Datei ist leer oder hat kein gültiges Format');
            }

            // Validiere und importiere
            $imported = 0;
            $errors = [];

            foreach ($rows as $rowNum => $row) {
                try {
                    // Validiere Row
                    if (empty($row['lernabschnitt']) || empty($row['frage']) || empty($row['loesung'])) {
                        throw new \Exception("Zeile {$rowNum}: Erforderliche Felder fehlen");
                    }

                    // Parse solution (kann komma-getrennt sein: A,B)
                    $loesung = trim($row['loesung']);
                    if (!$this->validateSolution($loesung)) {
                        throw new \Exception("Zeile {$rowNum}: Ungültige Lösung '{$loesung}'. Muss A, B, C, oder komma-getrennt sein");
                    }

                    // Erstelle Frage
                    LehrgangQuestion::create([
                        'lehrgang_id' => $lehrgang->id,
                        'lernabschnitt' => (int) $row['lernabschnitt'],
                        'nummer' => (int) ($row['nummer'] ?? 0),
                        'frage' => trim($row['frage']),
                        'antwort_a' => trim($row['antwort_a'] ?? ''),
                        'antwort_b' => trim($row['antwort_b'] ?? ''),
                        'antwort_c' => trim($row['antwort_c'] ?? ''),
                        'loesung' => $loesung,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            $message = "{$imported} Fragen importiert";
            if (!empty($errors)) {
                $message .= ". " . count($errors) . " Fehler: " . implode("; ", array_slice($errors, 0, 3));
            }

            return redirect()
                ->route('admin.lehrgaenge.show', $lehrgang->id)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.lehrgaenge.show', $lehrgang->id)
                ->with('error', 'Import-Fehler: ' . $e->getMessage());
        }
    }

    /**
     * Parse CSV-Datei
     */
    private function parseCSV($filePath)
    {
        $rows = [];
        $headers = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Lese erste Zeile um Trennzeichen zu detecten
            $firstLine = fgets($handle);
            rewind($handle);
            
            // Auto-detect Trennzeichen (Tab oder Komma)
            $delimiter = (strpos($firstLine, "\t") !== false) ? "\t" : ",";
            
            // Erste Zeile = Headers
            if ($header = fgetcsv($handle, 1000, $delimiter)) {
                $headers = array_map('trim', $header);
            }

            // Daten-Zeilen
            $rowNum = 2;
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $row = [];
                foreach ($headers as $i => $header) {
                    $row[strtolower(trim($header))] = trim($data[$i] ?? '');
                }
                
                if (!empty(trim($row['frage'] ?? ''))) {
                    $rows[$rowNum] = $row;
                }
                $rowNum++;
            }
            fclose($handle);
        }

        return $rows;
    }

    /**
     * Validiere Lösung
     */
    private function validateSolution($solution)
    {
        $validLetters = ['A', 'B', 'C'];
        $parts = array_map('trim', explode(',', strtoupper($solution)));

        foreach ($parts as $part) {
            if (!in_array($part, $validLetters)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lösche einzelne Frage
     */
    public function deleteQuestion(LehrgangQuestion $question)
    {
        $lehrgang = $question->lehrgang;
        $question->delete();

        return redirect()
            ->route('admin.lehrgaenge.show', $lehrgang->id)
            ->with('success', 'Frage gelöscht!');
    }

    /**
     * Bearbeite einzelne Frage (AJAX/Inline)
     */
    public function editQuestion($lehrgang, $question)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgang);
        $question = LehrgangQuestion::findOrFail($question);

        // Sicherstellen, dass die Frage zum Lehrgang gehört
        if ($question->lehrgang_id !== $lehrgang->id) {
            abort(404);
        }

        return view('admin.lehrgaenge.edit-question', compact('lehrgang', 'question'));
    }

    /**
     * Update einzelne Frage (PATCH - für inline Bearbeitung)
     */
    public function updateQuestion(Request $request, $lehrgang, $question)
    {
        $lehrgang = Lehrgang::findOrFail($lehrgang);
        $question = LehrgangQuestion::findOrFail($question);

        // Sicherstellen, dass die Frage zum Lehrgang gehört
        if ($question->lehrgang_id !== $lehrgang->id) {
            abort(404);
        }

        $validated = $request->validate([
            'lernabschnitt' => ['required', 'integer', 'min:1'],
            'nummer' => ['required', 'integer', 'min:1'],
            'frage' => ['required', 'string', 'max:1000'],
            'antwort_a' => ['required', 'string', 'max:500'],
            'antwort_b' => ['required', 'string', 'max:500'],
            'antwort_c' => ['required', 'string', 'max:500'],
            'loesung' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!$this->validateSolution($value)) {
                    $fail('Lösung muss A, B, C oder komma-getrennt (z.B. A,B) sein.');
                }
            }],
        ]);

        $question->update($validated);

        // Für AJAX-Anfragen nur JSON zurückgeben
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Frage aktualisiert!']);
        }

        return redirect()
            ->route('admin.lehrgaenge.show', $lehrgang->id)
            ->with('success', 'Frage aktualisiert!');
    }
}

