<?php
$title = 'STORM - Login';
ob_start();
$_SESSION['errors'] = [];
?>
<div class='container d-flex justify-content-center align-items-center vh-100'>
    <div class='card shadow-lg' style='max-width: 500px; width: 100%;'>
        <div class='card-header bg-primary text-white text-center'>
            <h4>Connexion</h4>
        </div>
        <div class='card-body'>
            <form action='/login' method='post'>
                <div class='mb-3'>
                    <label for='username' class='form-label'>Username</label>
                    <input type='text' name='username' class='form-control' id='username' required>
                </div>
                <div class='mb-3'>
                    <label for='password' class='form-label'>Mot de passe</label>
                    <input type='password' name='password' class='form-control' id='password' required>
                </div>
                <button type='submit' class='btn btn-primary w-100 py-2'>Se connecter</button>
            </form>
            <p class='text-center mt-3'>Vous n'avez pas de compte ? <a href='/register/page'>Inscrivez-vous</a></p>
        </div>
    </div>
</div>
<?php
	$content = ob_get_clean();
?>