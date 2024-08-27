<?php

use App\Enums\ActivityStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->dateTime('start_date');
            $table->dateTime('due_date');
            $table->dateTime('payment_date');
            $table->float('payment_amount');
            $table->foreignId('project_id')->nullable()->constrained();
            $table->foreignId('requirement_id')->nullable()->constrained();
            $table->enum('status', array_column(ActivityStatus::cases(), 'value'));
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
