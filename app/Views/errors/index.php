<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 : Page non trouvé</title>
</head>
<body>
    <h1>
        <?= htmlspecialchars("$message") ?>
    </h1>
    <center>
    <p>
        <?= htmlspecialchars("$url") ?>
    </p>
    </center>
    <p>
    Dans le fichier se situant à <strong><?= htmlspecialchars("$folder") ?></strong>
    </p>
</body>
</html>