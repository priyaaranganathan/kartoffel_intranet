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
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->char('name', length: 250);
            $table->char('email', length: 150);
            $table->char('contact', length: 250);
            $table->foreignId('client_id')->nullable()->constrained();
            $table->boolean('is_primary_contact');
            $table->softDeletes('deleted_at', precision: 0);
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
    }
};
