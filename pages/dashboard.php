<?php
$totalTeams = (int) $pdo->query(
    'SELECT COUNT(*) FROM teams'
)->fetchColumn();

$totalPlayers = (int) $pdo->query(
    'SELECT COUNT(*) FROM players'
)->fetchColumn();

$totalMatches = (int) $pdo->query(
    'SELECT COUNT(*) FROM matches'
)->fetchColumn();

$finishedMatches = (int) $pdo->query(
    "SELECT COUNT(*) FROM matches WHERE status = 'Finalizado'"
)->fetchColumn();

$sql = "
SELECT
    t.id,
    t.name,
    t.city,
    COUNT(m.id) AS played,
    SUM(
        CASE
            WHEN (m.home_team_id = t.id AND m.home_score > m.away_score)
              OR (m.away_team_id = t.id AND m.away_score > m.home_score)
            THEN 1 ELSE 0
        END
    ) AS wins,
    SUM(
        CASE
            WHEN (m.home_team_id = t.id AND m.home_score < m.away_score)
              OR (m.away_team_id = t.id AND m.away_score < m.home_score)
            THEN 1 ELSE 0
        END
    ) AS losses,
    SUM(
        CASE
            WHEN m.home_score = m.away_score THEN 1
            ELSE 0
        END
    ) AS draws,
    SUM(
        CASE
            WHEN m.home_team_id = t.id THEN m.home_score
            WHEN m.away_team_id = t.id THEN m.away_score
            ELSE 0
        END
    ) AS points_for,
    SUM(
        CASE
            WHEN m.home_team_id = t.id THEN m.away_score
            WHEN m.away_team_id = t.id THEN m.home_score
            ELSE 0
        END
    ) AS points_against
FROM teams t
LEFT JOIN matches m
    ON (
        (m.home_team_id = t.id OR m.away_team_id = t.id)
        AND m.status = 'Finalizado'
    )
GROUP BY t.id, t.name, t.city
";

$standings = $pdo->query($sql)->fetchAll();

foreach ($standings as &$row) {
    foreach (
        [
            'played',
            'wins',
            'losses',
            'draws',
            'points_for',
            'points_against'
        ] as $field
    ) {
        $row[$field] = (int) ($row[$field] ?? 0);
    }

    $row['difference'] =
        $row['points_for'] - $row['points_against'];

    $row['league_points'] =
        ($row['wins'] * 2) + $row['losses'];
}
unset($row);

usort($standings, function ($a, $b) {
    return ($b['league_points'] <=> $a['league_points'])
        ?: ($b['difference'] <=> $a['difference'])
        ?: ($b['points_for'] <=> $a['points_for'])
        ?: strcmp($a['name'], $b['name']);
});

$upcoming = $pdo->query(
    "SELECT m.*, h.name AS home_name, a.name AS away_name
     FROM matches m
     JOIN teams h ON h.id = m.home_team_id
     JOIN teams a ON a.id = m.away_team_id
     WHERE m.status = 'Programado'
     ORDER BY m.match_date ASC
     LIMIT 5"
)->fetchAll();
?>

<section class="hero">
    <div>
        <span class="eyebrow">PANEL DE CONTROL</span>
        <h1>Bienvenido a la liga 🏀</h1>
        <p>Administra equipos, jugadores y partidos desde un solo lugar.</p>
    </div>
    <a href="index.php?page=match_form" class="btn">+ Programar partido</a>
</section>

<div class="stats-grid">
    <article class="stat-card">
        <span>Equipos registrados</span>
        <strong><?= $totalTeams ?></strong>
        <span class="stat-icon">🏆</span>
    </article>

    <article class="stat-card">
        <span>Jugadores</span>
        <strong><?= $totalPlayers ?></strong>
        <span class="stat-icon">👥</span>
    </article>

    <article class="stat-card">
        <span>Partidos totales</span>
        <strong><?= $totalMatches ?></strong>
        <span class="stat-icon">🏀</span>
    </article>

    <article class="stat-card">
        <span>Partidos finalizados</span>
        <strong><?= $finishedMatches ?></strong>
        <span class="stat-icon">📊</span>
    </article>
</div>

<section class="section-card">
    <div class="section-title">
        <div>
            <h2>Tabla de posiciones</h2>
            <p>Clasificación calculada con partidos finalizados.</p>
        </div>
        <a href="index.php?page=matches">Ver partidos</a>
    </div>

    <?php if (!$standings): ?>
        <p class="empty">Registra equipos para crear la clasificación.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Equipo</th>
                        <th>PJ</th>
                        <th>PG</th>
                        <th>PP</th>
                        <th>PE</th>
                        <th>PF</th>
                        <th>PC</th>
                        <th>Dif.</th>
                        <th>Pts.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($standings as $i => $team): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= e($team['name']) ?></strong>
                                <small class="team-city"><?= e($team['city']) ?></small>
                            </td>
                            <td><?= $team['played'] ?></td>
                            <td><?= $team['wins'] ?></td>
                            <td><?= $team['losses'] ?></td>
                            <td><?= $team['draws'] ?></td>
                            <td><?= $team['points_for'] ?></td>
                            <td><?= $team['points_against'] ?></td>
                            <td><?= $team['difference'] ?></td>
                            <td><strong><?= $team['league_points'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <p class="table-note">
        PJ: partidos jugados; PG: ganados; PP: perdidos; PE: empatados;
        PF: puntos a favor; PC: puntos en contra.
        La clasificación otorga 2 puntos por victoria y 1 por derrota.
    </p>
</section>

<section class="section-card">
    <div class="section-title">
        <div>
            <h2>Próximos partidos</h2>
            <p>Encuentros que todavía están programados.</p>
        </div>
        <a href="index.php?page=matches">Ver todos</a>
    </div>

    <?php if (!$upcoming): ?>
        <p class="empty">No hay próximos partidos programados.</p>
    <?php else: ?>
        <div class="upcoming-list">
            <?php foreach ($upcoming as $match): ?>
                <article class="upcoming-item">
                    <div>
                        <strong>
                            <?= e($match['home_name']) ?>
                            <span class="vs">VS</span>
                            <?= e($match['away_name']) ?>
                        </strong>
                        <p><?= e($match['venue']) ?></p>
                    </div>
                    <div class="match-date">
                        <?= e(date('d/m/Y H:i', strtotime($match['match_date']))) ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>