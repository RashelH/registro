function validarRecaptcha() {
	var response = grecaptcha.getResponse();
	if (response.length == 0) {
	  // No se ha completado el reCAPTCHA
	  return false;
	} else {
	  // El reCAPTCHA se ha completado correctamente
	  return true;
	}
}

with (document.login) {
	onsubmit = function(e) {
		e.preventDefault();
		ok = true;
		if (username.value == "") {
			ok = false;
			alert("Debe escribir un nombre de usuario");
			username.focus();
		}
		if (password.value == "") {
			ok = false;
			alert("Debe escribir su password");
			password.focus();
		}

		// Validación de reCAPTCHA
		if (!validarRecaptcha()) {
			ok = false;
			alert("Por favor complete el reCAPTCHA.");
		}

		if (ok) {
			submit();
		}
	};

	// Agregar evento de click al botón de submit para validar el reCAPTCHA antes de enviar el formulario
	document.getElementById('submit-btn').addEventListener('click', function(e) {
		if (!validarRecaptcha()) {
			e.preventDefault();
			alert("Por favor complete el reCAPTCHA.");
		}
	});
}
