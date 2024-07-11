let  txtUsuario =document.querySelector('#login-usuario')
let  txtContrasenna =document.querySelector('#login-password')
let usuarioConectado = JSON.parse(localStorage.getItem("usuarioConectado"));
let opcionesNav = document.querySelectorAll("nav a");

// nav

const rederigirUsuarios = () => {
  switch (usuarioConectado.rol) {
    case 1:
      window.location.href = "mantenimiento.html";
      break;
    case 2:
      window.location.href = "index.html";
      break;
    case 3:
      window.location.href = "index.html";
      break;
    case 4:
      window.location.href = "dash-usuario.html";
      break;
  }
};

const iniciarSesion = () => {
  let usuarioLogin = txtUsuario.value;
  let contrasennaLogin = txtContrasenna.value;
  validarCredenciales(usuarioLogin, contrasennaLogin);
};

const validarCredenciales = (usuarioLogin, contrasennaLogin) => {
  let usuarioValidado = false;
  usuarios.forEach((objUsuario) => {
    if (objUsuario.usuario == usuarioLogin && objUsuario.contrasenna == contrasennaLogin ){
        usuarioValidado = true;
        localStorage.setItem("usuarioConectado", JSON.stringify(objUsuario));
    }
  });

  if (usuarioValidado == false) {
    Swal.fire({
      icon: "warning",
      title: "No se a podido iniciar sesión",
      text: "El correo del usuario o la contraseña son incorrectos",
      confirmButtonText: "Entendido",
    });
    txtUsuarioLogin.classList.add("input-invalid");
    txtContrasennaLogin.classList.add("input-invalid");
  } else {
    txtUsuarioLogin.classList.remove("input-invalid");
    txtContrasennaLogin.classList.remove("input-invalid");
    txtUsuarioLogin.classList.add("input-valid");
    txtContrasennaLogin.classList.add("input-valid");
    Swal.fire({
      icon: "success",
      title: "Inicio de sesión correcto",
      text: "Bienvenido",
      confirmButtonText: "Entendido",
    })
    .then(() => {
      rederigirUsuarios();
    });
  }
};
