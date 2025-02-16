<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Articles Management</title>
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1 class='mb-4'>Articles List</h1>
<a href='/articles/create' class='btn btn-primary mb-3'>Create Articles</a>
<table class='table'>
<thead class='table-light'><tr><th>id</th><th>nom</th><th>description</th><th>prix</th><th>quantite</th><th>created_at</th><th>updated_at</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($items as $item): ?>
<tr><td><?php echo htmlspecialchars($item['id']); ?></td><td><?php echo htmlspecialchars($item['nom']); ?></td><td><?php echo htmlspecialchars($item['description']); ?></td><td><?php echo htmlspecialchars($item['prix']); ?></td><td><?php echo htmlspecialchars($item['quantite']); ?></td><td><?php echo htmlspecialchars($item['created_at']); ?></td><td><?php echo htmlspecialchars($item['updated_at']); ?></td><td>
        <a href='/articles/show/<?php echo $item['id']; ?>' class='btn btn-info btn-sm'>Show</a>
        <a href='/articles/edit/<?php echo $item['id']; ?>' class='btn btn-warning btn-sm'>Edit</a>
        <form action='/articles/delete/<?php echo $item['id']; ?>' method='POST' class='d-inline'>
            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
        </form>
        </td></tr>
<?php endforeach; ?>
</tbody></table>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>