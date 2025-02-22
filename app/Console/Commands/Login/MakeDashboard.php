<?php

namespace App\Console\Commands\Login;


class MakeDashboard
{
    // Dashboard avec bootstrap

    public function dashBoard (){
        $dashboard_with_bootstrap_menu = <<<DASHBOARD
        <?php
            \$title = 'Dashboard';
            ob_start();
            ?>
            <nav class="navbar navbar-dark bg-dark fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/dashboard">Dashboard</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Menu</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="/dashboard">Accueil</a>
                                </li>
                                <li class="nav-item">
                                    <?php if (isset(\$_SESSION['user'])): ?>
                                        <a class="nav-link" href="/logout">Se déconnecter</a>
                                    <?php else: ?>
                                        <a class="nav-link" href="/login/page">Se connecter</a>
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <form class="d-flex mt-3" role="search">
                                <input class="form-control me-2" type="search" placeholder="Rechercher" aria-label="Rechercher">
                                <button class="btn btn-success" type="submit">Rechercher</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container mt-5 pt-5">
                <h1 class="text-center">Bienvenue sur votre Dashboard</h1>
                
                <?php if (isset(\$_SESSION['user'])): ?>
                    <p class="text-center">Bonjour <?= \$_SESSION['user']['email'] ?></p>
                <?php endif; ?>
                
                <p class="text-center">Vous pouvez gérer vos données ici</p>
            </div>
            <?php
                    #var_dump(\$usr);
                    \$content = ob_get_clean();
            ?>
        DASHBOARD;

        // Définition du chemin
        $cheminDossier = __DIR__ . '/../../../Views/dash';
        $cheminFichier = $cheminDossier . '/dashboard.php';

        // Créer le dossier s'il n'existe pas
        if (!is_dir($cheminDossier)) {
            mkdir($cheminDossier, 0755, true);
        }

        // Écrire le fichier
        if (file_put_contents($cheminFichier, $dashboard_with_bootstrap_menu) !== false) {
            echo("Dashboard créé avec succès : $cheminFichier");
        } else {
            echo("Erreur lors de la création du dashboard !");
        }
    }
    
}
