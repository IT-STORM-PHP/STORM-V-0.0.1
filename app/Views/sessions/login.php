<?php
$_SESSION['errors'] = [];
?>
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Authentification Management</title>
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'><div class='container d-flex justify-content-center align-items-center vh-100'>
    <div class='card shadow-lg' style='max-width: 500px; width: 100%;'>
        <div class='card-header bg-primary text-white text-center'>
            <h4>Connexion</h4>
        </div>
        <div class='card-body'>
            <form action='/login' method='post'>
                <div class='mb-3'>
                    <label for='email' class='form-label'>Email</label>
                    <input type='text' name='email' class='form-control' id='email' required>
                </div>
                <div class='mb-3'>
                    <label for='password' class='form-label'>Mot de passe</label>
                    <input type='password' name='password' class='form-control' id='password' required>
                </div>
                <button type='submit' class='btn btn-primary w-100 py-2'>Se connecter</button>
            </form>
        </div>
    </div>
</div>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>