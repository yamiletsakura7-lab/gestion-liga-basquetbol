<?php
$stmt = $pdo->query('SELECT id, name FROM teams ORDER BY name');
$teams = $stmt->fetchAll();

$id = (int) ($_GET['id'] ?? 0);

$match = [
    'home_team_id' => '',
    'away_team_id' => '',
    'match_date' => '',
    'venue' => '',
    'home_score' => '',
    'away_score' => '',
    'status' => 'Programado'
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM matches WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();

    if (!$found) {
        echo '<div class="alert error">Partido no encontrado.</div>';
        return;
    }

    $match = $found;
    $match['match_date'] = date(
        'Y-m-d\TH:i',
        strtotime($match['match_date'])
    );
}
?>

<div class="form-card">
    <h1><?= $id ? 'Editar partido' : 'Programar partido' ?></h1>
    <p>Selecciona los equipos, la fecha y el estado del encuentro.</p>

    <?php if (count($teams) < 2): ?>
        <div class="alert error">
            Necesitas registrar al menos dos equipos para programar un partido.
        </div>
        <a class="btn" href="index.php?page=team_form">Registrar equipo</a>
    <?php else: ?>
        <form method="POST" action="index.php?page=match_form">
            <input type="hidden" name="id" value="<?= $id ?>">

            <label for="home_team_id">Equipo local</label>
            <select name="home_team_id" id="home_team_id" required>
                <option value="">Seleccionar equipo local</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= (int) $team['id'] ?>"
                        <?= (int) $match['home_team_id'] === (int) $team['id'] ? 'selected' : '' ?>>
                        <?= e($team['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="away_team_id">Equipo visitante</label>
            <select name="away_team_id" id="away_team_id" required>
                <option value="">Seleccionar equipo visitante</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= (int) $team['id'] ?>"
                        <?= (int) $match['away_team_id'] === (int) $team['id'] ? 'selected' : '' ?>>
                        <?= e($team['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="match_date">Fecha y hora</label>
            <input type="datetime-local" id="match_date"
                name="match_date"
                value="<?= e($match['match_date']) ?>" required>

            <label for="venue">Lugar del partido</label>
            <input type="text" id="venue" name="venue"
                maxlength="150" value="<?= e($match['venue']) ?>" required>

            <label for="status">Estado</label>
            <select id="status" name="status" required>
                <option value="Programado"
                    <?= $match['status'] === 'Programado' ? 'selected' : '' ?>>
                    Programado
                </option>
                <option value="Finalizado"
                    <?= $match['status'] === 'Finalizado' ? 'selected' : '' ?>>
                    Finalizado
                </option>
            </select>

            <div class="score-inputs">
                <div>
                    <label for="home_score">Puntos del equipo local</label>
                    <input type="number" id="home_score" name="home_score"
                        min="0" max="999"
                        value="<?= e($match['home_score'] ?? '') ?>">
                </div>

                <div>
                    <label for="away_score">Puntos del visitante</label>
                    <input type="number" id="away_score" name="away_score"
                        min="0" max="999"
                        value="<?= e($match['away_score'] ?? '') ?>">
                </div>
            </div>

            <small>
                Si marcas el partido como finalizado, debes ingresar ambos marcadores.
            </small>

            <div class="form-actions">
                <button class="btn" type="submit">Guardar partido</button>
                <a class="btn btn-secondary" href="index.php?page=matches">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
</div>