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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open');
            $table->nullableMorphs('user');
            $table->string('priority')->default('medium');
            $table->string('category')->nullable();
            $table->uuid('assigned_to')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('priority');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
}; 