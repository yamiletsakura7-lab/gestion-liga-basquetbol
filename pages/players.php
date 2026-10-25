<?php
$stmt = $pdo->query(
    'SELECT p.*, t.name AS team_name
     FROM players p
     INNER JOIN teams t ON p.team_id = t.id
     ORDER BY t.name, p.name'
);
$players = $stmt->fetchAll();
?>

<div class="page-heading">
    <div>
        <h1>Gestión de jugadores</h1>
        <p>Consulta y administra la plantilla de cada equipo.</p>
    </div>
    <a class="btn" href="index.php?page=player_form">+ Nuevo jugador</a>
</div>

<div class="table-card">
    <?php if (!$players): ?>
        <p class="empty">Todavía no hay jugadores registrados.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>Equipo</th>
                        <th>Camiseta</th>
                        <th>Posición</th>
                        <th>Edad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player): ?>
                        <tr>
                            <td><?= e($player['name']) ?></td>
                            <td><?= e($player['team_name']) ?></td>
                            <td><?= (int) $player['jersey_number'] ?></td>
                            <td><?= e($player['position']) ?></td>
                            <td><?= (int) $player['age'] ?></td>
                            <td class="actions">
                                <a class="btn btn-small btn-secondary"
                                    href="index.php?page=player_form&id=<?= (int) $player['id'] ?>">
                                    Editar
                                </a>

                                <form method="POST"
                                    action="index.php?page=player_delete"
                                    onsubmit="return confirm('¿Eliminar este jugador?')">
                                    <input type="hidden" name="id"
                                        value="<?= (int) $player['id'] ?>">
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