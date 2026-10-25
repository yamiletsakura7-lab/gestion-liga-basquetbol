<?php
$stmt = $pdo->query('SELECT id, name FROM teams ORDER BY name');
$teams = $stmt->fetchAll();

$id = (int) ($_GET['id'] ?? 0);

$player = [
    'team_id' => '',
    'name' => '',
    'jersey_number' => '',
    'position' => '',
    'age' => ''
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM players WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();

    if (!$found) {
        echo '<div class="alert error">Jugador no encontrado.</div>';
        return;
    }

    $player = $found;
}
?>

<div class="form-card">
    <h1><?= $id ? 'Editar jugador' : 'Registrar jugador' ?></h1>
    <p>Introduce los datos de la plantilla.</p>

    <?php if (!$teams): ?>
        <div class="alert error">
            Primero debes registrar un equipo.
        </div>
        <a class="btn" href="index.php?page=team_form">Crear equipo</a>
    <?php else: ?>
        <form method="POST" action="index.php?page=player_form">
            <input type="hidden" name="id" value="<?= $id ?>">

            <label for="team_id">Equipo</label>
            <select id="team_id" name="team_id" required>
                <option value="">Seleccionar equipo</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= (int) $team['id'] ?>"
                        <?= (int) $player['team_id'] === (int) $team['id'] ? 'selected' : '' ?>>
                        <?= e($team['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="name">Nombre del jugador</label>
            <input id="name" name="name" maxlength="120"
                value="<?= e($player['name']) ?>" required>

            <label for="jersey_number">Número de camiseta</label>
            <input id="jersey_number" name="jersey_number"
                type="number" min="0" max="999"
                value="<?= e($player['jersey_number']) ?>" required>

            <label for="position">Posición</label>
            <select id="position" name="position" required>
                <?php
                $positions = [
                    'Base',
                    'Escolta',
                    'Alero',
                    'Ala-pívot',
                    'Pívot'
                ];
                ?>
                <option value="">Seleccionar posición</option>
                <?php foreach ($positions as $position): ?>
                    <option value="<?= e($position) ?>"
                        <?= $player['position'] === $position ? 'selected' : '' ?>>
                        <?= e($position) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="age">Edad</label>
            <input id="age" name="age" type="number"
                min="1" max="99" value="<?= e($player['age']) ?>" required>

            <div class="form-actions">
                <button class="btn" type="submit">Guardar jugador</button>
                <a class="btn btn-secondary" href="index.php?page=players">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
</div>