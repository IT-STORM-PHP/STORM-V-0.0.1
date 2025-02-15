<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$model} Management</title>\
    <link rel='stylesheet' href='/assets/vendor/bootstrap/css/bootstrap.min.css'>
</head>

<body class='container mt-5'>
<h1>Create Test</h1>
<form method='POST' action='/test/store' class='mt-4'>
<div class='mb-3'><label class='form-label'>name</label><input type='text' name='name' class='form-control'></div><div class='mb-3'><label class='form-label'>deci</label><input type='number' name='deci' class='form-control'></div><div class='mb-3'><label class='form-label'>boul</label><input type='number' name='boul' class='form-control'></div><button type='submit' class='btn btn-success'>Create Test</button>
</form>
<script src='/assets/vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
</body>

</html>