<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Base\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table($this->prefix.'products', function (Blueprint $table) {
            $table->string('heureka_feed_category')->nullable()->after('attribute_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table($this->prefix.'products', function (Blueprint $table) {
            $table->dropColumn('heureka_feed_category');
        });
    }
};
