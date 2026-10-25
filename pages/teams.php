<?php
$stmt = $pdo->query(
    'SELECT t.*,
        (SELECT COUNT(*) FROM players p WHERE p.team_id = t.id) AS total_players
     FROM teams t
     ORDER BY t.name'
);
$teams = $stmt->fetchAll();
?>

<div class="page-heading">
    <div>
        <h1>Gestión de equipos</h1>
        <p>Administra los equipos participantes de la liga.</p>
    </div>
    <a class="btn" href="index.php?page=team_form">+ Nuevo equipo</a>
</div>

<div class="table-card">
    <?php if (!$teams): ?>
        <p class="empty">Todavía no hay equipos registrados.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Ciudad</th>
                        <th>Entrenador</th>
                        <th>Jugadores</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($teams as $team): ?>
                        <tr>
                            <td><?= e($team['name']) ?></td>
                            <td><?= e($team['city']) ?></td>
                            <td><?= e($team['coach'] ?: 'Sin asignar') ?></td>
                            <td><?= (int) $team['total_players'] ?></td>
                            <td class="actions">
                                <a class="btn btn-small btn-secondary"
                                    href="index.php?page=team_form&id=<?= (int) $team['id'] ?>">
                                    Editar
                                </a>

                                <form method="POST"
                                    action="index.php?page=team_delete"
                                    onsubmit="return confirm('¿Eliminar este equipo y sus registros relacionados?')">
                                    <input type="hidden" name="id"
                                        value="<?= (int) $team['id'] ?>">
                                    <button class="btn btn-small btn-danger" type="submit">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>