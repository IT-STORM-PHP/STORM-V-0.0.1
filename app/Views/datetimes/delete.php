<?php
$title = '<nom de votre page>';
ob_start();?>
<h1 class='text-danger'>Are you sure you want to delete this Datetimes?</h1>
<form method='POST' action='/datetimes/delete/<?php echo $item['id']; ?>'>
<button type='submit' class='btn btn-danger'>Yes, Delete</button>
<a href='/datetimes' class='btn btn-secondary'>Cancel</a>
</form>
<?php $content = ob_get_clean();?>