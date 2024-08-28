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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->char('first_name', length: 250);
            $table->char('last_name', length: 250);
            $table->char('email', length: 250)->unique();
            $table->char('contact', length: 250)->unique();
            $table->foreignId('role_id')->nullable()->constrained();
            $table->foreignId('department_id')->nullable()->constrained();
            $table->unsignedBigInteger('reporting_manager_id')->nullable(); // Foreign key for reporting manager
            $table->boolean('status');
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();

            $table->foreign('reporting_manager_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null'); // Handle deletion of reporting manager
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
