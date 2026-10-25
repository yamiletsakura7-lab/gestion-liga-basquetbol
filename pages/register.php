<section class="auth-card">
    <div class="auth-icon">🏆</div>
    <h1>Crear una cuenta</h1>
    <p>Regístrate para gestionar los equipos y partidos.</p>

    <form method="POST" action="index.php?page=register">
        <label for="name">Nombre completo</label>
        <input
            type="text"
            id="name"
            name="name"
            maxlength="100"
            required
            autocomplete="name">

        <label for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="150"
            required
            autocomplete="email">

        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            required
            autocomplete="new-password">

        <small>Utiliza al menos 8 caracteres.</small>

        <button type="submit" class="btn">
            Crear cuenta
        </button>
    </form>

    <p class="auth-link">
        ¿Ya tienes una cuenta?
        <a href="index.php?page=login">Inicia sesión</a>
    </p>
</section>