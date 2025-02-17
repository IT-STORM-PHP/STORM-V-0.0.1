 # Documentation de la classe View

La classe View est une classe utilitaire permettant de gérer l'affichage des vues, les redirections, les réponses JSON, les messages flash, la gestion des cookies, les pages d'erreur et la pagination.

---

## 1. Affichage des vues

### `render($path, $dt = [])`
Affiche une vue dynamique.

**Paramètres** :
- `$path` (string) : Le chemin de la vue (sans l'extension `.php`).
- `$dt` (array) : Les données à passer à la vue.

**Exemple** :
```php
View::render('home', ['title' => 'Accueil']);
