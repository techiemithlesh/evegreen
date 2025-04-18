<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bag_packing_transport_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pack_transport_id');
            $table->bigInteger('bag_packing_id');
            $table->boolean('is_delivered')->default(false);
            $table->bigInteger('reiving_user_id')->nullable();
            $table->date('reiving_date')->nullable();
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_packing_transport_details');
    }
};
