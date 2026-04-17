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
         // CREATE THIS FIRST
        Schema::create('roles', function (Blueprint $table) {
            $table->id('Role_Id'); 
            $table->string('Role_Name');
        });

        // THEN CREATE THE USER TABLE
         Schema::create('user', function (Blueprint $table) {
            $table->id('User_id');
            $table->string('Fullname');
            $table->string('Username');
            $table->string('Email');
            $table->string('Password');
            $table->string('Gender');
            $table->string('Contact_Number');
            $table->string('Profile_Picture')->default('profile-male.jpg');
            $table->string('Account_Status')->default('Active');
            $table->rememberToken(); 
            
            
            // This links to the roles table
            $table->unsignedBigInteger('Role_Id')->default(2);
            $table->foreign('Role_Id')->references('Role_Id')->on('roles');
        });

        
        // DO NOT DELETE the Schema::create('sessions'...) part below it!
        // This is what fixes your "Table sessions not found" error.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
            
           
        });




         //TRANSACTION CATEGORIES (e.g., Tithes, Offering, Electricity, Rent)
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id('Category_Id'); 
            $table->string('Category_Name');
        });




        //TRANSACTIONS TABLE
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('Transaction_Id');
            $table->string('Description');
            $table->enum('Type', ['Income', 'Expense']); // Forces choice between Income/Expense
            $table->decimal('Amount', 15, 2);
            
            $table->date('Transaction_Date');
            $table->string('Status')->default('Completed'); // e.g., Pending, Completed, Cancelled
            
            // Foreign Key: Link to transactions_category
            $table->unsignedBigInteger('Category_Id');
            $table->foreign('Category_Id')->references('Category_Id')->on('transaction_categories');

            // Foreign Key: Link to user table
            $table->unsignedBigInteger('Recorded_By');
            $table->foreign('Recorded_By')->references('User_id')->on('user');

        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transaction_categories');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user');
        Schema::dropIfExists('roles');
    }
};
