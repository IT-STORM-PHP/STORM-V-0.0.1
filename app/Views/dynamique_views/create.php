<?php
$title = '<nom de votre page>';
ob_start();?>
<h1>Create Dynamique_views</h1>
<form method='POST' action='/dynamique_views/store' class='mt-4'>
<div class='mb-3'><label class='form-label'>nom</label><input type='text' name='nom' class='form-control'></div><div class='mb-3'><label class='form-label'>description</label><input type='text' name='description' class='form-control'></div><div class='mb-3'><label class='form-label'>url</label><input type='text' name='url' class='form-control'></div><div class='mb-3'><label class='form-label'>icon</label><input type='text' name='icon' class='form-control'></div><div class='mb-3'><label class='form-label'>color</label><input type='text' name='color' class='form-control'></div><button type='submit' class='btn btn-success'>Create Dynamique_views</button>
</form>
<?php $content = ob_get_clean();?>