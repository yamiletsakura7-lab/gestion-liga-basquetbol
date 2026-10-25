
<?php

require_once __DIR__ . '/config/bootstrap.php';

$page = $_GET['page'] ?? (
    logged_in() ? 'dashboard' : 'login'
);

$public = ['login', 'register'];

if (!in_array($page, $public, true)) {
    require_login();
}

$allowed = [
    'login',
    'register',
    'logout',
    'dashboard',
    'teams',
    'team_form',
    'team_delete',
    'players',
    'player_form',
    'player_delete',
    'matches',
    'match_form',
    'match_delete'
];

if (!in_array($page, $allowed, true)) {
    $page = logged_in() ? 'dashboard' : 'login';
}

if ($page === 'logout') {
    $_SESSION = [];
    session_destroy();

    header('Location: index.php?page=login');
    exit;
}

/* REGISTRO DE USUARIOS */
if (
    $page === 'register' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $name === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        flash(
            'Completa los campos correctamente. La contraseña debe tener al menos 8 caracteres.',
            'error'
        );
        redirect('register');
    }

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password)
             VALUES (?, ?, ?)'
        );

        $stmt->execute([
            $name,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        flash('Cuenta creada. Ya puedes iniciar sesión.');
        redirect('login');
    } catch (PDOException $ex) {
        flash(
            'No se pudo crear la cuenta. Comprueba que el correo no esté registrado.',
            'error'
        );
        redirect('register');
    }
}

/* INICIO DE SESIÓN */
if (
    $page === 'login' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $stmt = $pdo->prepare(
        'SELECT * FROM users WHERE email = ?'
    );

    $stmt->execute([
        trim($_POST['email'] ?? '')
    ]);

    $user = $stmt->fetch();

    if (
        $user &&
        password_verify(
            $_POST['password'] ?? '',
            $user['password']
        )
    ) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        redirect('dashboard');
    }

    flash('Correo o contraseña incorrectos.', 'error');
    redirect('login');
}

/* CREAR O EDITAR EQUIPOS */
if (
    $page === 'team_form' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $coach = trim($_POST['coach'] ?? '');

    if ($name === '' || $city === '') {
        flash('El nombre y la ciudad son obligatorios.', 'error');
        redirect('team_form' . ($id ? '&id=' . $id : ''));
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE teams
                 SET name = ?, city = ?, coach = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $name,
                $city,
                $coach ?: null,
                $id
            ]);

            flash('Equipo actualizado.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO teams (name, city, coach)
                 VALUES (?, ?, ?)'
            );

            $stmt->execute([
                $name,
                $city,
                $coach ?: null
            ]);

            flash('Equipo registrado.');
        }
    } catch (PDOException $ex) {
        flash(
            'No se pudo guardar. Verifica que el nombre no esté repetido.',
            'error'
        );
    }

    redirect('teams');
}

/* ELIMINAR EQUIPOS */
if (
    $page === 'team_delete' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $stmt = $pdo->prepare(
        'DELETE FROM teams WHERE id = ?'
    );

    $stmt->execute([
        (int) ($_POST['id'] ?? 0)
    ]);

    flash('Equipo eliminado.');
    redirect('teams');
}

/* CREAR O EDITAR JUGADORES */
if (
    $page === 'player_form' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $id = (int) ($_POST['id'] ?? 0);
    $team = (int) ($_POST['team_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $number = filter_var(
        $_POST['jersey_number'] ?? null,
        FILTER_VALIDATE_INT
    );
    $position = trim($_POST['position'] ?? '');
    $age = filter_var(
        $_POST['age'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        !$team ||
        $name === '' ||
        $number === false ||
        $number === null ||
        $number < 0 ||
        $position === '' ||
        $age === false ||
        $age === null ||
        $age < 1 ||
        $age > 99
    ) {
        flash('Revisa los datos del jugador.', 'error');
        redirect('player_form' . ($id ? '&id=' . $id : ''));
    }

    try {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE players
                 SET team_id = ?, name = ?, jersey_number = ?,
                     position = ?, age = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $team,
                $name,
                $number,
                $position,
                $age,
                $id
            ]);

            flash('Jugador actualizado.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO players
                 (team_id, name, jersey_number, position, age)
                 VALUES (?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $team,
                $name,
                $number,
                $position,
                $age
            ]);

            flash('Jugador registrado.');
        }
    } catch (PDOException $ex) {
        flash(
            'No se pudo guardar. Comprueba el equipo y el número de camiseta.',
            'error'
        );
    }

    redirect('players');
}

/* ELIMINAR JUGADORES */
if (
    $page === 'player_delete' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $stmt = $pdo->prepare(
        'DELETE FROM players WHERE id = ?'
    );

    $stmt->execute([
        (int) ($_POST['id'] ?? 0)
    ]);

    flash('Jugador eliminado.');
    redirect('players');
}

/* CREAR O EDITAR PARTIDOS */
if (
    $page === 'match_form' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $id = (int) ($_POST['id'] ?? 0);
    $home = (int) ($_POST['home_team_id'] ?? 0);
    $away = (int) ($_POST['away_team_id'] ?? 0);
    $date = $_POST['match_date'] ?? '';
    $venue = trim($_POST['venue'] ?? '');
    $status = $_POST['status'] ?? 'Programado';
    $hs = $_POST['home_score'] ?? '';
    $as = $_POST['away_score'] ?? '';

    $validDate = false;

    if ($date !== '') {
        $parsedDate = DateTime::createFromFormat(
            'Y-m-d\TH:i',
            $date
        );

        $validDate = $parsedDate &&
            $parsedDate->format('Y-m-d\TH:i') === $date;
    }

    $validScores = (
        $hs !== '' &&
        $as !== '' &&
        filter_var($hs, FILTER_VALIDATE_INT) !== false &&
        filter_var($as, FILTER_VALIDATE_INT) !== false &&
        (int) $hs >= 0 &&
        (int) $as >= 0
    );

    if (
        !$home ||
        !$away ||
        $home === $away ||
        !$validDate ||
        $venue === '' ||
        !in_array($status, ['Programado', 'Finalizado'], true) ||
        ($status === 'Finalizado' && !$validScores)
    ) {
        flash(
            'Completa correctamente los datos. Los equipos deben ser distintos y un partido finalizado requiere ambos marcadores.',
            'error'
        );

        redirect('match_form' . ($id ? '&id=' . $id : ''));
    }

    $hs = $status === 'Finalizado' ? (int) $hs : null;
    $as = $status === 'Finalizado' ? (int) $as : null;

    try {
        if ($id) {
            $stmt = $pdo->prepare(
                'UPDATE matches
                 SET home_team_id = ?, away_team_id = ?,
                     match_date = ?, venue = ?, home_score = ?,
                     away_score = ?, status = ?
                 WHERE id = ?'
            );

            $stmt->execute([
                $home,
                $away,
                str_replace('T', ' ', $date) . ':00',
                $venue,
                $hs,
                $as,
                $status,
                $id
            ]);

            flash('Partido actualizado.');
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO matches
                 (home_team_id, away_team_id, match_date,
                  venue, home_score, away_score, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $home,
                $away,
                str_replace('T', ' ', $date) . ':00',
                $venue,
                $hs,
                $as,
                $status
            ]);

            flash('Partido registrado.');
        }
    } catch (PDOException $ex) {
        flash('No se pudo guardar el partido. Revisa los datos.', 'error');
    }

    redirect('matches');
}

/* ELIMINAR PARTIDOS */
if (
    $page === 'match_delete' &&
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    $stmt = $pdo->prepare(
        'DELETE FROM matches WHERE id = ?'
    );

    $stmt->execute([
        (int) ($_POST['id'] ?? 0)
    ]);

    flash('Partido eliminado.');
    redirect('matches');
}

/* MOSTRAR PÁGINAS */
include __DIR__ . '/includes/header.php';

$view = __DIR__ . '/pages/' . $page . '.php';

if (file_exists($view)) {
    include $view;
} else {
    echo '<div class="alert error">Página no encontrada.</div>';
}

include __DIR__ . '/includes/footer.php';
