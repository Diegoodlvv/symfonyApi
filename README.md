# API Jeux Vidéo – Symfony 8

API REST développée avec **Symfony 8.1** permettant de gérer un catalogue de jeux vidéo, leurs éditeurs, leurs catégories ainsi que des utilisateurs. L'API est sécurisée par **JWT**, documentée avec **Swagger (NelmioApiDoc)** et envoie chaque semaine une **newsletter** listant les jeux qui sortent dans les 7 prochains jours.

---

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Authentification JWT](#authentification-jwt)
- [Documentation de l'API](#documentation-de-lapi)
- [Endpoints](#endpoints)
- [Newsletter hebdomadaire](#newsletter-hebdomadaire)
- [Modèle de données](#modèle-de-données)
- [Structure du projet](#structure-du-projet)

---

## Fonctionnalités

- **CRUD complet** sur 4 ressources : jeux vidéo, éditeurs, catégories, utilisateurs
- **Pagination** sur toutes les listes (`?page=1&limit=10`)
- **Authentification stateless par JWT** (LexikJWTAuthenticationBundle)
- **Sérialisation par groupes** (`*:read` / `*:write`) pour contrôler les champs exposés
- **Validation** des données (Symfony Validator)
- **Upload d'une image de couverture** pour les jeux vidéo
- **Gestion centralisée des erreurs** : toutes les exceptions sont renvoyées en JSON (`{ "status": ..., "message": ... }`)
- **Abonnement à la newsletter** par utilisateur
- **Envoi automatique d'une newsletter** chaque lundi à 8h30 (Symfony Scheduler + Mailer + Twig)
- **Documentation interactive** Swagger UI générée automatiquement (OpenAPI)
- **Fixtures** pour peupler la base avec des données de test

## Stack technique

| Outil | Version / rôle |
|---|---|
| PHP | ≥ 8.4 |
| Symfony | 8.1 |
| Doctrine ORM / Migrations | 3.x / 4.x |
| Base de données | MySQL / MariaDB (configurée pour MariaDB 10.4 via XAMPP) |
| LexikJWTAuthenticationBundle | Authentification JWT |
| NelmioApiDocBundle | Documentation OpenAPI / Swagger |
| Symfony Mailer + Twig | Envoi de la newsletter |
| Symfony Scheduler / Messenger | Tâche planifiée hebdomadaire |
| DoctrineFixturesBundle | Données de test (dev) |

## Prérequis

- PHP 8.4+ avec les extensions `ctype`, `iconv`, `openssl`, `pdo_mysql`
- [Composer](https://getcomposer.org/)
- MySQL ou MariaDB (ex. XAMPP)
- [Symfony CLI](https://symfony.com/download) (recommandé)
- OpenSSL (pour générer les clés JWT)
- Un compte SMTP de test, par exemple [Mailtrap](https://mailtrap.io/) (pour la newsletter)

## Installation

### 1. Cloner le projet et installer les dépendances

```bash
git clone <url-du-repo> symfonyApi
cd symfonyApi
composer install
```

### 2. Configurer l'environnement

Le fichier `.env` n'est pas versionné. Créez un fichier `.env.local` (ou `.env`) avec vos propres valeurs :

```dotenv
APP_ENV=dev
APP_SECRET=une_chaine_secrete

DATABASE_URL="mysql://root:@127.0.0.1:3306/projet_apiSF?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase

MAILER_DSN=smtp://USER:PASSWORD@sandbox.smtp.mailtrap.io:2525
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
```

### 3. Générer les clés JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

Les clés sont créées dans `config/jwt/` (ignorées par Git). La passphrase utilisée doit correspondre à `JWT_PASSPHRASE`.

### 4. Créer la base de données et lancer les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Charger les données de test (optionnel)

```bash
php bin/console doctrine:fixtures:load
```

Cela crée des catégories, des éditeurs, des jeux vidéo et 3 utilisateurs (`diego.dlvv@example.com`, `jane.doe@example.com`, `john.doe@example.com`).

### 6. Créer le dossier d'upload des couvertures

```bash
mkdir -p public/uploads/covers
```

### 7. Lancer le serveur

```bash
symfony serve
# ou
php -S localhost:8000 -t public
```

L'API est disponible sur `http://localhost:8000`.

## Authentification JWT

Toutes les routes `/api/*` nécessitent un token, **sauf** :

- `/api/login_check` (connexion)
- `/api/doc*` (documentation)
- `/api/editors` (liste des éditeurs)

### Obtenir un token

```bash
curl -X POST http://localhost:8000/api/login_check \
  -H "Content-Type: application/json" \
  -d '{"username": "diego.dlvv@example.com", "password": "votre_mot_de_passe"}'
```

Réponse :

```json
{ "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..." }
```

### Utiliser le token

```bash
curl http://localhost:8000/api/videogames \
  -H "Authorization: Bearer <token>"
```

## Documentation de l'API

| URL | Contenu |
|---|---|
| `/` | Interface Swagger UI interactive |
| `/api/doc.json` | Spécification OpenAPI au format JSON |
| `/api/doc.yaml` | Spécification OpenAPI au format YAML |

Dans Swagger UI, cliquez sur **Authorize** et collez votre token JWT pour tester les routes protégées.

## Endpoints

Les routes de lecture sont sous `/api/...`, les routes d'écriture sous `/api/v1/...`.
Toutes les listes acceptent les paramètres `page` (défaut : 1) et `limit` (défaut : 10).

### Jeux vidéo

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/videogames` | Liste paginée (triée par titre) |
| GET | `/api/videogames/{id}` | Détail d'un jeu (avec éditeur et catégories) |
| POST | `/api/v1/videogames` | Créer un jeu (+ upload `coverImage` optionnel) |
| PUT | `/api/v1/videogames/{id}` | Modifier un jeu |
| DELETE | `/api/v1/videogames/{id}/delete` | Supprimer un jeu |

### Éditeurs

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/editors` | Liste paginée (**publique**) |
| GET | `/api/editor/{id}` | Détail d'un éditeur |
| POST | `/api/v1/editor` | Créer un éditeur |
| PUT | `/api/v1/editor/{id}` | Modifier un éditeur |
| DELETE | `/api/v1/editor/{id}/delete` | Supprimer un éditeur |

### Catégories

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/categories` | Liste paginée |
| GET | `/api/category/{id}` | Détail d'une catégorie |
| POST | `/api/v1/category` | Créer une catégorie |
| PUT | `/api/v1/category/{id}` | Modifier une catégorie |
| DELETE | `/api/v1/category/{id}/delete` | Supprimer une catégorie |

### Utilisateurs

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/users` | Liste paginée |
| GET | `/api/users/{id}` | Détail d'un utilisateur |
| POST | `/api/v1/users` | Créer un utilisateur |
| PUT | `/api/v1/users/{id}` | Modifier un utilisateur |
| PATCH | `/api/v1/users/{id}/subscription` | Modifier l'abonnement à la newsletter |
| DELETE | `/api/v1/users/{id}/delete` | Supprimer un utilisateur |

### Exemples de corps de requête

```json
// POST /api/v1/editor
{ "name": "Nintendo", "country": "Japon" }

// POST /api/v1/category
{ "name": "RPG" }

// PATCH /api/v1/users/1/subscription
{ "subscriptionToNewsletter": true }
```

### Format des erreurs

```json
{ "status": 404, "message": "\"App\\Entity\\Editor\" object not found by \"EntityValueResolver\"." }
```

## Newsletter hebdomadaire

La commande `app:send-newsletter` récupère les jeux vidéo dont la date de sortie est comprise entre aujourd'hui et J+7, puis envoie un email HTML (template `templates/newsletter/newsletter.html.twig`) à tous les utilisateurs abonnés.

**Lancement manuel :**

```bash
php bin/console app:send-newsletter
```

**Exécution automatique :** la commande est planifiée via `#[AsCronTask('30 8 * * 1', 'Europe/Paris')]`, soit **chaque lundi à 8h30**. Pour que le scheduler tourne, lancez le worker :

```bash
php bin/console messenger:consume scheduler_default
```

En développement, les emails peuvent être consultés dans la boîte de test Mailtrap.

## Modèle de données

```
Editor (1) ────< (N) VideoGame (N) >────< (N) Category

User (indépendant)
```

| Entité | Champs principaux |
|---|---|
| **VideoGame** | `title`, `releaseDate`, `description`, `coverImage`, `editor` (ManyToOne), `category` (ManyToMany) |
| **Editor** | `name`, `country` |
| **Category** | `name` |
| **User** | `email` (unique), `password` (hashé), `roles`, `subscriptionToNewsletter` |

## Structure du projet

```
src/
├── Command/          # SendNewsletterCommand (newsletter planifiée)
├── Controller/       # Contrôleurs REST (VideoGame, Editor, Category, User)
├── DataFixtures/     # Données de test
├── Entity/           # Entités Doctrine
├── EventSubscriber/  # ExceptionListener (erreurs en JSON)
├── Repository/       # Requêtes (pagination, jeux à venir, abonnés)
└── Service/          # MailerService (envoi des emails)
config/
├── jwt/              # Clés JWT (non versionnées)
└── packages/         # Configuration des bundles
migrations/           # Migrations Doctrine
templates/newsletter/ # Template de l'email
```

## Commandes utiles

```bash
php bin/console debug:router          # Lister toutes les routes
php bin/console cache:clear           # Vider le cache
php bin/console make:migration        # Générer une migration après modification d'entité
php bin/console app:send-newsletter   # Envoyer la newsletter manuellement
```
