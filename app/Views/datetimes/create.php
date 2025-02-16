<?php
$title = '<nom de votre page>';
ob_start();?>
<h1>Create Datetimes</h1>
<form method='POST' action='/datetimes/store' class='mt-4'>
<div class='mb-3'><label class='form-label'>codAricle</label><input type='text' name='codAricle' class='form-control'></div><div class='mb-3'><label class='form-label'>nomArticle</label><input type='text' name='nomArticle' class='form-control'></div><button type='submit' class='btn btn-success'>Create Datetimes</button>
</form>
<?php $content = ob_get_clean();?>