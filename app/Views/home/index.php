<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur STORM</title>
    <!-- Inclusion de Bootstrap 5 -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .main-header {
            background-color: #004F71; /* 0xFF004F71 en hexadécimal */
            color: white;
            padding: 60px 0;
        }
        .main-header h1 {
            font-size: 3rem;
        }
        .card {
            border-radius: 15px;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .card .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #b0b0b0; /* Couleur gris clair */
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            cursor: pointer;
            display: none;
            border: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        .card:hover .copy-btn {
            display: block;
            opacity: 1;
        }
        .card:hover .copy-btn:hover {
            background-color: #a0a0a0; /* Couleur légèrement plus foncée au survol */
        }
        .commands .card-body {
            font-size: 1.1rem;
        }
        .cli-command {
            color: #004F71; /* Commandes en bleu */
            font-weight: bold;
        }
        h3 ,#h2{
            color: #004F71; 
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- En-tête -->
    <header class="main-header text-center">
        <div class="container">
            <h1 class="display-4">Bienvenue sur STORM</h1>
            <p class="lead">Voici un aperçu des commandes que vous pouvez utiliser dans notre application.</p>
        </div>
    </header>

    <!-- Section des commandes -->
    <section class="container my-5">
        <h2 class="text-center  mb-4" id="h2">Quelques commandes disponibles</h2>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Créer un CRUD</h5>
                        <p class="card-text">Utilisez la commande <span class="cli-command">php storm make:crud <strong>nom_model</strong></span> pour créer un modèle avec les méthodes CRUD automatiquement.</p>
                        <a href="#" class="btn btn-primary">En savoir plus</a>
                    </div>
                    <button class="copy-btn" data-clipboard-text="php storm make:crud nom_model">Copier</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Exécuter une migration</h5>
                        <p class="card-text">La commande <span class="cli-command">php storm migrate</span> exécute les migrations de base de données et applique les modifications.</p>
                        <a href="#" class="btn btn-primary">En savoir plus</a>
                    </div>
                    <button class="copy-btn" data-clipboard-text="php storm migrate">Copier</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Créer une migration</h5>
                        <p class="card-text">Pour créer une migration, utilisez <span class="cli-command">php storm make:migrations <strong>nom_table</strong></span>.</p>
                        <a href="#" class="btn btn-primary">En savoir plus</a>
                    </div>
                    <button class="copy-btn" data-clipboard-text="php storm make:migrations nom_table">Copier</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section d'information -->
    <section class="container my-5">
    <div class="row text-center">
        <div class="col-lg-6">
            <h3 class="">Développez plus vite avec STORM</h3>
            <p>STORM vous aide à accélérer vos processus de développement en automatisant les tâches récurrentes, comme la création de modèles, migrations et contrôleurs.</p>
        </div>
        <div class="col-lg-6">
            <h3 class="">Facilitez la gestion de votre code</h3>
            <p>Grâce à des commandes simples et puissantes, STORM rend la gestion de votre application plus fluide et organisée, vous permettant de vous concentrer sur ce qui compte vraiment.</p>
        </div>
    </div>
</section>


    <!-- Footer -->
    <footer class="text-center py-4" style="background-color: #004F71; color: white;">
        <p>&copy; 2025 STORM. Tous droits réservés.</p>
    </footer>

    <!-- Inclusion de Bootstrap JS -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copier dans le presse-papier
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const text = button.getAttribute('data-clipboard-text');
                
                // Utilisation de l'API Clipboard pour copier
                navigator.clipboard.writeText(text).then(() => {
                    alert('Commande copiée : ' + text);
                }).catch(err => {
                    alert('Échec de la copie : ' + err);
                });
            });
        });
    </script>
</body>
</html>
