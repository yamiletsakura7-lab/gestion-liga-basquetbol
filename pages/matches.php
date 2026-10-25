<?php
$stmt = $pdo->query(
    'SELECT m.*,
            h.name AS home_name,
            a.name AS away_name
     FROM matches m
     INNER JOIN teams h ON m.home_team_id = h.id
     INNER JOIN teams a ON m.away_team_id = a.id
     ORDER BY m.match_date DESC'
);

$matches = $stmt->fetchAll();
?>

<div class="page-heading">
    <div>
        <h1>Gestión de partidos</h1>
        <p>Programa encuentros y registra los resultados.</p>
    </div>
    <a class="btn" href="index.php?page=match_form">+ Nuevo partido</a>
</div>

<div class="table-card">
    <?php if (!$matches): ?>
        <p class="empty">Todavía no hay partidos registrados.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Local</th>
                        <th>Marcador</th>
                        <th>Visitante</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr>
                            <td><?= e(date('d/m/Y H:i', strtotime($match['match_date']))) ?></td>
                            <td><?= e($match['home_name']) ?></td>
                            <td class="score">
                                <?php if ($match['status'] === 'Finalizado'): ?>
                                    <?= (int) $match['home_score'] ?>
                                    -
                                    <?= (int) $match['away_score'] ?>
                                <?php else: ?>
                                    VS
                                <?php endif; ?>
                            </td>
                            <td><?= e($match['away_name']) ?></td>
                            <td><?= e($match['venue']) ?></td>
                            <td>
                                <span class="status <?= $match['status'] === 'Finalizado' ? 'finished' : 'scheduled' ?>">
                                    <?= e($match['status']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-small btn-secondary"
                                    href="index.php?page=match_form&id=<?= (int) $match['id'] ?>">
                                    Editar
                                </a>

                                <form method="POST"
                                    action="index.php?page=match_delete"
                                    onsubmit="return confirm('¿Eliminar este partido?')">
                                    <input type="hidden" name="id"
                                        value="<?= (int) $match['id'] ?>">
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