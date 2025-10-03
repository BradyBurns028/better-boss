<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method creates the `users`, `password_reset_tokens`, and `sessions` tables.
     *
     * @return void
     */
    public function up(): void
    {
        // Create the 'users' table
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('first_name'); // User's first name
            $table->string('last_name'); // User's last name
            $table->string('email')->unique(); // Unique email address
            $table->string('password'); // User's password
            $table->timestamps(); // Timestamps for created_at and updated_at
            $table->softDeletes(); // Soft delete column
            $table->timestamp('email_verified_at')->nullable(); // Nullable timestamp for email verification
            $table->rememberToken(); // Token for "remember me" functionality
        });

        // Create the 'password_reset_tokens' table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Primary key: email address
            $table->string('token'); // Reset token
            $table->timestamp('created_at')->nullable(); // Nullable timestamp for token creation
        });

        // Create the 'sessions' table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Primary key: session ID
            $table->foreignId('user_id')->nullable()->index(); // Nullable foreign key to 'users' table
            $table->string('ip_address', 45)->nullable(); // Nullable IP address (supports IPv4 and IPv6)
            $table->text('user_agent')->nullable(); // Nullable user agent string
            $table->longText('payload'); // Session payload data
            $table->integer('last_activity')->index(); // Indexed timestamp for last activity
        });
    }

    /**
     * Reverse the migrations.
     * This method drops the `users`, `password_reset_tokens`, and `sessions` tables.
     *
     * @return void
     */
    public function down(): void
    {
        // Drop the 'users' table
        Schema::dropIfExists('users');

        // Drop the 'password_reset_tokens' table
        Schema::dropIfExists('password_reset_tokens');

        // Drop the 'sessions' table
        Schema::dropIfExists('sessions');
    }
};
