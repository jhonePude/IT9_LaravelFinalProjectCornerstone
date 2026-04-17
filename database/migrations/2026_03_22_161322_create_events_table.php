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
        Schema::create('events', function (Blueprint $table) {
            
        $table->id('Event_Id');
        $table->string('Title');
        $table->text('Description');
        $table->date('Event_Date');
        $table->time('Event_Time');
        $table->string('Location');
        $table->string('Image_Banner')->nullable();
        
        // Foreign Key to Categories
        $table->unsignedBigInteger('Event_Category_Id');
        $table->foreign('Event_Category_Id')->references('Event_Category_Id')->on('event_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
