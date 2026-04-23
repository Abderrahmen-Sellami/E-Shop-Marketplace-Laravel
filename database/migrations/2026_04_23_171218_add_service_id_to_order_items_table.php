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
        Schema::table('order_items', function (Blueprint $table) {
            // 1. On ajoute la colonne service_id (nullable car une ligne peut être un produit)
            // On la place après product_id pour garder une structure propre
            $table->foreignId('service_id')->nullable()->after('product_id')->constrained()->onDelete('set null');

            // 2. IMPORTANT : On rend product_id nullable
            // car si on commande un service, il n'y a pas de product_id
            $table->foreignId('product_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Pour revenir en arrière
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};