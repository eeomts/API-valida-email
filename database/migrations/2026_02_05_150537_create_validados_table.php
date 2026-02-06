<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validados', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->longText('token');
            $table->string('origem'); 
            $table->unsignedTinyInteger('validado')->default(0);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validados');
    }
};
