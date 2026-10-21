# TOP ÉLÉGANCE

Site web de la marque TOP ÉLÉGANCE (Lomé, Togo).
Technologies : PHP 8, Laravel, Blade, Tailwind CSS, Alpine.js, MySQL.

## Prérequis

Installés sur ton ordinateur : Git, PHP 8, Composer, Node.js, MySQL.

## Installation locale

1. Cloner le dépôt :
```bash
   git clone <url-du-depot>
   cd top-elegance
```
2. Installer les dépendances PHP et JavaScript :
```bash
   composer install
   npm install
```
3. Créer ton fichier de configuration personnel :
```bash
   cp .env.example .env
   php artisan key:generate
```
4. Dans MySQL, créer une base de données nommée `top_elegance`, puis renseigner
   `DB_USERNAME` et `DB_PASSWORD` dans ton fichier `.env` (pas dans `.env.example`).
5. Créer les tables :
```bash
   php artisan migrate
```
6. Lancer le site, dans deux terminaux séparés :
```bash
   npm run dev
   php artisan serve
```
   Le site est alors disponible sur http://127.0.0.1:8000

## Règles de travail

Voir le fichier CONTRIBUTING.md.