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
            $table->char('name', length: 250);
            $table->text('description');
            $table->foreignId('client_id')->constrained();
            $table->softDeletes('deleted_at', precision: 0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->float('total_cost');
            $table->boolean('status');
            $table->timestamps();
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
