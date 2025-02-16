<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Articles Management</title>
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1>Edit Articles</h1>
<form method='POST' action='/articles/update/<?php echo $item['id']; ?>' class='mt-4'>
<div class='mb-3'><label class='form-label'>nom</label><input type='text' name='nom' value='<?php echo htmlspecialchars($item['nom']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>description</label><input type='text' name='description' value='<?php echo htmlspecialchars($item['description']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>prix</label><input type='number' name='prix' value='<?php echo htmlspecialchars($item['prix']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>quantite</label><input type='number' name='quantite' value='<?php echo htmlspecialchars($item['quantite']); ?>' class='form-control'></div><button type='submit' class='btn btn-primary'>Update Articles</button>
</form>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>