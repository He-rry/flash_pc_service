<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
        });
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
           $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('set null');
            $table->string('action'); // 'ADD', 'IMPORT', 'EXPORT'
            $table->string('module')->default('SHOPS');
            $table->text('description');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
