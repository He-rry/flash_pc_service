<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            // Customer Info 
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('customer_address');
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('pc_model')->nullable();
            $table->text('issue_description')->nullable();
            // Relationships 
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('service_type_id')->constrained('service_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
