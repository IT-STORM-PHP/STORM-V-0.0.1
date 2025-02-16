<?php
$title = '<nom de votre page>';
ob_start();?>
<h1 class='mb-4'>Dynamique_views List</h1>
<a href='/dynamique_views/create' class='btn btn-primary mb-3'>Create Dynamique_views</a>
<table class='table'>
<thead class='table-light'><tr><th>id</th><th>nom</th><th>description</th><th>url</th><th>icon</th><th>color</th><th>created_at</th><th>updated_at</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($items as $item): ?>
<tr><td><?php echo htmlspecialchars($item['id']); ?></td><td><?php echo htmlspecialchars($item['nom']); ?></td><td><?php echo htmlspecialchars($item['description']); ?></td><td><?php echo htmlspecialchars($item['url']); ?></td><td><?php echo htmlspecialchars($item['icon']); ?></td><td><?php echo htmlspecialchars($item['color']); ?></td><td><?php echo htmlspecialchars($item['created_at']); ?></td><td><?php echo htmlspecialchars($item['updated_at']); ?></td><td>
        <a href='/dynamique_views/show/<?php echo $item['id']; ?>' class='btn btn-info btn-sm'>Show</a>
        <a href='/dynamique_views/edit/<?php echo $item['id']; ?>' class='btn btn-warning btn-sm'>Edit</a>
        <form action='/dynamique_views/delete/<?php echo $item['id']; ?>' method='POST' class='d-inline'>
            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
        </form>
        </td></tr>
<?php endforeach; ?>
</tbody></table>
<?php $content = ob_get_clean();?>
