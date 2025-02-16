<?php
$title = '<nom de votre page>';
ob_start();?>
<h1 class='text-danger'>Are you sure you want to delete this Dynamique_views?</h1>
<form method='POST' action='/dynamique_views/delete/<?php echo $item['id']; ?>'>
<button type='submit' class='btn btn-danger'>Yes, Delete</button>
<a href='/dynamique_views' class='btn btn-secondary'>Cancel</a>
</form>
<?php $content = ob_get_clean();?>