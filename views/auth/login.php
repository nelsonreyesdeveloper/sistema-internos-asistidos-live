<div class="contenedor-app">
    <div class="imagen"></div>
    <div class="app">

        <form class="formulario" method="POST" action="/">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" required id="email" placeholder="Tu Email" name="email" />
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input type="password" required id="password" placeholder="Tu Password" name="password" />
            </div>

            <?php
            include_once __DIR__ . "/../templates/alertas.php";
            ?>
            <input type="submit" class="boton" value="Iniciar Sesión">
        </form>
    </div>
</div>