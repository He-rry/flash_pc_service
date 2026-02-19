<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->timestamps();
        });

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_UpdateServiceType;
            CREATE PROCEDURE sp_UpdateServiceType(IN p_id INT, IN p_name VARCHAR(255))
            BEGIN
                UPDATE service_types 
                SET service_name = p_name, updated_at = NOW()
                WHERE id = p_id;
            END;
        ");
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_DeleteServiceType;
            CREATE PROCEDURE sp_DeleteServiceType(IN p_id INT)
            BEGIN
                DELETE FROM service_types WHERE id = p_id;
            END;
        ");
        DB::unprepared("
        DROP PROCEDURE IF EXISTS sp_CreateServiceType;
        CREATE PROCEDURE  sp_CreateServiceType(IN p_name VARCHAR(255))
        BEGIN
        INSERT INTO service_types(service_name,created_at,updated_at)
        VALUES(p_name, NOW(), NOW());
        END;
        ");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_UpdateServiceType");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_DeleteServiceType");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_CreateServiceType");
        
    }
};
