<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title><?=$title?></title>
    <!-- Inclusion de Bootstrap CSS -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
</head>

<body class='container mt-5'>
    <?= $content ?>
    <!-- Inclusion de Bootstrap JS -->
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/clipboard.js" ></script>
</body>

</html>