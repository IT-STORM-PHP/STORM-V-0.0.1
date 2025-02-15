<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$model} Management</title>\
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1 class='mb-4'>Test List</h1>
<a href='/test/create' class='btn btn-primary mb-3'>Create Test</a>
<table class='table'>
<thead class='table-light'><tr><th>id</th><th>name</th><th>deci</th><th>boul</th><th>created_at</th><th>updated_at</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($items as $item): ?>
<tr><td><?php echo htmlspecialchars($item['id']); ?></td><td><?php echo htmlspecialchars($item['name']); ?></td><td><?php echo htmlspecialchars($item['deci']); ?></td><td><?php echo htmlspecialchars($item['boul']); ?></td><td><?php echo htmlspecialchars($item['created_at']); ?></td><td><?php echo htmlspecialchars($item['updated_at']); ?></td><td>
        <a href='/test/show/<?php echo $item['id']; ?>' class='btn btn-info btn-sm'>Show</a>
        <a href='/test/edit/<?php echo $item['id']; ?>' class='btn btn-warning btn-sm'>Edit</a>
        <form action='/test/delete/<?php echo $item['id']; ?>' method='POST' class='d-inline'>
            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
        </form>
        </td></tr>
<?php endforeach; ?>
</tbody></table>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>