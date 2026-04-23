# Guide de préparation — Questions de soutenance

## Questions fréquentes et réponses modèles

---

### 1. "Pourquoi avez-vous utilisé Eloquent plutôt que des requêtes SQL brutes ?"

**Réponse :** Eloquent offre plusieurs avantages dans notre contexte :
- **Sécurité** : toutes les requêtes sont des prepared statements, ce qui élimine les injections SQL sans effort supplémentaire.
- **Lisibilité** : `Product::active()->with('category')->paginate(12)` est plus clair que le SQL équivalent.
- **Relations** : le lazy/eager loading (`with(['user', 'reviews'])`) évite le problème N+1 et simplifie le code des controllers.
- **Évolutivité** : changer de SGBD (MySQL → PostgreSQL) ne nécessite pas de réécrire les requêtes.

---

### 2. "Comment fonctionne votre middleware IsAdmin ?"

**Réponse :** Le middleware `IsAdmin` intercepte chaque requête vers les routes `/admin/*` avant qu'elle n'atteigne le controller. Il vérifie `$request->user()->isAdmin()` — une méthode sur le modèle `User` qui compare le champ `role` à `'admin'`. Si la vérification échoue, il lève une erreur 403 (Forbidden). Il est enregistré comme alias `'admin'` dans `bootstrap/app.php` et appliqué via `->middleware('admin')` sur le groupe de routes admin.

---

### 3. "Quelle est la différence entre une Gate et une Policy ?"

**Réponse :** 
- **Gate** : vérification simple et globale, définie dans `AppServiceProvider`. Ex : `Gate::define('is-admin', fn($user) => $user->isAdmin())`. Utilisé pour des vérifications non liées à un modèle spécifique.
- **Policy** : classe dédiée à un modèle. Ex : `ProductPolicy::update($user, $product)` vérifie si l'utilisateur peut modifier CE produit spécifique (admin ou propriétaire). Plus organisé pour des autorisations complexes par ressource.

Dans notre projet, on utilise des Policies car chaque vérification porte sur une instance précise (ce produit, cette commande).

---

### 4. "Comment sécurisez-vous les uploads d'images ?"

**Réponse :** Plusieurs niveaux de sécurité :
1. **Validation côté serveur** : `'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048'` — on vérifie le vrai type MIME, pas juste l'extension.
2. **Stockage hors public** : les fichiers sont stockés dans `storage/app/public/products/` et accessibles via un lien symbolique. Ils ne sont pas dans le dossier `public/` directement.
3. **Noms aléatoires** : Laravel génère un nom unique pour chaque fichier (`store('products', 'public')`), ce qui empêche d'écraser des fichiers existants ou de deviner les URLs.

---

### 5. "Expliquez votre schéma de base de données et les clés étrangères."

**Réponse :** Notre schéma suit les règles de la 3ème forme normale :
- `products.user_id` → FK vers `users.id` (le vendeur)
- `products.category_id` → FK vers `categories.id`
- `order_items.order_id` + `order_items.product_id` → FKs vers leurs tables respectives
- `reviews` a une contrainte UNIQUE sur `(user_id, product_id)` pour éviter qu'un utilisateur note deux fois le même produit
- `categories.parent_id` → FK auto-référentielle pour les sous-catégories

---

### 6. "Comment fonctionne votre API REST ?"

**Réponse :** L'API utilise Laravel Sanctum pour l'authentification par token. Les routes publiques (`GET /api/products`, `GET /api/products/{id}`) ne nécessitent pas d'authentification. Les routes protégées utilisent le middleware `auth:sanctum` qui vérifie le token Bearer dans le header `Authorization`. Les réponses sont en JSON avec des codes HTTP appropriés (200, 201, 403, 404). Les controllers API héritent du `Controller` de base et utilisent les mêmes Policies que l'application web.

---

### 7. "Comment gérez-vous la cohérence des stocks lors d'une commande ?"

**Réponse :** La commande est traitée dans une **transaction de base de données** (`DB::transaction()`). À l'intérieur, on utilise `lockForUpdate()` sur chaque produit pour éviter les conditions de concurrence. Si le stock est insuffisant, une exception est levée et la transaction est annulée en entier. Cette approche garantit qu'on ne peut jamais commander plus que le stock disponible, même avec des requêtes simultanées.

---

### 8. "Pourquoi avez-vous utilisé SoftDeletes sur Product ?"

**Réponse :** Si un produit est supprimé définitivement, les commandes qui contiennent ce produit perdraient leur référence (FK cassée, ou `NULL`). Avec SoftDeletes, le produit reste en base avec un timestamp `deleted_at`. Il n'apparaît plus dans le catalogue (`Product::active()` exclut les produits supprimés) mais les commandes historiques restent cohérentes et consultables.

---

### 9. "Expliquez la recherche AJAX."

**Réponse :** La barre de recherche envoie des requêtes `fetch()` au endpoint `/catalog/search?q=...` avec un délai de 300ms (debounce) pour éviter une requête à chaque touche. Côté serveur, `CatalogController::search()` utilise le scope `search()` défini sur le modèle Product, qui fait un `LIKE '%terme%'` sur le titre et la description. La réponse JSON est affichée dans un dropdown sous la barre. On a configuré `rate limiting` implicitement via la validation `min:2` caractères.

---

### 10. "Comment avez-vous réparti le travail en équipe ?"

**Réponse (adaptez selon votre réalité) :**
- Personne 1 : Backend core (auth, modèles, migrations, seeders, commandes, API REST, middleware, sécurité)
- Personne 2 : Features & UI (CRUD produits, upload, catalogue, AJAX, vues Blade, panel admin, wishlist)
- Revue croisée du code avant chaque push sur GitHub
- Branches Git séparées par fonctionnalité, merge sur `main` après validation
