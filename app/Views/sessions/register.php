<?php

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