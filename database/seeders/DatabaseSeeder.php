<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Service; // AJOUTÉ
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Utilisateurs ───────────────────────────────────────
        $admin = User::create([
            'name'              => 'Admin Principal',
            'email'             => 'admin@ecommerce.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        $seller1 = User::create([
            'name'              => 'Mohamed Ben Ali',
            'email'             => 'seller1@ecommerce.com',
            'password'          => Hash::make('password'),
            'role'              => 'seller',
            'email_verified_at' => now(),
        ]);

        $seller2 = User::create([
            'name'              => 'Sarra Trabelsi',
            'email'             => 'seller2@ecommerce.com',
            'password'          => Hash::make('password'),
            'role'              => 'seller',
            'email_verified_at' => now(),
        ]);

        $buyer1 = User::create([
            'name'              => 'Ahmed Chaabane',
            'email'             => 'buyer1@ecommerce.com',
            'password'          => Hash::make('password'),
            'role'              => 'buyer',
            'email_verified_at' => now(),
        ]);

        // ── Catégories ─────────────────────────────────────────
        $electronics = Category::create(['name' => 'Électronique',  'slug' => 'electronique',  'description' => 'Appareils et gadgets électroniques']);
        $clothing    = Category::create(['name' => 'Vêtements',     'slug' => 'vetements',      'description' => 'Mode et accessoires']);
        $books       = Category::create(['name' => 'Livres',        'slug' => 'livres',         'description' => 'Romans, techniques, éducation']);
        $home        = Category::create(['name' => 'Maison',        'slug' => 'maison',         'description' => 'Décoration et mobilier']);
        $sports      = Category::create(['name' => 'Sport',         'slug' => 'sport',          'description' => 'Équipement sportif']);

        // ── Produits ───────────────────────────────────────────
        $p1 = Product::create(['user_id' => $seller1->id, 'category_id' => $electronics->id, 'title' => 'Smartphone Samsung Galaxy A55', 'description' => 'Excellent smartphone.', 'price' => 899.99, 'stock' => 15]);
        $p2 = Product::create(['user_id' => $seller1->id, 'category_id' => $electronics->id, 'title' => 'Laptop HP ProBook 450', 'description' => 'Ordinateur pro.', 'price' => 1299.00, 'stock' => 8]);
        $p3 = Product::create(['user_id' => $seller2->id, 'category_id' => $clothing->id, 'title' => 'Veste en cuir homme', 'description' => 'Cuir véritable.', 'price' => 199.99, 'stock' => 30]);

        // ── SERVICES (AJOUTÉ POUR VOTRE BONUS) ────────────────
        $s1 = Service::create([
            'user_id'     => $seller1->id,
            'name'        => 'Réparation Smartphone',
            'description' => 'Réparation écran et batterie en 24h.',
            'price'       => 50.00,
            'is_active'   => true
        ]);

        $s2 = Service::create([
            'user_id'     => $seller2->id,
            'name'        => 'Cours de Yoga à domicile',
            'description' => 'Séance personnalisée de 1 heure.',
            'price'       => 40.00,
            'is_active'   => true
        ]);

        // ── Commandes ──────────────────────────────────────────
        
       
        $order1 = Order::create([
            'user_id'          => $buyer1->id,
            'status'           => 'validated',
            'total'            => 899.99,
            'shipping_address' => 'Tunis, Tunisie',
            'payment_method'   => 'card',
        ]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $p1->id, 'quantity' => 1, 'unit_price' => 899.99]);

        
        $order2 = Order::create([
            'user_id'          => $buyer1->id,
            'status'           => 'pending',
            'total'            => 249.99,
            'shipping_address' => 'Tunis, Tunisie',
            'payment_method'   => 'cash',
        ]);
        // L'item produit
        OrderItem::create(['order_id' => $order2->id, 'product_id' => $p3->id, 'quantity' => 1, 'unit_price' => 199.99]);
        // L'item service
        OrderItem::create(['order_id' => $order2->id, 'service_id' => $s1->id, 'quantity' => 1, 'unit_price' => 50.00]);

        $this->command->info('✅ Base de données peuplée avec succès (incluant les Services) !');
    }
}