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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('credits')->default(0)->after('role');
        });

        Schema::table('habits', function (Blueprint $table) {
            $table->string('type', 32)->default('private')->after('user_id');
            $table->foreignId('parent_id')->nullable()->after('type')->constrained('habits')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('habits', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['type', 'parent_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['credits']);
        });
    }
};
