<?php
$title = 'Bienvenue sur STORM';
ob_start();
?>
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
                    <p class="card-text">Utilisez la commande <span class="cli-command">php storm make:crud
                            <strong>nom_model</strong></span> pour créer un modèle avec les méthodes CRUD
                        automatiquement.</p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn" data-clipboard-text="php storm make:crud nom_model">Copier</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Exécuter une migration</h5>
                    <p class="card-text">La commande <span class="cli-command">php storm migrate</span> exécute les
                        migrations de base de données et applique les modifications.</p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn" data-clipboard-text="php storm migrate">Copier</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Créer une migration</h5>
                    <p class="card-text">Pour créer une migration, utilisez <span class="cli-command">php storm
                            make:migrations <strong>nom_table</strong></span>.</p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn" data-clipboard-text="php storm make:migrations nom_table">Copier</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Lancer le serveur</h5>
                    <p class="card-text">Pour lancer le serveur local sur le port 8000, utilisez <span
                            class="cli-command">php storm serve</span>.</p>
                    <p class="card-text">Pour lancer le serveur sur un port et une adresse spécifique, utilisez <span
                            class="cli-command">php storm serve <strong>--host=[@IP]</strong>
                            <strong>--port=[numPort]</strong></span>.</p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn"
                    data-clipboard-text="php storm serve --host=[@IP] --port=[numPort]">Copier</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Annulation de migration</h5>
                    <p class="card-text">La commande <span class="cli-command">php storm rollback</span> annule la
                        dernière migration effectuée.</p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn" data-clipboard-text="php storm rollback">Copier</button>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Créer un nouveau contrôleur</h5>
                    <p class="card-text">Utilisez la commande <span class="cli-command">php storm make:controllers
                            <strong>nom_controller</strong></span>.</p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn" data-clipboard-text="php storm make:controllers nom_controller">Copier</button>
            </div>
        </div>
        <!-- Ajout de la nouvelle carte pour l'authentification -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Créer un système d'authentification</h5>
                    <p class="card-text">Pour créer un système d'authentification, utilisez la commande <span
                            class="cli-command">php storm make:login</span> et suivez les instructions dans le terminal.
                    </p>
                    <a href="#" class="btn btn-primary">En savoir plus</a>
                </div>
                <button class="copy-btn" data-clipboard-text="php storm make:login">Copier</button>
            </div>
        </div>
    </div>

</section>

<!-- Section d'information -->
<section class="container my-5">
    <div class="row text-center">
        <div class="col-lg-6">
            <h3 class="">Développez plus vite avec STORM</h3>
            <p>STORM vous aide à accélérer vos processus de développement en automatisant les tâches récurrentes, comme
                la création de modèles, migrations et contrôleurs.</p>
        </div>
        <div class="col-lg-6">
            <h3 class="">Facilitez la gestion de votre code</h3>
            <p>Grâce à des commandes simples et puissantes, STORM rend la gestion de votre application plus fluide et
                organisée, vous permettant de vous concentrer sur ce qui compte vraiment.</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="text-center py-4" style="background-color: #004F71; color: white;">
    <p>&copy; 2025 STORM. Tous droits réservés.</p>
</footer>
<?php
$content = ob_get_clean();
?>