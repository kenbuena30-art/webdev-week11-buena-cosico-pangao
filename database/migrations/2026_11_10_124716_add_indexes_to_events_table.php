<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // GIN index on the whole details column (for whereJsonContains / @>)
            $table->index('details', 'events_details_gin_idx', 'gin');

            // Full-text index on description (Laravel builds a GIN to_tsvector index)
            $table->fullText('description', 'events_description_fts_idx');
        });

        // Expression index for the arrow filter. Laravel's schema builder
        // cannot create an index on an expression, so we use raw SQL.
        DB::statement("CREATE INDEX events_dress_code_idx ON events ((details->>'dress_code'))");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS events_dress_code_idx');

        Schema::table('events', function (Blueprint $table) {
            $table->dropFullText('events_description_fts_idx');
            $table->dropIndex('events_details_gin_idx');
        });
    }
};