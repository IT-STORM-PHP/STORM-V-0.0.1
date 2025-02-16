<?php
$title = '<nom de votre page>';
ob_start();?>
<h1 class='mb-4'>Show Dynamique_views</h1>
<?php if (!empty($item)): ?>
<div class='card mb-4'>
<div class='card-body'>
<p><strong>id:</strong> <?php echo htmlspecialchars($item['id'] ?? 'N/A'); ?></p>
<p><strong>nom:</strong> <?php echo htmlspecialchars($item['nom'] ?? 'N/A'); ?></p>
<p><strong>description:</strong> <?php echo htmlspecialchars($item['description'] ?? 'N/A'); ?></p>
<p><strong>url:</strong> <?php echo htmlspecialchars($item['url'] ?? 'N/A'); ?></p>
<p><strong>icon:</strong> <?php echo htmlspecialchars($item['icon'] ?? 'N/A'); ?></p>
<p><strong>color:</strong> <?php echo htmlspecialchars($item['color'] ?? 'N/A'); ?></p>
<p><strong>created_at:</strong> <?php echo htmlspecialchars($item['created_at'] ?? 'N/A'); ?></p>
<p><strong>updated_at:</strong> <?php echo htmlspecialchars($item['updated_at'] ?? 'N/A'); ?></p>
</div>
</div>
<?php else: ?>
<p class='text-danger'>Aucune donnée trouvée.</p>
<?php endif; ?>
<a href='/dynamique_views' class='btn btn-secondary'>Back to List</a>

\<?php $content = ob_get_clean();?>