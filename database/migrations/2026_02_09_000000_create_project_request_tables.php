<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('pending');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
