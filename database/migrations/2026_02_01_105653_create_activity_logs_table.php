<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // ဘယ်သူလုပ်တာလဲ
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // ဘယ်ဆိုင်မှာ လုပ်တာလဲ (Nullable ဖြစ်ရမယ်၊ User ဆောက်တဲ့အခါ Shop ID မရှိလို့ပါ)
            $table->foreignId('shop_id')->nullable()->constrained('shops')->onDelete('set null');

            $table->string('action'); // ADD, EDIT, DELETE, IMPORT
            $table->string('module'); // SHOPS, USERS, ROLES
            $table->text('description'); // စာသားအရှည်

            // JSON Column - ဒါက Frontend (API) အတွက် အသက်ပဲ!
            $table->json('changes')->nullable();

            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
