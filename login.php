<?php
session_start();

$_SESSION['user'] = 'Thomas Ostrowski';
$user = $_SESSION['user'] ?? null;

echo '<h1>Bonjour '.$user;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <a href="index.php">Retour aux avis</a>
</body>
</html>