# Rapport Technique — Projet E-Commerce Laravel
**Module :** Programmation Web 2  
**Niveau :** 2ème année E-Business  
**Année universitaire :** 2025–2026

---

## 1. Description du projet

**E-Shop** est une plateforme e-commerce complète développée avec le framework Laravel 11.  
Elle permet la mise en relation de vendeurs et d'acheteurs autour d'un catalogue de produits multi-catégories, avec gestion des commandes, des avis, et un panel d'administration complet.

### Fonctionnalités implémentées

**Obligatoires :**
- Authentification complète (inscription, connexion, vérification email, réinitialisation mot de passe)
- Gestion du profil utilisateur
- CRUD produits avec upload d'images
- Catalogue public avec recherche, filtrage par catégorie, tri et pagination
- Panier (session Laravel)
- Commandes avec statuts (En attente / Validée / Annulée)
- Évaluations et commentaires produits

**Bonus niveau 1 :**
- Pagination Laravel native
- Upload d'images (Storage::disk public)
- Wishlist (favoris)
- Filtres avancés (prix min/max, catégorie, tri)

**Bonus niveau 2 :**
- Notifications (base de données — nouvelles commandes)
- API REST complète (produits + commandes, authentification Sanctum)
- Recherche AJAX live (barre de recherche en temps réel)

**Bonus niveau 3 :**
- Gestion des rôles : Admin / Seller / Buyer
- Panel d'administration (statistiques, modération produits, gestion utilisateurs et commandes)
- Architecture multi-vendeurs (chaque vendeur gère ses propres produits)

---

## 2. Architecture MVC Laravel

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php     — CRUD produits + upload
│   │   ├── CatalogController.php     — Catalogue public + AJAX search
│   │   ├── CartController.php        — Panier (session)
│   │   ├── OrderController.php       — Commandes (store, cancel)
│   │   ├── ReviewController.php      — Avis produits
│   │   ├── WishlistController.php    — Wishlist toggle AJAX
│   │   ├── Admin/
│   │   │   └── AdminController.php   — Dashboard + modération
│   │   └── Api/
│   │       ├── ProductController.php — REST API produits
│   │       └── OrderController.php  — REST API commandes
│   └── Middleware/
│       └── IsAdmin.php               — Protection routes admin
├── Models/
│   ├── User.php       — Rôles, relations
│   ├── Product.php    — Scopes, accessors, SoftDeletes
│   ├── Category.php   — Hiérarchie parent/enfant
│   ├── Order.php      — Statuts, labels
│   ├── OrderItem.php  — Sous-total calculé
│   ├── Review.php     — Contrainte unicité user/product
│   └── Wishlist.php
├── Policies/
│   ├── ProductPolicy.php — Autorisation update/delete
│   ├── OrderPolicy.php   — Autorisation view/update
│   └── ReviewPolicy.php  — Autorisation delete
├── Notifications/
│   └── NewOrderNotification.php — Notification vendeur
└── Providers/
    └── AppServiceProvider.php   — Enregistrement policies
```

---

## 3. Schéma de la base de données

### Tables et relations

| Table         | Description                                    |
|---------------|------------------------------------------------|
| users         | Utilisateurs avec rôle (admin/seller/buyer)    |
| categories    | Catégories avec hiérarchie parent_id           |
| products      | Produits liés à un vendeur et une catégorie    |
| orders        | Commandes d'un acheteur (statut, total)        |
| order_items   | Lignes de commande (produit, quantité, prix)   |
| reviews       | Avis (1 par user/produit, note 1-5)            |
| wishlists     | Table pivot user ↔ produit                    |
| notifications | Notifications Laravel (polymorphiques)        |

### Relations principales

```
User (1) ──────────── (N) Product        [vendeur]
User (1) ──────────── (N) Order          [acheteur]
User (N) ──────────── (N) Product        [wishlist]
User (1) ──────────── (N) Review

Category (1) ──────── (N) Product
Category (1) ──────── (N) Category       [sous-catégories]

Order (1) ─────────── (N) OrderItem
Product (1) ────────── (N) OrderItem
Product (1) ────────── (N) Review
```

---

## 4. Choix techniques

### Pourquoi Laravel ?
Laravel impose une structure MVC claire, fournit Eloquent ORM pour éviter les injections SQL, Blade pour les templates avec échappement automatique (protection XSS), et une protection CSRF native sur tous les formulaires POST.

### Authentification — Laravel Breeze
Laravel Breeze génère le système d'authentification complet (inscription, connexion, vérification email, réinitialisation mot de passe) tout en restant du code Laravel pur et lisible. C'est la solution recommandée pour les projets de taille moyenne.

### Panier en session
Le panier est stocké en session (`session()->put('cart', [...])`) plutôt qu'en base de données. Avantage : pas besoin d'être connecté pour ajouter au panier, et les performances sont meilleures pour les accès fréquents.

### API REST — Laravel Sanctum
Sanctum fournit l'authentification par token pour les API sans la complexité d'OAuth2 (contrairement à Passport). Adapté pour un projet de cette taille.

### Gestion des rôles — Champ enum + Middleware
Plutôt qu'un package tiers (Spatie), les rôles sont gérés avec un champ `enum('role', ['admin','seller','buyer'])` sur la table users. Plus simple, entièrement maîtrisé, et suffisant pour les besoins du projet.

### Policies vs Gates
Les **Policies** sont utilisées pour l'autorisation sur des modèles spécifiques (`ProductPolicy::update` vérifie que l'utilisateur est admin ou propriétaire du produit). Les **Gates** seraient utilisées pour des vérifications globales non liées à un modèle.

### SoftDeletes sur Product
Les produits supprimés restent en base de données (soft delete), ce qui préserve l'historique des commandes contenant ces produits.

---

## 5. Sécurité

| Menace          | Solution Laravel                                      |
|-----------------|-------------------------------------------------------|
| Injection SQL   | Eloquent ORM — requêtes préparées automatiques        |
| XSS             | Blade — `{{ }}` échappe le HTML par défaut            |
| CSRF            | Middleware VerifyCsrfToken + directive `@csrf`         |
| Accès non auth. | Middleware `auth` + `verified` sur routes protégées   |
| Accès non admin | Middleware `IsAdmin` sur routes `/admin/*`            |
| Ownership       | Policies Laravel (ProductPolicy, OrderPolicy)         |
| Upload fichiers | Validation MIME type + taille max (2MB) côté serveur  |

---

## 6. Diagramme de classes (simplifié)

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│    User     │       │   Product    │       │  Category   │
│─────────────│       │──────────────│       │─────────────│
│ id          │       │ id           │       │ id          │
│ name        │1    N │ user_id (FK) │N     1│ name        │
│ email       │───────│ category_id  │───────│ slug        │
│ password    │       │ title        │       │ parent_id   │
│ role        │       │ price        │       └─────────────┘
│ avatar      │       │ stock        │
└──────┬──────┘       │ image        │
       │              │ status       │
       │1             └──────┬───────┘
       │                     │1
       │N                    │N
┌──────┴──────┐       ┌──────┴───────┐
│   Order     │       │  OrderItem   │
│─────────────│       │──────────────│
│ id          │1    N │ id           │
│ user_id(FK) │───────│ order_id(FK) │
│ status      │       │ product_id   │
│ total       │       │ quantity     │
│ created_at  │       │ unit_price   │
└─────────────┘       └──────────────┘
```

---

## 7. Installation et démarrage

Voir le fichier `INSTALL.md` pour le guide d'installation complet.

Comptes de test après seeding :
| Rôle    | Email                    | Mot de passe |
|---------|--------------------------|--------------|
| Admin   | admin@ecommerce.com      | password     |
| Vendeur | seller1@ecommerce.com    | password     |
| Acheteur| buyer1@ecommerce.com     | password     |
