<?php

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login y Register</title>
    <!-- icons -->
    <script src="https://kit.fontawesome.com/c02f88807d.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet" />
  
    <!-- css reset -->
    <link rel="stylesheet" href="../css/estilologin.css" />
    <link rel="stylesheet" href="../css/estilologin2.css" />
    <link rel="stylesheet" href="../css/estilologin3.css" />
    
</head>

<body>
<main>
    <!-- formulario de login y registro -->
    <section class="form-all">
      <div class="contenedor-todo">
          <div class="caja-trasera">
              <!-- iniciar sesión -->
              <div class="caja-trasera-login">
                  <h3>
                      <span class="questions-marks-styles">¿</span>Ya tienes una cuenta
                      <span class="questions-marks-styles">?</span>
                  </h3>
                  <p>Inicia sesión para entrar en la página</p>
                  <button type="button" id="btn-login">Login<i class="fa-solid fa-right-to-bracket"></i></button>
              </div>
              <!-- registrarse -->
              <div class="caja-trasera-register">
                  <h3>
                      <span class="questions-marks-styles">¿</span>Aún no tienes una cuenta
                      <span class="questions-marks-styles">?</span>
                  </h3>
                  <p>Regístrate gratis para que puedas iniciar sesión</p>
                  <button type="button" id="btn-register">Registrate<i class="fa-solid fa-clipboard-list"></i></button>
              </div>
          </div>
          <div class="contenedor-login-register">
              <!-- login -->
              <form id="formulariologin" action="../php/Validacionusuario.php" method="POST" class="formulario-login">
                  <h2>Iniciar Sesión</h2>
                  <div class="card--label-input" id="grupo__usuario">
                      <input type="text" name="Usuario" id="login-usuario" placeholder="Usuario" class="formulario-input border-general" required/>
                      <label for="Usuario" class="form-label">Usuario</label>
                      <p class="formulario__input-error">El usuario tiene que ser de 4 a 16 dígitos y solo puede contener números, letras y guion bajo.</p>
                  </div>
                  <div class="card--label-input" id="grupo__password">
                      <input type="password" name="Password" id="login-password" placeholder="Contraseña" class="formulario-input border-general" required/>
                      <label for="Password" class="form-label">Contraseña</label>
                      <p class="formulario__input-error">La contraseña tiene que ser de 4 a 12 dígitos.</p>
                      <button type="button" id="btn-password-1" class="btn-password btn-password1" title="show password">
                          <i class="fa-solid fa-eye"></i>
                      </button>
                  </div>
                  <div class="container-btn-iniciar-sesion">
                      <button type="submit" name="login" class="btn-forms">Iniciar sesión</button>
                  </div>
              </form>
<!-- FORMULARIO REGISTRO -->
<form id="formularioregistro" action="../php/Registrarusario.php" method="POST" class="formulario-register">
    <h2>Registro</h2>
    <div class="card--label-input" id="grupo__user">
        <input type="text" id="usuario" name="Usuario" placeholder="Usuario" class="formulario-input border-general" required />
        <label for="Usuario" class="form-label">Usuario</label>
        <span id="usuario-existe" class="error-message">Usuario en uso: elige otro nombre de usuario</span>
    </div>
    <div class="card--label-input" id="grupo__email">
        <input type="text" id="correo" name="Correo" placeholder="Correo Electrónico" class="formulario-input border-general" required />
        <label for="Correo" class="form-label">Correo Electrónico</label>
        <span id="correo-existe" class="error-message">Correo en uso: elige otro correo electrónico</span>
        <p class="formulario__input-error-register">El correo solo puede contener letras, números, puntos, guiones y guion bajo.</p>
    </div>
    <div class="card--label-input" id="grupo__password2">
        <input type="password" placeholder="Contraseña" name="Password" id="register-password2" class="formulario-input border-general" />
        <label for="register-password" class="form-label">Contraseña</label>
        <p class="formulario__input-error-register">La contraseña tiene que ser de 4 a 12 dígitos.</p>
        <button type="button" id="btn-password-2" class="btn-password btn-password2" title="show password">
            <i class="ri-eye-fill"></i>
        </button>
    </div>
    <div class="card--label-input">
        <input type="password" name="ConfirmPassword" placeholder="Confirmar Contraseña" class="formulario-input border-general" required />
        <label for="ConfirmPassword" class="form-label">Confirmar Contraseña</label>
    </div>
    <div class="card--label-input">
        <label for="lang">Tipo de usuario:</label>
        <select id="lang" name="TipoUsuario" class="formulario-input border-general">
            <option value="Postulante">Postulante</option>
            <option value="Empleador">Empleador</option>
            <option value="Empresa">Empresa</option>
        </select>
    </div>
    <div class="container-btn-register">
        <button type="submit" name="register" id="submitBtn" class="btn-forms btn-register">Continuar</button>
    </div>
</form>
<!-- FIN FORMULARIO REGISTRO -->
          </div>
      </div>
      </section>
      <main>
      <?php if (isset($_GET['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR',
                    text: 'El usuario o la contraseña son incorrectos',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '<?php echo htmlspecialchars($_GET['success']); ?>',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/estilologin.js"></script>
    <script src="../js/usuarioexistente.js"></script>
    <script src="jquery/jquery-3.3.1.min.js"></script>    

    
  </body>
</html>
