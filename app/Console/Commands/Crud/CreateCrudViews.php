<?php

namespace App\Console\Commands\Crud;

class CreateCrudViews
{
    public function createCrudViews($viewDir, $model, $columns, $foreignKeys = [])
    {
        $modelLower = strtolower($model);

        // Fonction pour déterminer le type d'input en fonction du type de colonne
        function getInputType($type)
        {
            if (str_contains($type, 'int') || str_contains($type, 'float') || str_contains($type, 'double') || str_contains($type, 'decimal')) {
                // Si c'est un tinyint(1), c'est un booléen
                if (str_contains($type, 'tinyint(1)')) {
                    return 'radio'; 
                }
                return 'number';
            } elseif (preg_match('/varchar\((\d+)\)/', $type, $matches) && (int)$matches[1] > 255) {
                return 'textarea';
            } elseif (str_contains($type, 'text')) {
                return 'textarea';
            } elseif (str_contains($type, 'date')) {
                return 'date';
            } elseif (str_contains($type, 'datetime') || str_contains($type, 'timestamp')) {
                return 'datetime-local';
            } elseif (str_contains($type, 'year')) {
                return 'year';
            } elseif (str_contains($type, 'time')) {
                return 'time';
            } elseif (str_contains($type, 'boolean') || str_contains($type, 'tinyint(1)')) {
                return 'radio'; // Utiliser des boutons radio pour les booléens
            } elseif (str_contains($type, 'enum')) {
                return 'enum';
            } elseif (str_contains($type, 'json')) {
                return 'json';
            } elseif (str_contains($type, 'file')) {
                return 'file';
            }
            return 'text';
        }

        // Fonction pour extraire les valeurs d'un champ ENUM
        function getEnumValues($type)
        {
            if (preg_match("/^enum\((.*)\)$/", $type, $matches)) {
                $enum = array();
                foreach (explode(',', $matches[1]) as $value) {
                    $enum[] = trim($value, "'");
                }
                return $enum;
            }
            return [];
        }

        // Trouver la clé primaire
        $primaryKey = 'id';
        foreach ($columns as $column) {
            if ($column['Key'] === 'PRI') {
                $primaryKey = $column['Field'];
                break;
            }
        }

        // Vue Index
        $listViewContent = "<?php\n\$title = '{$model} List';\nob_start();?>\n
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
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $displayColumn = $foreignKey['display_column'] ?? $foreignColumn; // Utiliser la colonne significative
                $listViewContent .= "<td><?php echo htmlspecialchars(\$item['{$foreignTable}_{$displayColumn}'] ?? 'N/A'); ?></td>";
            } else {
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
        $createViewContent = "<?php\n\$title = 'Create {$model}';\nob_start();?>\n<div class='container'><h1>Create {$model}</h1>\n<form method='POST' action='/{$modelLower}/store' class='mt-4' enctype='multipart/form-data'>\n";
        foreach ($columns as $column) {
            if (in_array($column['Field'], ['id', 'created_at', 'updated_at', $primaryKey])) continue;

            $inputType = getInputType($column['Type']);
            $required = ($column['Null'] === 'NO') ? 'required' : '';
            $createViewContent .= "<div class='mb-3'><label class='form-label'>{$column['Field']}</label>";

            if (isset($foreignKeys[$column['Field']])) {
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $displayColumn = $foreignKey['display_column'] ?? $foreignColumn; // Utiliser la colonne significative
                $createViewContent .= "<select name='{$column['Field']}' class='form-select' {$required}>
                    <option value=''>Sélectionnez une option</option>
                    <?php if (!empty(\${$foreignTable})): ?>
                        <?php foreach (\${$foreignTable} as \${$foreignTable}Item): ?>
                            <option value='<?php echo \${$foreignTable}Item['{$foreignColumn}']; ?>'>
                                <?php echo \${$foreignTable}Item['{$displayColumn}']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value=''>Aucune donnée disponible</option>
                    <?php endif; ?>
                </select>";
            } elseif ($inputType === 'radio') {
                // Gestion des boutons radio pour les booléens
                $createViewContent .= "<div class='form-check'>
                    <input type='radio' name='{$column['Field']}' value='1' class='form-check-input' id='{$column['Field']}_yes' {$required}>
                    <label class='form-check-label' for='{$column['Field']}_yes'>Yes</label>
                </div>
                <div class='form-check'>
                    <input type='radio' name='{$column['Field']}' value='0' class='form-check-input' id='{$column['Field']}_no'>
                    <label class='form-check-label' for='{$column['Field']}_no'>No</label>
                </div>";
            } elseif ($inputType === 'textarea') {
                $createViewContent .= "<textarea name='{$column['Field']}' class='form-control' {$required}></textarea>";
            } elseif ($inputType === 'enum') {
                $enumValues = getEnumValues($column['Type']);
                $createViewContent .= "<select name='{$column['Field']}' class='form-select' {$required}>";
                foreach ($enumValues as $value) {
                    $createViewContent .= "<option value='{$value}'>{$value}</option>";
                }
                $createViewContent .= "</select>";
            } elseif ($inputType === 'year') {
                $createViewContent .= "<input type='number' name='{$column['Field']}' min='1900' max='2100' class='form-control' {$required}>";
            } elseif ($inputType === 'time') {
                $createViewContent .= "<input type='time' name='{$column['Field']}' class='form-control' {$required}>";
            } elseif ($inputType === 'file') {
                $createViewContent .= "<input type='file' name='{$column['Field']}' class='form-control' {$required}>";
            } elseif ($inputType === 'json') {
                $createViewContent .= "<textarea name='{$column['Field']}' class='form-control' {$required}></textarea>";
            } else {
                $createViewContent .= "<input type='{$inputType}' name='{$column['Field']}' class='form-control' {$required}>";
            }
            $createViewContent .= "</div>";
        }
        $createViewContent .= "<button type='submit' class='btn btn-success'>Create {$model}</button>\n</form>\n</div><?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/create.php", $createViewContent);

        // Vue Show
        $showViewContent = "<?php\n\$title = 'Show {$model}';\nob_start();?>\n<div class='container'><h1 class='mb-4'>Show {$model}</h1>\n";
        $showViewContent .= "<?php if (!empty(\$item)): ?>\n";
        $showViewContent .= "<div class='card mb-4'>\n<div class='card-body'>\n";

        foreach ($columns as $column) {
            if (isset($foreignKeys[$column['Field']])) {
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $displayColumn = $foreignKey['display_column'] ?? $foreignColumn; // Utiliser la colonne significative
                $showViewContent .= "<p><strong>{$column['Field']}:</strong> <?php echo htmlspecialchars(\$item['{$foreignTable}_{$displayColumn}'] ?? 'N/A'); ?></p>\n";
            } else {
                $showViewContent .= "<p><strong>{$column['Field']}:</strong> <?php echo htmlspecialchars(\$item['{$column['Field']}'] ?? 'N/A'); ?></p>\n";
            }
        }

        $showViewContent .= "</div>\n</div>\n";
        $showViewContent .= "<?php else: ?>\n<p class='text-danger'>Aucune donnée trouvée.</p>\n<?php endif; ?>\n";
        $showViewContent .= "<a href='/{$modelLower}' class='btn btn-secondary'>Back to List</a>\n\n</div><?php \$content = ob_get_clean();?>";

        file_put_contents("{$viewDir}/show.php", $showViewContent);

        // Vue Edit
        $editViewContent = "<?php\n\$title = 'Edit {$model}';\nob_start();?>\n<div class='container'><h1>Edit {$model}</h1>\n<form method='POST' action='/{$modelLower}/update/<?php echo \$item['{$primaryKey}']; ?>' class='mt-4' enctype='multipart/form-data'>\n";
        foreach ($columns as $column) {
            if (in_array($column['Field'], ['id', 'created_at', 'updated_at', $primaryKey])) continue;

            $inputType = getInputType($column['Type']);
            $required = ($column['Null'] === 'NO') ? 'required' : '';
            $editViewContent .= "<div class='mb-3'><label class='form-label'>{$column['Field']}</label>";

            if (isset($foreignKeys[$column['Field']])) {
                $foreignKey = $foreignKeys[$column['Field']];
                $foreignTable = $foreignKey['table'];
                $foreignColumn = $foreignKey['column'];
                $displayColumn = $foreignKey['display_column'] ?? $foreignColumn; // Utiliser la colonne significative
                $editViewContent .= "<select name='{$column['Field']}' class='form-select' {$required}>
                    <option value=''>Sélectionnez une option</option>
                    <?php if (!empty(\${$foreignTable})): ?>
                        <?php foreach (\${$foreignTable} as \${$foreignTable}Item): ?>
                            <option value='<?php echo \${$foreignTable}Item['{$foreignColumn}']; ?>' <?php echo (\$item['{$column['Field']}'] == \${$foreignTable}Item['{$foreignColumn}']) ? 'selected' : ''; ?>>
                                <?php echo \${$foreignTable}Item['{$displayColumn}']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value=''>Aucune donnée disponible</option>
                    <?php endif; ?>
                </select>";
            } elseif ($inputType === 'radio') {
                // Gestion des boutons radio pour les booléens
                $editViewContent .= "<div class='form-check'>
                    <input type='radio' name='{$column['Field']}' value='1' class='form-check-input' id='{$column['Field']}_yes' <?php echo (\$item['{$column['Field']}'] == 1) ? 'checked' : ''; ?> {$required}>
                    <label class='form-check-label' for='{$column['Field']}_yes'>Yes</label>
                </div>
                <div class='form-check'>
                    <input type='radio' name='{$column['Field']}' value='0' class='form-check-input' id='{$column['Field']}_no' <?php echo (\$item['{$column['Field']}'] == 0) ? 'checked' : ''; ?>>
                    <label class='form-check-label' for='{$column['Field']}_no'>No</label>
                </div>";
            } elseif ($inputType === 'textarea') {
                $editViewContent .= "<textarea name='{$column['Field']}' class='form-control' {$required}><?php echo htmlspecialchars(\$item['{$column['Field']}']); ?></textarea>";
            } elseif ($inputType === 'enum') {
                $enumValues = getEnumValues($column['Type']);
                $editViewContent .= "<select name='{$column['Field']}' class='form-select' {$required}>";
                foreach ($enumValues as $value) {
                    $editViewContent .= "<option value='{$value}' <?php echo (\$item['{$column['Field']}'] == '{$value}') ? 'selected' : ''; ?>>{$value}</option>";
                }
                $editViewContent .= "</select>";
            } elseif ($inputType === 'year') {
                $editViewContent .= "<input type='number' name='{$column['Field']}' min='1900' max='2100' value='<?php echo htmlspecialchars(\$item['{$column['Field']}']); ?>' class='form-control' {$required}>";
            } elseif ($inputType === 'time') {
                $editViewContent .= "<input type='time' name='{$column['Field']}' value='<?php echo htmlspecialchars(\$item['{$column['Field']}']); ?>' class='form-control' {$required}>";
            } elseif ($inputType === 'file') {
                $editViewContent .= "<input type='file' name='{$column['Field']}' class='form-control' {$required}>";
            } elseif ($inputType === 'json') {
                $editViewContent .= "<textarea name='{$column['Field']}' class='form-control' {$required}><?php echo htmlspecialchars(\$item['{$column['Field']}']); ?></textarea>";
            } else {
                $editViewContent .= "<input type='{$inputType}' name='{$column['Field']}' value='<?php echo htmlspecialchars(\$item['{$column['Field']}']); ?>' class='form-control' {$required}>";
            }
            $editViewContent .= "</div>";
        }
        $editViewContent .= "<button type='submit' class='btn btn-warning'>Update {$model}</button>\n</form>\n</div><?php \$content = ob_get_clean();?>";
        file_put_contents("{$viewDir}/edit.php", $editViewContent);

        echo "✅ Vues CRUD pour '$model' créées avec succès.\n";
    }
}
