<?php
<<<<<<< HEAD

$_SESSION['errors'] = [];

?>

<form action='/register' method='post'>

    <div class='form-group'>

        <label for='login'>Identifiant</label>

        <input type='text' name='login' class='form-control' id='login' required>

    </div>

    <div class='form-group'>

        <label for='password'>Mot de passe</label>

        <input type='password' name='password' class='form-control' id='password' required>

    </div>

    <div class='form-group'>

        <label for='confirm_password'>Confirmer le mot de passe</label>

        <input type='password' name='confirm_password' class='form-control' id='confirm_password' required>

    </div>

    <button type='submit' class='btn btn-primary'>S'inscrire</button>

</form>
=======
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
        <h4>Inscription</h4>
    </div>
    <div class='card-body'>
        <form action='/register' method='post'>
            <div class='mb-3'>
                <label for='nom' class='form-label'>nom</label>
                <input type='text' name='nom' class='form-control' required>
            </div>
            <div class='mb-3'>
                <label for='prenom' class='form-label'>prenom</label>
                <input type='text' name='prenom' class='form-control' required>
            </div>
            <div class='mb-3'>
                <label for='email' class='form-label'>email</label>
                <input type='text' name='email' class='form-control' required>
            </div>
            <div class='mb-3'>
                <label for='password' class='form-label'>password</label>
                <input type='text' name='password' class='form-control' required>
            </div>
            <div class='mb-3'>
                <label for='role' class='form-label'>role</label>
                <input type='text' name='role' class='form-control' required>
            </div>
            <button type='submit' class='btn btn-primary w-100 py-2'>S'inscrire</button>
        </form>
    </div>
</div>
</div>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>
>>>>>>> Godwill05_test
