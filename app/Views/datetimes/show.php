<?php
$title = '<nom de votre page>';
ob_start();?>
<h1 class='mb-4'>Show Datetimes</h1>
<?php if (!empty($item)): ?>
<div class='card mb-4'>
<div class='card-body'>
<p><strong>id:</strong> <?php echo htmlspecialchars($item['id'] ?? 'N/A'); ?></p>
<p><strong>codArticle:</strong> <?php echo htmlspecialchars($item['codArticle'] ?? 'N/A'); ?></p>
<p><strong>nomArticle:</strong> <?php echo htmlspecialchars($item['nomArticle'] ?? 'N/A'); ?></p>
<p><strong>created_at:</strong> <?php echo htmlspecialchars($item['created_at'] ?? 'N/A'); ?></p>
<p><strong>updated_at:</strong> <?php echo htmlspecialchars($item['updated_at'] ?? 'N/A'); ?></p>
</div>
</div>
<?php else: ?>
<p class='text-danger'>Aucune donnée trouvée.</p>
<?php endif; ?>
<a href='/datetimes' class='btn btn-secondary'>Back to List</a>

\<?php $content = ob_get_clean();?>