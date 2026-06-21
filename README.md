# ALLPNEU 84 — Installation MAMP + Railway

## Installation locale (MAMP)

### 1. Démarrer MAMP
- Ouvrez MAMP
- Cliquez "Start" pour démarrer Apache + MySQL
- Port Apache : 8888, Port MySQL : 8889

### 2. Créer le dossier
Copiez ce dossier dans :
```
/Applications/MAMP/htdocs/allpneu/
```

### 3. Créer la base de données
- Ouvrez http://localhost:8888/phpMyAdmin
- Cliquez "Nouveau" → créez la base `allpneu`
- Importez le fichier `database.sql` (onglet Importer)

### 4. Vérifier la config
Ouvrez `config.php` et vérifiez :
- `DB_HOST` = localhost
- `DB_PORT` = 8889 (MAMP par défaut)
- `DB_USER` = root
- `DB_PASS` = root

### 5. Ouvrir l'appli
Allez sur : http://localhost:8888/allpneu/

---

## Déploiement Railway (accès en ligne)

### 1. Créer un compte GitHub
- https://github.com → Sign up (gratuit)

### 2. Créer un dépôt
- New repository → `allpneu`
- Uploadez tous les fichiers de ce dossier

### 3. Créer un compte Railway
- https://railway.app → Login with GitHub (gratuit)

### 4. Nouveau projet
- New Project → Deploy from GitHub repo → sélectionnez `allpneu`

### 5. Ajouter MySQL
- Dans Railway : Add Plugin → MySQL
- Notez les variables : MYSQL_HOST, MYSQL_PORT, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE

### 6. Adapter config.php pour Railway
Remplacez les defines dans config.php par :
```php
define('DB_HOST', getenv('MYSQL_HOST'));
define('DB_PORT', getenv('MYSQL_PORT') ?: '3306');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'allpneu');
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASS', getenv('MYSQL_PASSWORD'));
```

### 7. Importer la base
Dans Railway → MySQL → Connect → importez database.sql

### 8. Accéder à l'appli
Railway génère une URL du type : https://allpneu-production.up.railway.app

---

## Structure des fichiers
```
allpneu/
├── index.html      → Application ALLPNEU
├── api.php         → API REST (CRUD)
├── config.php      → Connexion base de données
├── database.sql    → Structure MySQL
├── .htaccess       → Config Apache
└── README.md       → Ce fichier
```
