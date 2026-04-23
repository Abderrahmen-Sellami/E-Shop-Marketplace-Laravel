<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
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

        $buyer2 = User::create([
            'name'              => 'Leila Mansouri',
            'email'             => 'buyer2@ecommerce.com',
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

        // Sous-catégories
        Category::create(['name' => 'Smartphones', 'slug' => 'smartphones', 'parent_id' => $electronics->id]);
        Category::create(['name' => 'Ordinateurs', 'slug' => 'ordinateurs', 'parent_id' => $electronics->id]);

        // ── Produits ───────────────────────────────────────────
        $products = [
            ['user_id' => $seller1->id, 'category_id' => $electronics->id, 'title' => 'Smartphone Samsung Galaxy A55', 'description' => 'Excellent smartphone avec écran AMOLED 6.6", 8 Go RAM, 256 Go stockage.', 'price' => 899.99, 'stock' => 15],
            ['user_id' => $seller1->id, 'category_id' => $electronics->id, 'title' => 'Laptop HP ProBook 450', 'description' => 'Ordinateur portable professionnel Intel Core i5, 16 Go RAM, SSD 512 Go.', 'price' => 1299.00, 'stock' => 8],
            ['user_id' => $seller1->id, 'category_id' => $electronics->id, 'title' => 'Casque Bluetooth Sony WH-1000XM5', 'description' => 'Casque sans fil avec réduction de bruit active de classe mondiale.', 'price' => 349.99, 'stock' => 20],
            ['user_id' => $seller2->id, 'category_id' => $clothing->id, 'title' => 'Veste en cuir homme', 'description' => 'Veste en cuir véritable, coupe slim, disponible en noir et marron.', 'price' => 199.99, 'stock' => 30],
            ['user_id' => $seller2->id, 'category_id' => $clothing->id, 'title' => 'Robe d\'été femme', 'description' => 'Robe légère en coton, motifs floraux, parfaite pour la saison estivale.', 'price' => 59.99, 'stock' => 50],
            ['user_id' => $seller2->id, 'category_id' => $books->id, 'title' => 'Laravel 11 - Guide complet', 'description' => 'Maîtrisez Laravel de A à Z : routing, Eloquent, middleware, API REST.', 'price' => 39.99, 'stock' => 100],
            ['user_id' => $seller1->id, 'category_id' => $books->id, 'title' => 'Clean Code - Robert C. Martin', 'description' => 'Le guide de référence pour écrire du code propre et maintenable.', 'price' => 45.00, 'stock' => 60],
            ['user_id' => $seller2->id, 'category_id' => $home->id, 'title' => 'Lampe de bureau LED', 'description' => 'Lampe ergonomique avec réglage de luminosité et port USB intégré.', 'price' => 79.99, 'stock' => 25],
            ['user_id' => $seller1->id, 'category_id' => $sports->id, 'title' => 'Tapis de yoga premium', 'description' => 'Tapis antidérapant 6mm, matière écologique, idéal pour yoga et pilates.', 'price' => 49.99, 'stock' => 40],
            ['user_id' => $seller2->id, 'category_id' => $sports->id, 'title' => 'Vélo de route Shimano 21V', 'description' => 'Vélo léger aluminium 21 vitesses, parfait pour les trajets urbains.', 'price' => 599.00, 'stock' => 10],
        ];

        $createdProducts = [];
        foreach ($products as $data) {
            $createdProducts[] = Product::create($data);
        }

        // ── Reviews ────────────────────────────────────────────
        $reviewsData = [
            [$buyer1->id, $createdProducts[0]->id, 5, 'Excellent smartphone, très rapide !'],
            [$buyer2->id, $createdProducts[0]->id, 4, 'Bonne qualité, livraison rapide.'],
            [$buyer1->id, $createdProducts[1]->id, 5, 'Parfait pour le travail, très performant.'],
            [$buyer2->id, $createdProducts[2]->id, 4, 'Réduction de bruit impressionnante.'],
            [$buyer1->id, $createdProducts[5]->id, 5, 'Livre très complet, je recommande vivement !'],
        ];

        foreach ($reviewsData as [$userId, $productId, $rating, $comment]) {
            Review::create([
                'user_id'    => $userId,
                'product_id' => $productId,
                'rating'     => $rating,
                'comment'    => $comment,
            ]);
        }

        // ── Commandes ──────────────────────────────────────────
        $order1 = Order::create([
            'user_id'          => $buyer1->id,
            'status'           => 'validated',
            'total'            => 1249.98,
            'shipping_address' => '12 Rue de la République, Tunis 1001',
            'payment_method'   => 'card',
        ]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $createdProducts[0]->id, 'quantity' => 1, 'unit_price' => 899.99]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $createdProducts[2]->id, 'quantity' => 1, 'unit_price' => 349.99]);

        $order2 = Order::create([
            'user_id'          => $buyer2->id,
            'status'           => 'pending',
            'total'            => 199.99,
            'shipping_address' => '45 Avenue Habib Bourguiba, Sfax 3000',
            'payment_method'   => 'cash',
        ]);
        OrderItem::create(['order_id' => $order2->id, 'product_id' => $createdProducts[3]->id, 'quantity' => 1, 'unit_price' => 199.99]);

        $order3 = Order::create([
            'user_id'          => $buyer1->id,
            'status'           => 'cancelled',
            'total'            => 79.98,
            'shipping_address' => '12 Rue de la République, Tunis 1001',
            'payment_method'   => 'paypal',
        ]);
        OrderItem::create(['order_id' => $order3->id, 'product_id' => $createdProducts[5]->id, 'quantity' => 2, 'unit_price' => 39.99]);

        $this->command->info('✅ Base de données peuplée avec succès !');
        $this->command->info('Admin : admin@ecommerce.com / password');
        $this->command->info('Seller : seller1@ecommerce.com / password');
        $this->command->info('Buyer : buyer1@ecommerce.com / password');
    }
}
