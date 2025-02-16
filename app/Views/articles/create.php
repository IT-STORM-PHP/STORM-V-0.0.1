<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Articles Management</title>
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1>Create Articles</h1>
<form method='POST' action='/articles/store' class='mt-4'>
<div class='mb-3'><label class='form-label'>nom</label><input type='text' name='nom' class='form-control'></div><div class='mb-3'><label class='form-label'>description</label><input type='text' name='description' class='form-control'></div><div class='mb-3'><label class='form-label'>prix</label><input type='number' name='prix' class='form-control'></div><div class='mb-3'><label class='form-label'>quantite</label><input type='number' name='quantite' class='form-control'></div><button type='submit' class='btn btn-success'>Create Articles</button>
</form>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>