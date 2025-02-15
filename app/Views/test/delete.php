<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$model} Management</title>\
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1 class='text-danger'>Are you sure you want to delete this Test?</h1>
<form method='POST' action='/test/delete/<?php echo $item['id']; ?>'>
<button type='submit' class='btn btn-danger'>Yes, Delete</button>
<a href='/test' class='btn btn-secondary'>Cancel</a>
</form>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>