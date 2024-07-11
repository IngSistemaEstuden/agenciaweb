// declaracion de variables
const btnLogin = document.querySelector("#btn-login");
const btnRegister = document.querySelector("#btn-register");
const formlogin = document.querySelector(".formulario-login");
const formRegister = document.querySelector(".formulario-register");
const contenedorLoginRegister = document.querySelector(
  ".contenedor-login-register"
);
const cajaTraseraLogin = document.querySelector(".caja-trasera-login");
const cajaTraseraRegister = document.querySelector(".caja-trasera-register");
const cajatresera = document.querySelector(".caja-trasera");
let anchoPage = () => {
  if (window.innerWidth > 850) {
    cajaTraseraLogin.style.display = "block";
    cajaTraseraRegister.style.display = "block";
    cajatresera.style.marginTop = "0px";
    formlogin.style.width = "90%";
  } else {
    cajaTraseraRegister.style.display = "block";
    formlogin.style.width = "100%";
    cajaTraseraRegister.style.opacity = "1";
    cajaTraseraLogin.style.display = "none";
    formlogin.style.display = "block";
    formRegister.style.display = "none";
    contenedorLoginRegister.style.left = "0px";
    cajatresera.style.marginTop = "20px";
  }
};

let login = () => {
  if (window.innerWidth > 850) {
    formlogin.style.display = "block";
    contenedorLoginRegister.style.left = "10px";
    formRegister.style.display = "none";
    cajaTraseraRegister.style.opacity = "1";
    cajaTraseraLogin.style.opacity = "0px";
  } else {
    formlogin.style.display = "block";
    contenedorLoginRegister.style.left = "0px";
    formlogin.style.width = "100%";
    formRegister.style.display = "none";
    cajaTraseraRegister.style.display = "block";
    cajaTraseraLogin.style.display = "none";
    cajatresera.style.marginTop = "20px";
  }
};
let register = () => {
  if (window.innerWidth > 850) {
    formRegister.style.display = "block";
    contenedorLoginRegister.style.left = "410px";
    formlogin.style.display = "none";
    cajaTraseraRegister.style.opacity = "0";
    cajaTraseraLogin.style.opacity = "1";
  } else {
    formRegister.style.display = "block";
    contenedorLoginRegister.style.left = "0px";
    formlogin.style.display = "none";
    cajaTraseraRegister.style.display = "none";
    cajaTraseraLogin.style.display = "block";
    cajaTraseraLogin.style.opacity = "1";
    cajatresera.style.marginTop = "20px";
  }
};
if (window.innerWidth > 850) {
  window.addEventListener("resize", anchoPage);
  anchoPage();
}
anchoPage();
btnRegister.addEventListener("click", register);
btnLogin.addEventListener("click", login);

// validacion de inputs
let inputs = document.querySelectorAll("input");
for (let i = 0; i < inputs.length; i++) {
  inputs[i].addEventListener("keyup", function () {
    if (this.value.length >= 1) {
      this.nextElementSibling.classList.add("fijar");
    } else {
      this.nextElementSibling.classList.remove("fijar");
    }
  });
}

//fucion de boton password
let btnPassword1 = document.querySelector(".btn-password1");
let icon = document.querySelector(".fa-eye");

let password1 = () => {
  let tipo = document.getElementById("login-password");
  if (tipo.type == "password") {
    tipo.type = "text";
    icon.classList.replace("fa-eye", "ri-eye-close-fill");
  } else {
    tipo.type = "password";
    icon.classList.replace("ri-eye-close-fill", "fa-eye");
  }
};
// register
let btnPassword2 = document.querySelector("#btn-password-2");
let icon2 = btnPassword2.querySelector("i");

let password2 = () => {
    let tipo2 = document.getElementById("register-password2");
    if (tipo2.type == "password") {
        tipo2.type = "text";
        icon2.classList.replace("ri-eye-fill", "ri-eye-close-fill");
    } else {
        tipo2.type = "password";
        icon2.classList.replace("ri-eye-close-fill", "ri-eye-fill");
    }
};

btnPassword2.addEventListener("click", password2);

btnPassword1.addEventListener("click", password1);
btnPassword2.addEventListener("click", password2);

// validacion login
const btnFormLogin = document.querySelector("#btn-login-form");
const formularioLogin = document.querySelector("#form-login");
const inputsLogin = document.querySelectorAll("#form-login input");