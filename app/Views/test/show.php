<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$model} Management</title>\
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1 class='mb-4'>Show Test</h1>
<?php if (!empty($item)): ?>
<div class='card mb-4'>
<div class='card-body'>
<p><strong>id:</strong> <?php echo htmlspecialchars($item['id'] ?? 'N/A'); ?></p>
<p><strong>name:</strong> <?php echo htmlspecialchars($item['name'] ?? 'N/A'); ?></p>
<p><strong>deci:</strong> <?php echo htmlspecialchars($item['deci'] ?? 'N/A'); ?></p>
<p><strong>boul:</strong> <?php echo htmlspecialchars($item['boul'] ?? 'N/A'); ?></p>
<p><strong>created_at:</strong> <?php echo htmlspecialchars($item['created_at'] ?? 'N/A'); ?></p>
<p><strong>updated_at:</strong> <?php echo htmlspecialchars($item['updated_at'] ?? 'N/A'); ?></p>
</div>
</div>
<?php else: ?>
<p class='text-danger'>Aucune donnée trouvée.</p>
<?php endif; ?>
<a href='/test' class='btn btn-secondary'>Back to List</a>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>