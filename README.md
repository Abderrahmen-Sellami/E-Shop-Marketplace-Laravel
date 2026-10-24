# E-Shop Marketplace

Marketplace e-commerce multi-vendeurs développée avec **Laravel 13**, **PHP 8.3**, **Bootstrap 5** et **Blade**. Des vendeurs publient des **produits** et des **services** ; des acheteurs les parcourent, les ajoutent au panier et passent commande. Un panneau d'administration permet de modérer le catalogue, les utilisateurs et les commandes.

Projet académique réalisé dans le cadre du module *Programmation Web 2* (année universitaire 2025-2026).

## Démonstration

🎥 **[Voir la vidéo de démonstration sur LinkedIn](https://www.linkedin.com/feed/update/urn:li:activity:7473684336354029569/)** : parcours complet de la marketplace (catalogue, panier hybride produits et services, commande, espace vendeur, administration).

## Fonctionnalités

**Catalogue et achat**
- Catalogue public avec recherche en direct (AJAX), filtre par catégorie, filtre par prix, tri et pagination
- Fiche produit avec avis et notes
- Liste de favoris (wishlist)
- Panier en session pour les produits **et** les services
- Commandes avec statuts (en attente, validée, annulée) et annulation par l'acheteur
- Création de commande dans une **transaction SQL** avec verrou de ligne (`lockForUpdate`) : le stock est vérifié puis décrémenté sans risque de survente, et restitué à l'annulation (produits uniquement)

**Vendeurs**
- CRUD des produits avec upload d'images
- Gestion de leurs propres services
- Notification en base de données à chaque nouvelle commande

**Administration**
- Tableau de bord et statistiques
- Gestion des utilisateurs et de leurs rôles
- Suivi des commandes et changement de statut
- Activation / désactivation des produits et des services

**Sécurité et architecture**
- Authentification complète (Laravel Breeze) : inscription, connexion, vérification d'e-mail, réinitialisation du mot de passe
- Trois rôles : `admin`, `seller`, `buyer`
- Autorisations par *policies* (produits, commandes, avis) et middleware d'accès administrateur
- API REST (Laravel Sanctum) pour les produits et les commandes

## Stack

Laravel 13 · PHP 8.3 · Blade · Bootstrap 5 (CDN) · Laravel Breeze · Laravel Sanctum · Vite

## Installation

Prérequis : PHP 8.3, Composer, Node.js, et une base de données (SQLite par défaut dans `.env.example`, ou MySQL).

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
# ajuster DB_* dans .env si besoin (MySQL), puis :
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Application disponible sur http://localhost:8000.

## Comptes de démonstration

Créés par le seeder (mot de passe identique pour tous : `password`).

| Rôle | E-mail |
|---|---|
| Administrateur | `admin@ecommerce.com` |
| Vendeur | `seller1@ecommerce.com`, `seller2@ecommerce.com` |
| Acheteur | `buyer1@ecommerce.com` |

Ces comptes sont destinés au développement local uniquement.

## API

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/products`, `/api/products/{id}` | Liste et détail des produits |
| POST / PUT / DELETE | `/api/products` | Création, modification, suppression (autorisées par policy) |
| GET | `/api/orders`, `/api/orders/{id}` | Commandes de l'utilisateur (Sanctum) |
| PATCH | `/api/orders/{id}/status` | Changement de statut (Sanctum) |

## Structure

```
app/Http/Controllers   catalogue, panier, commandes, avis, favoris, services, admin, API
app/Models             User, Product, Service, Category, Order, OrderItem, Review, Wishlist
app/Policies           règles d'autorisation
database/              migrations et seeder de démonstration
resources/views        vues Blade (catalogue, panier, commandes, admin…)
```

## Licence

Projet académique. Tous droits réservés.
