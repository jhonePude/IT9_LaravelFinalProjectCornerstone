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
        Schema::create('portal_event_registrations', function (Blueprint $table) {
            $table->id('Registration_Id');
        
            // Foreign Key to User
            $table->unsignedBigInteger('User_id');
            $table->foreign('User_id')->references('User_id')->on('user')->onDelete('cascade');

            // Foreign Key to Event
            $table->unsignedBigInteger('Event_Id');
            $table->foreign('Event_Id')->references('Event_Id')->on('events')->onDelete('cascade');

            $table->timestamp('Registration_Date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
