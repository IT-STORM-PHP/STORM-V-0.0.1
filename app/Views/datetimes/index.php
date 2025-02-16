<?php
$title = '<nom de votre page>';
ob_start(); ?>
<h1 class='mb-4'>Datetimes List</h1>
<a href='/datetimes/create' class='btn btn-primary mb-3'>Create Datetimes</a>
<table class='table'>
    <thead class='table-light'>
        <tr>
            <th>id</th>
            <th>Code Article</th>
            <th>Nom Article</th>
            <th>Date de création</th>
            <th>Date de modification</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['id']); ?></td>
                <td><?php echo htmlspecialchars($item['codAricle']); ?></td>
                <td><?php echo htmlspecialchars($item['nomArticle']); ?></td>
                <td><?php echo htmlspecialchars($item['created_at']); ?></td>
                <td><?php echo htmlspecialchars($item['updated_at']); ?></td>
                <td>
                    <a href='/datetimes/show/<?php echo $item['id']; ?>' class='btn btn-info btn-sm'>Show</a>
                    <a href='/datetimes/edit/<?php echo $item['id']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                    <form action='/datetimes/delete/<?php echo $item['id']; ?>' method='POST' class='d-inline'>
                        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $content = ob_get_clean(); ?>