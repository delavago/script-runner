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
        Schema::create('credentials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // e.g., "Production DB", "AWS Account"
            $table->string('type'); // e.g., "ssh", "database", "api_key", "windows", "linux_user"
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // Encrypted
            $table->text('private_key')->nullable(); // For SSH keys, encrypted
            $table->string('host')->nullable(); // Server/endpoint
            $table->integer('port')->nullable();
            $table->string('domain')->nullable(); // For Windows domain accounts
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
