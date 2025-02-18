# Documentation de la classe `Auth`

## 📌 Introduction
La classe `Auth` gère l'authentification des utilisateurs dans le projet Storm. 
Elle permet de vérifier si un utilisateur est connecté, de récupérer ses informations, 
de gérer les sessions et les rôles, ainsi que d'appliquer des restrictions d'accès.

## 📜 Méthodes disponibles

### 1. `check() : bool`
**Vérifie si un utilisateur est connecté.**

#### Exemple d'utilisation :
```php
if (Auth::check()) {
    echo "Utilisateur connecté";
} else {
    echo "Non connecté";
}
```

---

### 2. `user() : ?array`
**Récupère les informations de l'utilisateur connecté.**

#### Exemple d'utilisation :
```php
$user = Auth::user();
if ($user) {
    echo "Nom : " . $user['name'];
} else {
    echo "Aucun utilisateur connecté";
}
```

---

### 3. `login(array $user)`
**Connecte un utilisateur en enregistrant ses données en session.**

#### Paramètres :
- `$user` *(array)* : Tableau contenant les données de l'utilisateur.

#### Exemple d'utilisation :
```php
$userData = [
    'id' => 1,
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'role' => 'admin'
];
Auth::login($userData);
```

---

### 4. `logout()`
**Déconnecte l'utilisateur et détruit la session.**

#### Exemple d'utilisation :
```php
Auth::logout();
```

---

### 5. `hasRole(string $role) : bool`
**Vérifie si l'utilisateur a un rôle spécifique.**

#### Paramètres :
- `$role` *(string)* : Nom du rôle à vérifier (ex : 'admin', 'user').

#### Exemple d'utilisation :
```php
if (Auth::hasRole('admin')) {
    echo "L'utilisateur est un administrateur";
} else {
    echo "Accès refusé";
}
```

---

### 6. `requireAuth(string $redirectUrl = '/login/page')`
**Redirige l'utilisateur vers une page de connexion s'il n'est pas authentifié.**

#### Paramètres :
- `$redirectUrl` *(string, optionnel)* : URL de redirection si l'utilisateur n'est pas connecté.

#### Exemple d'utilisation :
```php
Auth::requireAuth('/login/page');
```

---

## 🎯 Pourquoi utiliser `Auth` ?
✔ **Sécurise l'authentification** en évitant la duplication de code.
✔ **Facilite la gestion des sessions** et des utilisateurs.
✔ **Permet une meilleure organisation** du projet.

🚀 **Prêt à être intégré dans Storm !**
