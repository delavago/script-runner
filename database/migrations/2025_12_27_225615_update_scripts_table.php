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
        Schema::table('scripts', function (Blueprint $table) {
            $table->boolean('use_credentials')->default(false);
            $table->foreignUuid('credential_id')->nullable()->constrained('credentials')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropForeign(['credential_id']);
            $table->dropColumn('use_credentials');
            $table->dropColumn('credential_id');
        });
    }
};
