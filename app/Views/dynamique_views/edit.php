<?php
$title = '<nom de votre page>';
ob_start();?>
<h1>Edit Dynamique_views</h1>
<form method='POST' action='/dynamique_views/update/<?php echo $item['id']; ?>' class='mt-4'>
<div class='mb-3'><label class='form-label'>nom</label><input type='text' name='nom' value='<?php echo htmlspecialchars($item['nom']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>description</label><input type='text' name='description' value='<?php echo htmlspecialchars($item['description']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>url</label><input type='text' name='url' value='<?php echo htmlspecialchars($item['url']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>icon</label><input type='text' name='icon' value='<?php echo htmlspecialchars($item['icon']); ?>' class='form-control'></div><div class='mb-3'><label class='form-label'>color</label><input type='text' name='color' value='<?php echo htmlspecialchars($item['color']); ?>' class='form-control'></div><button type='submit' class='btn btn-primary'>Update Dynamique_views</button>
</form>
<?php $content = ob_get_clean();?>