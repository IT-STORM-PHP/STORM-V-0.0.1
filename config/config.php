<?php
    putenv('ENV=test');
    return [
        'db_host' => '127.0.0.1',
        'db_port' => '3306',
        'db_name' => 'tp_code_php',
        'db_user' => 'root',
        'db_pass' => '',
        'errors_route_message' => "Impossible d'accéder à cette URL, car elle n'est pas définie. Définissez-la avec :",
    ];
?>
