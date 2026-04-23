# Guide d'installation — E-Shop Laravel

## Prérequis
- WAMP Server (PHP 8.2+, MySQL 8+)
- Composer installé globalement
- Git

---

## Étape 1 — Créer le projet Laravel

Ouvre une invite de commande (cmd) dans `C:\wamp64\www\` et lance :

```bash
composer create-project laravel/laravel ecommerce
cd ecommerce
```

---

## Étape 2 — Installer les dépendances

```bash
# Authentification (Breeze avec Blade)
composer require laravel/breeze --dev
php artisan breeze:install blade

# API REST (Sanctum)
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

---

## Étape 3 — Copier les fichiers du projet

Copie tous les fichiers fournis dans le dossier `ecommerce/` en respectant l'arborescence :

```
ecommerce/
├── app/
│   ├── Http/Controllers/     ← Copier tous les controllers
│   ├── Models/               ← Copier tous les models
│   ├── Policies/             ← Copier les policies
│   ├── Notifications/        ← Copier les notifications
│   └── Providers/            ← Remplacer AppServiceProvider.php
├── bootstrap/
│   └── app.php               ← Remplacer (middleware alias admin)
├── database/
│   ├── migrations/           ← Copier toutes les migrations
│   └── seeders/              ← Remplacer DatabaseSeeder.php
├── resources/views/          ← Copier toutes les vues
├── routes/
│   ├── web.php               ← Remplacer
│   └── api.php               ← Remplacer
└── docs/                     ← Rapport technique
```

---

## Étape 4 — Configurer la base de données

### 4a. Créer la base dans phpMyAdmin
Ouvre `http://localhost/phpmyadmin` et crée une base nommée `ecommerce_db`.

### 4b. Configurer le fichier `.env`
Copie `.env.example` en `.env` et modifie :

```env
APP_NAME=EcommerceApp
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=          # Laisser vide si WAMP par défaut
```

---

## Étape 5 — Générer la clé et configurer le storage

```bash
php artisan key:generate
php artisan storage:link
```

---

## Étape 6 — Migrations et seeders

```bash
php artisan migrate:fresh --seed
```

✅ La base de données est créée et peuplée avec des données de test.

**Comptes créés :**
| Rôle     | Email                 | Mot de passe |
|----------|-----------------------|--------------|
| Admin    | admin@ecommerce.com   | password     |
| Vendeur  | seller1@ecommerce.com | password     |
| Vendeur  | seller2@ecommerce.com | password     |
| Acheteur | buyer1@ecommerce.com  | password     |
| Acheteur | buyer2@ecommerce.com  | password     |

---

## Étape 7 — Lancer le serveur

```bash
php artisan serve
```

Ouvre `http://localhost:8000` dans ton navigateur.

---

## Étape 8 — Vérifier l'installation

### Checklist
- [ ] Page d'accueil affiche les produits
- [ ] Inscription/connexion fonctionnent
- [ ] Catalogue avec filtres fonctionne
- [ ] Ajouter au panier fonctionne
- [ ] Connexion admin → dashboard visible à `/admin/dashboard`
- [ ] Upload d'image lors de la création de produit

### Test de l'API REST
```bash
# Lister les produits (public)
curl http://localhost:8000/api/products

# Connexion (obtenir token)
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"seller1@ecommerce.com","password":"password"}'
```

---

## Problèmes courants

### "Class not found" après la copie de fichiers
```bash
composer dump-autoload
```

### Permission denied sur storage/
```bash
# Windows : pas de problème généralement
# Si besoin :
php artisan cache:clear
php artisan config:clear
```

### Les images ne s'affichent pas
```bash
php artisan storage:link
```
Vérifie que `public/storage` est bien un lien symbolique vers `storage/app/public`.

### Erreur CSRF token mismatch
S'assurer que `@csrf` est présent dans tous les formulaires POST (déjà inclus dans les vues fournies).

### Email de vérification ne fonctionne pas
Pour les tests locaux, configurer Mailtrap dans `.env` ou utiliser :
```env
MAIL_MAILER=log
```
Les emails seront loggés dans `storage/logs/laravel.log`.

---

## Structure des routes

| Méthode | URL                          | Description              | Auth     |
|---------|------------------------------|--------------------------|----------|
| GET     | /                            | Accueil                  | Non      |
| GET     | /catalog                     | Catalogue                | Non      |
| GET     | /catalog/search?q=           | Recherche AJAX           | Non      |
| GET     | /products/{product}          | Détail produit           | Non      |
| GET     | /products                    | Mes produits             | Auth     |
| POST    | /products                    | Créer produit            | Auth     |
| PUT     | /products/{product}          | Modifier produit         | Auth     |
| DELETE  | /products/{product}          | Supprimer produit        | Auth     |
| POST    | /cart/add/{product}          | Ajouter au panier        | Auth     |
| POST    | /orders                      | Passer une commande      | Auth     |
| GET     | /orders                      | Mes commandes            | Auth     |
| GET     | /admin/dashboard             | Dashboard admin          | Admin    |
| GET     | /api/products                | API produits             | Public   |
| GET     | /api/orders                  | API commandes            | Sanctum  |
