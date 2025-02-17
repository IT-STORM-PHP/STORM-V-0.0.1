# Routes Documentation

## 1. Définir des routes simples

```php
use App\Routes\Route;

Route::get('/home', function () {
    echo "Bienvenue sur la page d'accueil";
});

Route::get('/users/{id}', [UserController::class, 'show']);
``` 
## 2. Rediriger vers une méthode spécifique
``` php
    use App\Contoller\HomeController ;
    Route::post('/users/create', [UserController::class, 'create']);

```

## 3. Ajouter un middleware pour une route
```php
    use App\Contoller\HomeController;
    use App\Views\View;
    Route::beforeMiddleware(['/dashboard', '/uneautreroute'], function () {
    if (!isset($_SESSION['user'])) {
        
        return View::redirect('/login');
    }
});

Route::get('/dashboard', function () {
    echo "Bienvenue sur votre tableau de bord";
});
```
## 4. Deja élaboré(en cours de documentation)