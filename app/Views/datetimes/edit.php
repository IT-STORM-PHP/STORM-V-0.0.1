<?php
$title = '<nom de votre page>';
ob_start();?>
<h1>Edit Datetimes</h1>
<form method='POST' action='/datetimes/update/<?php echo $item['id']; ?>' class='mt-4'>
<div class='mb-3'><label class='form-label'>codAricle</label><input type='text' name='codAricle' value='<?php echo htmlspecialchars($item['codAricle']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>nomArticle</label><input type='text' name='nomArticle' value='<?php echo htmlspecialchars($item['nomArticle']); ?>' class='form-control'></div><button type='submit' class='btn btn-primary'>Update Datetimes</button>
</form>
<?php $content = ob_get_clean();?>