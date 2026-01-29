<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('order')->default(0); 
            $table->string('name');
            $table->text('description')->nullable(); 
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
        
        // Tabel Pivot (task_user) untuk Multi-Contributors
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            // Relasi ke tasks
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            // Relasi ke users
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps(); // Mencatat kapan user berkontribusi

            // Mencegah duplikasi nama user yang sama di satu task
            $table->unique(['task_id', 'user_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus child table (pivot) dulu
        Schema::dropIfExists('task_user');
        
        // Baru hapus parent table
        Schema::dropIfExists('tasks');
    }
};