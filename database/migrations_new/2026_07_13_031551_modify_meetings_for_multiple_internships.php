<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create the pivot table
        Schema::create('internship_meeting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Migrate existing meeting associations to the pivot table
        $meetings = DB::table('meetings')->get();
        foreach ($meetings as $meeting) {
            if ($meeting->internship_id) {
                DB::table('internship_meeting')->insert([
                    'internship_id' => $meeting->internship_id,
                    'meeting_id' => $meeting->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Drop the foreign key and column from meetings
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['internship_id']);
            $table->dropColumn('internship_id');
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->foreignId('internship_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Restore associations (taking the first one for each meeting)
        $pivots = DB::table('internship_meeting')->get();
        foreach ($pivots as $pivot) {
            DB::table('meetings')->where('id', $pivot->meeting_id)
                ->update(['internship_id' => $pivot->internship_id]);
        }

        Schema::dropIfExists('internship_meeting');
    }
};
