<?php
$title = 'Edit Livres';
ob_start();?>
<div class='container'><h1>Edit Livres</h1>
<form method='POST' action='/livres/update/<?php echo $item['id']; ?>' class='mt-4' enctype='multipart/form-data'>
<div class='mb-3'><label class='form-label'>titre</label><input type='text' name='titre' value='<?php echo htmlspecialchars($item['titre']); ?>' class='form-control' required></div><div class='mb-3'><label class='form-label'>annee_publication</label><input type='date' name='annee_publication' value='<?php echo htmlspecialchars($item['annee_publication']); ?>' class='form-control' required></div><div class='mb-3'><label class='form-label'>auteur_id</label><select name='auteur_id' class='form-select' required>
                    <option value=''>Sélectionnez une option</option>
                    <?php if (!empty($auteurs)): ?>
                        <?php foreach ($auteurs as $auteursItem): ?>
                            <option value='<?php echo $auteursItem['id']; ?>' <?php echo ($item['auteur_id'] == $auteursItem['id']) ? 'selected' : ''; ?>>
                                <?php echo $auteursItem['nom']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value=''>Aucune donnée disponible</option>
                    <?php endif; ?>
                </select></div><div class='mb-3'><label class='form-label'>categorie_id</label><select name='categorie_id' class='form-select' required>
                    <option value=''>Sélectionnez une option</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $categoriesItem): ?>
                            <option value='<?php echo $categoriesItem['id']; ?>' <?php echo ($item['categorie_id'] == $categoriesItem['id']) ? 'selected' : ''; ?>>
                                <?php echo $categoriesItem['nom']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value=''>Aucune donnée disponible</option>
                    <?php endif; ?>
                </select></div><div class='mb-3'><label class='form-label'>lieu_edition_id</label><select name='lieu_edition_id' class='form-select' required>
                    <option value=''>Sélectionnez une option</option>
                    <?php if (!empty($lieu_edition)): ?>
                        <?php foreach ($lieu_edition as $lieu_editionItem): ?>
                            <option value='<?php echo $lieu_editionItem['id']; ?>' <?php echo ($item['lieu_edition_id'] == $lieu_editionItem['id']) ? 'selected' : ''; ?>>
                                <?php echo $lieu_editionItem['nom']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value=''>Aucune donnée disponible</option>
                    <?php endif; ?>
                </select></div><div class='mb-3'><label class='form-label'>verifie</label><select name='verifie' class='form-select' required><option value='Oui' <?php echo ($item['verifie'] == 'Oui') ? 'selected' : ''; ?>>Oui</option><option value='Non' <?php echo ($item['verifie'] == 'Non') ? 'selected' : ''; ?>>Non</option></select></div><div class='mb-3'><label class='form-label'>archive</label><div class='form-check'>
                    <input type='radio' name='archive' value='1' class='form-check-input' id='archive_yes' <?php echo ($item['archive'] == 1) ? 'checked' : ''; ?> >
                    <label class='form-check-label' for='archive_yes'>Yes</label>
                </div>
                <div class='form-check'>
                    <input type='radio' name='archive' value='0' class='form-check-input' id='archive_no' <?php echo ($item['archive'] == 0) ? 'checked' : ''; ?>>
                    <label class='form-check-label' for='archive_no'>No</label>
                </div></div><button type='submit' class='btn btn-warning'>Update Livres</button>
</form>
</div><?php $content = ob_get_clean();?>