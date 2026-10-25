<section class="auth-card">
    <div class="auth-icon">🏀</div>
    <h1>Iniciar sesión</h1>
    <p>Ingresa para administrar tu liga de básquetbol.</p>

    <form method="POST" action="index.php?page=login">
        <label for="email">Correo electrónico</label>
        <input
            type="email"
            id="email"
            name="email"
            required
            autocomplete="email">

        <label for="password">Contraseña</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password">

        <button type="submit" class="btn">
            Ingresar
        </button>
    </form>

    <p class="auth-link">
        ¿No tienes una cuenta?
        <a href="index.php?page=register">Regístrate aquí</a>
    </p>
</section>