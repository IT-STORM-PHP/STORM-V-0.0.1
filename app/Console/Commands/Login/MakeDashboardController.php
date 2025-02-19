<?php


namespace App\Console\Commands\Login;

class MakeDashboardController
{

    public function makeDashboardController($name = 'DashboardController')
    {
        $chemin_dossier = __DIR__ . "/../../../Controllers/Dashboard/";
        $name = ucfirst($name);
        $path = $chemin_dossier . $name . '.php';
        $content = "<?php\n\n";
        $content .= "namespace App\Controllers\Dashboard;\n\n";
        $content .= "use App\Controllers\Controller;\n";
        $content .= "use App\Views\View;\n";
        $content .= "use App\Auth\Auth;\n\n";
        $content .= "class $name extends Controller\n{\n";
        
        /**
         * Génération de la méthode dashboard avec un commentaire explicatif
         */
        $content .= "    /**\n";
        $content .= "     * Affiche la page du tableau de bord.\n";
        $content .= "     * Vérifie que l'utilisateur est connecté avant d'afficher la page.\n";
        $content .= "     * \n";
        $content .= "     * @return mixed\n";
        $content .= "     */\n";
        $content .= "    public function dashboard()\n";
        $content .= "    {\n";
        $content .= "        \$user = Auth::user();\n";
        $content .= "        Auth::requireAuth('/login/page'); // Vérifie que l'utilisateur est bien connecté\n";
        $content .= "        return View::render('dash/dashboard', ['usr'=>\$user]);\n";
        $content .= "    }\n\n";
        
        /**
         * Génération de la méthode isConnect avec un commentaire explicatif
         */
        $content .= "    /**\n";
        $content .= "     * Vérifie si l'utilisateur est connecté.\n";
        $content .= "     * Si ce n'est pas le cas, il est redirigé vers la page de connexion.\n";
        $content .= "     * \n";
        $content .= "     * @return mixed\n";
        $content .= "     */\n";
        $content .= "    public function isConnect()\n";
        $content .= "    {\n";
        $content .= "        if (!Auth::check()) {\n";
        $content .= "            return View::redirect('/login/page'); // Redirection si l'utilisateur n'est pas connecté\n";
        $content .= "        }\n";
        $content .= "    }\n\n";
        
        /**
         * Génération de la méthode logout avec un commentaire explicatif
         */
        $content .= "    /**\n";
        $content .= "     * Déconnecte l'utilisateur en cours et détruit sa session.\n";
        $content .= "     * Après la déconnexion, il est redirigé vers la page de connexion.\n";
        $content .= "     * \n";
        $content .= "     * @return mixed\n";
        $content .= "     */\n";
        $content .= "    public function logout()\n";
        $content .= "    {\n";
        $content .= "        Auth::logout(); // Supprime les informations de session\n";
        $content .= "        return View::redirect('/login/page'); // Redirection après la déconnexion\n";
        $content .= "    }\n";
        $content .= "}\n";        

        //Vériier si le dossier existe
        if (!file_exists($chemin_dossier)) {
            mkdir($chemin_dossier, 0777, true);
        }

        file_put_contents($path, $content);


        // affichage d'un message de succès dans la console après la création du fichier avec location du fichier
        echo "\033[32mLe fichier a été créé avec succès dans le dossier $path\033[0m\n";
        
    }
}
