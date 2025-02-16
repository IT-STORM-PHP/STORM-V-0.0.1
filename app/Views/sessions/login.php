<?php

$_SESSION['errors'] = [];

?>

<form action='/login' method='post'>

    <div class='form-group'>

        <label for='login'>Identifiant</label>

        <input type='text' name='login' class='form-control' id='login' required>

    </div>

    <div class='form-group'>

        <label for='password'>Mot de passe</label>

        <input type='password' name='password' class='form-control' id='password' required>

    </div>

    <button type='submit' class='btn btn-primary'>Se connecter</button>

</form>