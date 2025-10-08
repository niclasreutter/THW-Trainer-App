<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void
	{
		Schema::create('question_statistics', function (Blueprint $table) {
			$table->id();
			$table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
			$table->boolean('is_correct');
			$table->timestamps();
			
			// Index für bessere Performance bei Statistik-Abfragen
			$table->index(['question_id', 'is_correct']);
			$table->index('created_at');
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('question_statistics');
	}
};

