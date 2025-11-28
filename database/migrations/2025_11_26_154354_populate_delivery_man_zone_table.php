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
        $deliveryMen = DB::table('delivery_men')->whereNotNull('zone_id')->get();

        foreach ($deliveryMen as $dm) {
            // Check if entry already exists to avoid duplicates
            $exists = DB::table('delivery_man_zone')
                ->where('delivery_man_id', $dm->id)
                ->where('zone_id', $dm->zone_id)
                ->exists();

            if (!$exists) {
                DB::table('delivery_man_zone')->insert([
                    'delivery_man_id' => $dm->id,
                    'zone_id' => $dm->zone_id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('delivery_man_zone')->truncate();
    }
};
