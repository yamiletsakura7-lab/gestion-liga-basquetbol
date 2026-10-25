<?php
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liga de Básquetbol</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="topbar">
        <a class="brand" href="index.php">
            🏀 Liga Basket
        </a>

        <?php if (logged_in()): ?>
            <nav class="nav">
                <a href="index.php?page=dashboard">Inicio</a>
                <a href="index.php?page=teams">Equipos</a>
                <a href="index.php?page=players">Jugadores</a>
                <a href="index.php?page=matches">Partidos</a>
                <a href="index.php?page=logout">Cerrar sesión</a>
            </nav>
            <span class="welcome">
                Hola, <?= e($_SESSION['user_name'] ?? '') ?>
            </span>
        <?php endif; ?>
    </header>

    <main class="container">
        <?php if ($flashMessage): ?>
            <div class="alert <?= e($flashMessage['type']) ?>">
                <?= e($flashMessage['message']) ?>
            </div>
        <?php endif; ?>