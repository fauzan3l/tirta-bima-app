<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
        $table->date('date');
        $table->time('time');
        $table->string('session_name')->default('Sesi Sore (15:30 WIB)');
        $table->string('status')->default('hadir');
        $table->string('notes')->nullable();
        $table->timestamps();
    });
    }
};