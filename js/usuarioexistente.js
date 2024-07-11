
        function verificarUsuarioOCorreo(campo, valor) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'usuarioexistente.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var respuesta = JSON.parse(xhr.responseText);
                    if (campo === 'Usuario' && respuesta.usuario === 'existe') {
                        document.getElementById('usuario-existe').style.display = 'block';
                        document.getElementById('usuario').classList.add('error-input');
                        document.getElementById('submitBtn').disabled = true;
                    } else if (campo === 'Usuario') {
                        document.getElementById('usuario-existe').style.display = 'none';
                        document.getElementById('usuario').classList.remove('error-input');
                        document.getElementById('submitBtn').disabled = false;
                    }
                    if (campo === 'Correo' && respuesta.correo === 'existe') {
                        document.getElementById('correo-existe').style.display = 'block';
                        document.getElementById('correo').classList.add('error-input');
                        document.getElementById('submitBtn').disabled = true;
                    } else if (campo === 'Correo') {
                        document.getElementById('correo-existe').style.display = 'none';
                        document.getElementById('correo').classList.remove('error-input');
                        document.getElementById('submitBtn').disabled = false;
                    }
                }
            };
            xhr.send(campo + '=' + encodeURIComponent(valor));
        }

        document.getElementById('usuario').addEventListener('input', function() {
            verificarUsuarioOCorreo('Usuario', this.value);
        });

        document.getElementById('correo').addEventListener('input', function() {
            verificarUsuarioOCorreo('Correo', this.value);
        });

        document.getElementById('formularioregistro').addEventListener('submit', function(e) {
            if (document.getElementById('usuario').classList.contains('error-input') || 
                document.getElementById('correo').classList.contains('error-input')) {
                e.preventDefault();
                alert('Por favor, corrija los errores antes de continuar.');
            }
        });
   