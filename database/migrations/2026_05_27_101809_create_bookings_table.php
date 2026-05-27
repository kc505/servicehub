<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_profile_id')->constrained()->onDelete('cascade');
            $table->string('service_description');
            $table->enum('status', ['pending', 'accepted', 'declined', 'completed'])->default('pending');
            $table->dateTime('scheduled_at')->nullable();
            $table->text('client_note')->nullable();
            $table->text('provider_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
