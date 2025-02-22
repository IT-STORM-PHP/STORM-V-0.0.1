<?php
$title = 'Show Livres';
ob_start();?>
<div class='container'><h1 class='mb-4'>Show Livres</h1>
<?php if (!empty($item)): ?>
<div class='card mb-4'>
<div class='card-body'>
<p><strong>id:</strong> <?php echo htmlspecialchars($item['id'] ?? 'N/A'); ?></p>
<p><strong>titre:</strong> <?php echo htmlspecialchars($item['titre'] ?? 'N/A'); ?></p>
<p><strong>annee_publication:</strong> <?php echo htmlspecialchars($item['annee_publication'] ?? 'N/A'); ?></p>
<p><strong>auteur_id:</strong> <?php echo htmlspecialchars($item['auteurs_nom'] ?? 'N/A'); ?></p>
<p><strong>categorie_id:</strong> <?php echo htmlspecialchars($item['categories_nom'] ?? 'N/A'); ?></p>
<p><strong>lieu_edition_id:</strong> <?php echo htmlspecialchars($item['lieu_edition_nom'] ?? 'N/A'); ?></p>
<p><strong>verifie:</strong> <?php echo htmlspecialchars($item['verifie'] ?? 'N/A'); ?></p>
<p><strong>archive:</strong> <?php echo htmlspecialchars($item['archive'] ?? 'N/A'); ?></p>
</div>
</div>
<?php else: ?>
<p class='text-danger'>Aucune donnée trouvée.</p>
<?php endif; ?>
<a href='/livres' class='btn btn-secondary'>Back to List</a>

</div><?php $content = ob_get_clean();?>