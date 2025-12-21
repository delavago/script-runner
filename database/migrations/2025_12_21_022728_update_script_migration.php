<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropColumn('file_type');
            $table->dropColumn('file_path');
            $table->jsonb('attachment')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
