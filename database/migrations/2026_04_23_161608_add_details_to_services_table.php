<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // On ajoute les colonnes manquantes après le ID
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->string('name')->after('user_id');
            $table->text('description')->after('name');
            $table->decimal('price', 8, 2)->after('description');
            $table->boolean('is_active')->default(true)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'name', 'description', 'price', 'is_active']);
        });
    }
};