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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['planning', 'active', 'on_hold', 'completed'])->default('planning');
            $table->string('color', 7)->default('#3B82F6');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict')->index();
            $table->timestamps();

            $table->unique(['organization_id', 'slug']); // composite key means -> slug must be unique within that organization.
            $table->index('status');
            $table->index('created_by');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
