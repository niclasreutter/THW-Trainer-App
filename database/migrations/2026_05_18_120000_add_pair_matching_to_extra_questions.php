<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // SQLite stores ENUM as VARCHAR + CHECK constraint. Patch the
            // constraint via writable_schema to allow the new value, both on
            // the canonical table and on the user-submission staging table.
            $oldConstraint = "\"typ\" in ('matching', 'image_name', 'image_select')";
            $newConstraint = "\"typ\" in ('matching', 'image_name', 'image_select', 'pair_matching')";

            foreach (['extra_questions', 'user_extra_question_submissions'] as $table) {
                $row = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (!$row || !str_contains($row->sql, $oldConstraint)) {
                    throw new \RuntimeException(
                        "Cannot patch enum on `{$table}`: expected CHECK constraint not found. " .
                        "Schema may have drifted – inspect with `sqlite3 database.sqlite \".schema {$table}\"`."
                    );
                }
            }

            DB::statement('PRAGMA writable_schema = 1');
            DB::statement(
                "UPDATE sqlite_master
                 SET sql = REPLACE(sql, ?, ?)
                 WHERE type = 'table'
                   AND name IN ('extra_questions', 'user_extra_question_submissions')",
                [$oldConstraint, $newConstraint]
            );
            DB::statement('PRAGMA writable_schema = 0');
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE extra_questions
                 MODIFY COLUMN typ ENUM('matching', 'image_name', 'image_select', 'pair_matching') NOT NULL"
            );
            DB::statement(
                "ALTER TABLE user_extra_question_submissions
                 MODIFY COLUMN typ ENUM('matching', 'image_name', 'image_select', 'pair_matching') NOT NULL"
            );
        }
        // pgsql / others: assume column accepts new value (no strict CHECK).

        Schema::create('extra_pair_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_question_id')->constrained('extra_questions')->cascadeOnDelete();
            $table->string('left_text');
            $table->string('right_text');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['extra_question_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extra_pair_items');

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA writable_schema = 1');
            DB::statement(
                "UPDATE sqlite_master
                 SET sql = REPLACE(sql,
                     '\"typ\" in (''matching'', ''image_name'', ''image_select'', ''pair_matching'')',
                     '\"typ\" in (''matching'', ''image_name'', ''image_select'')'
                 )
                 WHERE type = 'table'
                   AND name IN ('extra_questions', 'user_extra_question_submissions')"
            );
            DB::statement('PRAGMA writable_schema = 0');
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE extra_questions
                 MODIFY COLUMN typ ENUM('matching', 'image_name', 'image_select') NOT NULL"
            );
            DB::statement(
                "ALTER TABLE user_extra_question_submissions
                 MODIFY COLUMN typ ENUM('matching', 'image_name', 'image_select') NOT NULL"
            );
        }
    }
};
