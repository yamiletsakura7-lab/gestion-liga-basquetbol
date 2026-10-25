<?php
$id = (int) ($_GET['id'] ?? 0);

$team = [
    'name' => '',
    'city' => '',
    'coach' => ''
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM teams WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();

    if (!$found) {
        echo '<div class="alert error">Equipo no encontrado.</div>';
        return;
    }

    $team = $found;
}
?>

<div class="form-card">
    <h1><?= $id ? 'Editar equipo' : 'Registrar equipo' ?></h1>
    <p>Completa la información del equipo.</p>

    <form method="POST" action="index.php?page=team_form">
        <input type="hidden" name="id" value="<?= $id ?>">

        <label for="name">Nombre del equipo</label>
        <input id="name" name="name" maxlength="100"
            value="<?= e($team['name']) ?>" required>

        <label for="city">Ciudad</label>
        <input id="city" name="city" maxlength="100"
            value="<?= e($team['city']) ?>" required>

        <label for="coach">Entrenador</label>
        <input id="coach" name="coach" maxlength="100"
            value="<?= e($team['coach'] ?? '') ?>">

        <div class="form-actions">
            <button class="btn" type="submit">Guardar equipo</button>
            <a class="btn btn-secondary" href="index.php?page=teams">Cancelar</a>
        </div>
    </form>
</div>