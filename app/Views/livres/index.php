<?php
$title = 'Livres List';
ob_start();?>

<div class='container'>
    <h1 class='mb-4'>Livres List</h1>
    <a href='/livres/create' class='btn btn-success mb-3'>Create Livres</a>
    <table class='table'>
    <thead class='table-light'><tr><th>id</th><th>titre</th><th>annee_publication</th><th>auteur_id</th><th>categorie_id</th><th>lieu_edition_id</th><th>verifie</th><th>archive</th><th>Actions</th></tr></thead><tbody>

<?php foreach ($items as $item): ?>
<tr><td><?php echo htmlspecialchars($item['id']); ?></td><td><?php echo htmlspecialchars($item['titre']); ?></td><td><?php echo htmlspecialchars($item['annee_publication']); ?></td><td><?php echo htmlspecialchars($item['auteurs_nom'] ?? 'N/A'); ?></td><td><?php echo htmlspecialchars($item['categories_nom'] ?? 'N/A'); ?></td><td><?php echo htmlspecialchars($item['lieu_edition_nom'] ?? 'N/A'); ?></td><td><?php echo htmlspecialchars($item['verifie']); ?></td><td><?php echo htmlspecialchars($item['archive']); ?></td><td>
    <a href='/livres/show/<?php echo $item['id']; ?>' class='btn btn-dark btn-sm'>Show</a>
    <a href='/livres/edit/<?php echo $item['id']; ?>' class='btn btn-warning btn-sm'>Edit</a>
    <form action='/livres/delete/<?php echo $item['id']; ?>' method='POST' class='d-inline'>
        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
    </form>
</td></tr>
<?php endforeach; ?>
</tbody></table>
</div><?php $content = ob_get_clean();?>
