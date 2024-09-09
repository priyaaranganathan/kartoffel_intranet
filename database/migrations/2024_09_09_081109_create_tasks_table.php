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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('code')->unique()->nullable(); // Make it unique
            // $table->enum('type', ['Project', 'Requirement', 'General']);
            $table->foreignId('project_id')->nullable()->constrained();
            $table->foreignId('requirement_id')->nullable()->constrained();
            $table->enum('status', array_column(ActivityStatus::cases(), 'value'));
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
