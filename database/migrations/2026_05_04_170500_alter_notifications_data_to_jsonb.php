<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Replace clearly non-JSON payloads with an empty object to avoid cast errors.
        DB::statement("UPDATE notifications SET data = '{}' WHERE trim(data) NOT LIKE '{%' AND trim(data) NOT LIKE '[%';");

        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE jsonb USING data::jsonb');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE text USING data::jsonb::text');
    }
};
