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
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->string('statusable_id');
            $table->string('statusable_type');
            $table->string('previous_status');
            $table->string('new_status');
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->nullOnDelete();   
            $table->timestamp('changed_at')->useCurrent();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            $table->index(['statusable_id', 'statusable_type']);
            $table->index('changed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
};
