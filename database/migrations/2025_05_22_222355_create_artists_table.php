<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create artists table
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('bio')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });

        // Add artist_id column to concerts table (nullable for now)
        Schema::table('concerts', function (Blueprint $table) {
            $table->foreignId('artist_id')->nullable()->after('artist');
        });

        // Migrate existing data: create artists from unique concert artist names
        $uniqueArtists = DB::table('concerts')
            ->select('artist')
            ->distinct()
            ->get();

        foreach ($uniqueArtists as $artistRecord) {
            DB::table('artists')->insert([
                'name' => $artistRecord->artist,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update concerts to reference the new artists
        $concerts = DB::table('concerts')->get();
        foreach ($concerts as $concert) {
            $artist = DB::table('artists')
                ->where('name', $concert->artist)
                ->first();

            if ($artist) {
                DB::table('concerts')
                    ->where('id', $concert->id)
                    ->update(['artist_id' => $artist->id]);
            }
        }

        // Now make artist_id required and add foreign key constraint
        Schema::table('concerts', function (Blueprint $table) {
            $table->foreignId('artist_id')->nullable(false)->change();
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');

            // Drop the old artist string column
            $table->dropColumn('artist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the artist string column
        Schema::table('concerts', function (Blueprint $table) {
            $table->string('artist', 100)->after('id');
        });

        // Populate artist column from artists table
        $concerts = DB::table('concerts')
            ->join('artists', 'concerts.artist_id', '=', 'artists.id')
            ->select('concerts.id', 'artists.name as artist_name')
            ->get();

        foreach ($concerts as $concert) {
            DB::table('concerts')
                ->where('id', $concert->id)
                ->update(['artist' => $concert->artist_name]);
        }

        // Remove foreign key constraint and artist_id column
        Schema::table('concerts', function (Blueprint $table) {
            $table->dropForeign(['artist_id']);
            $table->dropColumn('artist_id');
        });

        // Drop artists table
        Schema::dropIfExists('artists');
    }
};
