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
            Schema::create('tickets', function (Blueprint $table) {
                $table->id()->primary();
                $table->string('title');
                $table->text('description');
                $table->string('category');
                $table->string('priority');
                $table->string('status');
                $table->string('reporter_id');
                $table->string('assigned_to_id')->nullable();
                $table->string('assigned_team')->nullable();
                $table->timestamp('date_reported');
                $table->timestamp('due_date')->nullable();
                $table->timestamp('resolved_date')->nullable();
                $table->timestamp('closed_date')->nullable();
                $table->boolean('sla_breached')->default(false);
                $table->decimal('response_time')->nullable();
                $table->decimal('resolution_time')->nullable();
                $table->decimal('estimated_effort')->nullable();
                $table->decimal('actual_effort')->nullable();
                $table->string('parent_ticket_id')->nullable();
                $table->string('converted_to_type')->nullable();
                $table->string('converted_to_id')->nullable();
                $table->timestamp('converted_at')->nullable();
                $table->string('converted_by')->nullable();
                $table->text('conversion_reason')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
