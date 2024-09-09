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
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique()->nullable(); // Make it unique
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_category_id')->constrained()->onDelete('cascade');
            $table->enum('status', array_column(ActivityStatus::cases(), 'value'));
            $table->float('efforts')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
