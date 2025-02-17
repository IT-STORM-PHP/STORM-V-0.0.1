# Documentation de la classe View


 La classe View est une classe utilitaire permettant de gérer l'affichage des vues, les redirections, les réponses JSON, les messages flash, la gestion des cookies, les pages d'erreur et la pagination.
___

```markdown
# Ceci est une documentation de la classe actue view et les méthodes actuelle
```



## 1. Affichage des vues

### `render($path, $dt = [])`
Affiche une vue dynamique. \
**Paramètres :**
- `$path` (string) : Le chemin de la vue (sans l'extension `.php`).
- `$dt` (array) : Les données à passer à la vue sous forme de tableau associatif.

**Exemple :**
```php
View::render('home', ['title' => 'Accueil']);
```
## 2. Redirection

### `redirect($url)`
Redirige l'utilisateur vers une URL donnée. \
**Paramètre :**
- `$url` (string) : L'URL vers laquelle rediriger.


**Exemple :**
```php
#adresse externe 
View::redirect('https://example.com');
#adrrese en local
View::redirect('/<nom_route>');
```

## 3. Réponse JSON

### `jsonResponse($data, $status = 200)`
Retourne une réponse JSON structurée avec un code HTTP spécifique.

**Paramètres :**
- `$data` (array) : Les données à encoder en JSON.
- `$status` (int) : Le code HTTP de réponse (par défaut 200).

**Exemple :**
```php
    View::jsonResponse(['message' => 'Succès'], 200);
```



## 4. Gestion des messages flash (sessions)

### `setFlash($key, $message)`
Stocke un message flash en session.

**Paramètres :**
- `$key` (string) : La clé du message.
- `$message` (string) : Le message à stocker.

**Exemple :**
```php
    View::setFlash('success', 'Opération réussie !');
```

## 5. Gestion des cookies

### `setCookie($name, $value, $expire = 3600, $path = "/", $secure = false, $httponly = true)`
Définit un cookie sécurisé.

**Paramètres :**
- `$name` (string) : Nom du cookie.
- `$value` (string) : Valeur du cookie.
- `$expire` (int) : Durée en secondes (par défaut 3600).
- `$path` (string) : Chemin de validité du cookie.
- `$secure` (bool) : true si le cookie ne doit être transmis que via HTTPS.
- `$httponly` (bool) : true si le cookie doit être accessible uniquement via HTTP.

**Exemple :**

```php
    View::setCookie('user', 'JohnDoe', 86400);
```

## 6. Gestion des erreurs

### `renderErrorPage($code = 404, $message = "Page not found")`

Affiche une page d'erreur et définit le code HTTP.

**Paramètres :**
- `$code` (int) : Code HTTP de l'erreur (ex: 404, 500).
- `$message` (string) : Message d'erreur à afficher.

**Exemple :**
```php
    View::renderErrorPage(403, "Accès interdit");
```
## 7. Téléchargement de fichiers

### `downloadFile($filePath, $fileName = null)`
Permet de télécharger un fichier.
**Paramètres :**
- `$filePath` (string) : Chemin du fichier à télécharger.
- `$fileName` (string|null) : Nom du fichier à afficher lors du téléchargement.

**Exemple :**
```php
    View::downloadFile('/path/to/file.pdf', 'document.pdf');
```
## 8. Pagination

### `paginate($items, $page = 1, $perPage = 10)`
Paginate un tableau de données.
**Paramètres :**
- `$items` (array) : Liste des éléments à paginer.
- `$page` (int) : Numéro de la page actuelle.
- `$perPage` (int) : Nombre d'éléments par page.

**Exemple :**
```php
    $data = range(1, 100); // Tableau de 100 éléments
$pagination = View::paginate($data, 2, 10);

print_r($pagination);
```
