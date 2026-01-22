<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Symfony\Component\Clock\now;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feature_requests', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('title');
            $table->text('description');
            $table->string('request_type');
            $table->string('priority');
            $table->string('status');
            $table->integer('progress')->nullable()->default(0);
            $table->string('reporter_id');
            $table->string('assigned_to_id')->nullable();
            $table->string('assigned_team')->nullable();
            $table->timestamp('date_submitted')->useCurrent();
            $table->timestamp('approval_date')->nullable();
            $table->timestamp('assignment_date')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->timestamp('review_date')->nullable();
            $table->decimal('estimated_effort', 10, 2)->nullable();
            $table->decimal('actual_effort', 10, 2)->nullable();
            $table->decimal('sla_time_elapsed', 10, 2)->nullable();
            $table->decimal('sla_time_remaining', 10, 2)->nullable();
            $table->boolean('sla_breached')->nullable()->default(false);
            $table->string('approved_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('roi_impact')->nullable();
            $table->text('quality_impact')->nullable();
            $table->text('post_implementation_notes')->nullable();
            $table->string('source_ticket_id')->nullable();
            $table->boolean('is_direct_input')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_requests');
    }
};
