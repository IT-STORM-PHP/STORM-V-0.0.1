<?php

namespace App\Console\Commands\Crud;

class CreateCrudViews
{
    public function createCrudViews($viewDir, $model, $columns, $foreignKeys = [])
    {
        $modelLower = strtolower($model);

        function getInputType($type)
        {
            if (str_contains($type, 'int') || str_contains($type, 'float') || str_contains($type, 'double') || str_contains($type, 'decimal')) {
                return 'number';
            } elseif (preg_match('/varchar\((\d+)\)/', $type, $matches) && (int)$matches[1] > 255) {
                return 'textarea';
            } elseif (str_contains($type, 'text')) {
                return 'textarea';
            } elseif (str_contains($type, 'date')) {
                return 'date';
            } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
                return 'datetime-local';
            } elseif (str_contains($type, 'boolean') || str_contains($type, 'tinyint(1)')) {
                return 'select';
            }
            return 'text';
        }

        // Trouver la clé primaire
        $primaryKey = 'id'; // Valeur par défaut
        foreach ($columns as $column) {
            if ($column['Key'] === 'PRI') {
                $primaryKey = $column['Field'];
                break;
            }
        }

        // Vue Index
        $listViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n
<div class='container'>
    <h1 class='mb-4'>{$model} List</h1>
    <a href='/{$modelLower}/create' class='btn btn-success mb-3'>Create {$model}</a>
    <table class='table'>
    <thead class='table-light'><tr>";

        foreach ($columns as $column) {
            $listViewContent .= "<th>{$column['Field']}</th>";
        }
        $listViewContent .= "<th>Actions</th></tr></thead><tbody>\n
<?php foreach (\$items as \$item): ?>\n<tr>";

        foreach ($columns as $column) {
            if (isset($foreignKeys[$column['Field']])) {
                // Si la colonne est une clé étrangère, afficher une valeur significative
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $listViewContent .= "<td><?php echo htmlspecialchars(\$item['{$foreignTable}_{$foreignColumn}'] ?? 'N/A'); ?></td>";
            } else {
                // Sinon, afficher la valeur de la colonne
                $listViewContent .= "<td><?php echo htmlspecialchars(\$item['{$column['Field']}']); ?></td>";
            }
        }

        $listViewContent .= "<td>
    <a href='/{$modelLower}/show/<?php echo \$item['{$primaryKey}']; ?>' class='btn btn-dark btn-sm'>Show</a>
    <a href='/{$modelLower}/edit/<?php echo \$item['{$primaryKey}']; ?>' class='btn btn-warning btn-sm'>Edit</a>
    <form action='/{$modelLower}/delete/<?php echo \$item['{$primaryKey}']; ?>' method='POST' class='d-inline'>
        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
    </form>
</td></tr>\n<?php endforeach; ?>\n</tbody></table>\n</div><?php \$content = ob_get_clean();?>\n";

        file_put_contents("{$viewDir}/index.php", $listViewContent);



        // Vue Create
        $createViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<div class='container'><h1>Create {$model}</h1>\n<form method='POST' action='/{$modelLower}/store' class='mt-4'>\n";
        foreach ($columns as $column) {
            if (in_array($column['Field'], ['id', 'created_at', 'updated_at', $primaryKey])) continue;

            $inputType = getInputType($column['Type']);
            $createViewContent .= "<div class='mb-3'><label class='form-label'>{$column['Field']}</label>";

            // Vérifier si la colonne est une clé étrangère
            if (isset($foreignKeys[$column['Field']])) {
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $createViewContent .= "<select name='{$column['Field']}' class='form-select'>";
                $createViewContent .= "<?php if (!empty(\${$foreignTable})): ?>";
                $createViewContent .= "<?php foreach (\${$foreignTable} as \${$foreignTable}Item): ?>";
                $createViewContent .= "<option value='<?php echo \${$foreignTable}Item['{$foreignColumn}']; ?>'><?php echo \${$foreignTable}Item['name']; ?></option>\n"; // Afficher une valeur significative
                $createViewContent .= "<?php endforeach; ?>";
                $createViewContent .= "<?php else: ?>";
                $createViewContent .= "<option value=''>Aucune donnée disponible</option>\n";
                $createViewContent .= "<?php endif; ?>";
                $createViewContent .= "</select>";
            } elseif ($inputType === 'select') {
                $createViewContent .= "<select name='{$column['Field']}' class='form-select'><option value='1'>Yes</option><option value='0'>No</option></select>\n";
            } elseif ($inputType === 'textarea') {
                $createViewContent .= "<textarea name='{$column['Field']}' class='form-control'></textarea>";
            } else {
                $createViewContent .= "<input type='{$inputType}' name='{$column['Field']}' class='form-control'>";
            }
            $createViewContent .= "</div>";
        }
        $createViewContent .= "<button type='submit' class='btn btn-success'>Create {$model}</button>\n</form>\n</div><?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/create.php", $createViewContent);

        // Vue Show
        $showViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<div class='container'><h1 class='mb-4'>Show {$model}</h1>\n";
        $showViewContent .= "<?php if (!empty(\$item)): ?>\n";
        $showViewContent .= "<div class='card mb-4'>\n<div class='card-body'>\n";

        foreach ($columns as $column) {
            if (isset($foreignKeys[$column['Field']])) {
                // Si la colonne est une clé étrangère, afficher une valeur significative
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $showViewContent .= "<p><strong>{$column['Field']}:</strong> <?php echo htmlspecialchars(\$item['{$foreignTable}_{$foreignColumn}'] ?? 'N/A'); ?></p>\n";
            } else {
                // Sinon, afficher la valeur de la colonne
                $showViewContent .= "<p><strong>{$column['Field']}:</strong> <?php echo htmlspecialchars(\$item['{$column['Field']}'] ?? 'N/A'); ?></p>\n";
            }
        }

        $showViewContent .= "</div>\n</div>\n";
        $showViewContent .= "<?php else: ?>\n<p class='text-danger'>Aucune donnée trouvée.</p>\n<?php endif; ?>\n";
        $showViewContent .= "<a href='/{$modelLower}' class='btn btn-secondary'>Back to List</a>\n\n</div><?php \$content = ob_get_clean();?>";

        file_put_contents("{$viewDir}/show.php", $showViewContent);


        // Vue Edit
        $editViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<div class='container'><h1>Edit {$model}</h1>\n<form method='POST' action='/{$modelLower}/update/<?php echo \$item['{$primaryKey}']; ?>' class='mt-4'>\n";
        foreach ($columns as $column) {
            if (in_array($column['Field'], ['id', 'created_at', 'updated_at', $primaryKey])) continue;

            $inputType = getInputType($column['Type']);
            $editViewContent .= "<div class='mb-3'><label class='form-label'>{$column['Field']}</label>";

            // Vérifier si la colonne est une clé étrangère
            if (isset($foreignKeys[$column['Field']])) {
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $editViewContent .= "<select name='{$column['Field']}' class='form-select'>";
                $editViewContent .= "<?php if (!empty(\${$foreignTable})): ?>";
                $editViewContent .= "<?php foreach (\${$foreignTable} as \${$foreignTable}Item): ?>";
                $editViewContent .= "<option value='<?php echo \${$foreignTable}Item['{$foreignColumn}']; ?>' <?php echo (\$item['{$column['Field']}'] == \${$foreignTable}Item['{$foreignColumn}']) ? 'selected' : ''; ?>><?php echo \${$foreignTable}Item['name']; ?></option>"; // Afficher une valeur significative (par exemple, 'name')
                $editViewContent .= "<?php endforeach; ?>";
                $editViewContent .= "<?php else: ?>";
                $editViewContent .= "<option value=''>Aucune donnée disponible</option>";
                $editViewContent .= "<?php endif; ?>";
                $editViewContent .= "</select>";
            } elseif ($inputType === 'select') {
                $editViewContent .= "<select name='{$column['Field']}' class='form-select'><option value='1'>Yes</option><option value='0'>No</option></select>";
            } elseif ($inputType === 'textarea') {
                $editViewContent .= "<textarea name='{$column['Field']}' class='form-control'><?php echo htmlspecialchars(\$item['{$column['Field']}']); ?></textarea>";
            } else {
                $editViewContent .= "<input type='{$inputType}' name='{$column['Field']}' value='<?php echo htmlspecialchars(\$item['{$column['Field']}']); ?>' class='form-control'>";
            }
            $editViewContent .= "</div>";
        }
        $editViewContent .= "<button type='submit' class='btn btn-warning'>Update {$model}</button>\n</form>\n</div><?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/edit.php", $editViewContent);


        // Vue Delete
        $deleteViewContent = "<?php\n\$title = '<nom de votre page>';\nob_start();?>\n<h1 class='text-danger'>Are you sure you want to delete this {$model}?</h1>\n<form method='POST' action='/{$modelLower}/delete/<?php echo \$item['id']; ?>'>\n<button type='submit' class='btn btn-danger'>Yes, Delete</button>\n<a href='/{$modelLower}' class='btn btn-secondary'>Cancel</a>\n</form>\n<?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/delete.php", $deleteViewContent);
    }
}
