// Validasi Show Password
const togglePassword = document.getElementById("togglePassword");
const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
const passwordField = document.getElementById("password");
const confirmPasswordField = document.getElementById("confirm_password");

togglePassword.addEventListener("click", function () {
	// Toggle the type attribute
	const type =
		passwordField.getAttribute("type") === "password" ? "text" : "password";
	passwordField.setAttribute("type", type);

	// Toggle the icon
	this.setAttribute(
		"name",
		type === "password" ? "eye-off-outline" : "eye-outline",
	);
});

toggleConfirmPassword.addEventListener("click", function () {
	// Toggle the type attribute
	const type =
	confirmPasswordField.getAttribute("type") === "password" ? "text" : "password";
	confirmPasswordField.setAttribute("type", type);

	// Toggle the icon
	this.setAttribute(
		"name",
		type === "password" ? "eye-off-outline" : "eye-outline",
	);
});

// Validasi untuk form number sebanyak 11/12 ankas
function validatePhoneNumber(input) {
	if (input.value.length > 11 && input.value.length > 12) {
		input.value = input.value.slice(0, 12); // Potong angka yang lebih dari 12 digit
	}
}

//Fungsi untuk meembuat hapus jika ada kesalahan tidka sengaja hapus.php
function confirmDelete(id) {
	if (confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) {
		// Redirect to the deletion script with the given ID
		window.location.href = `Users/hapus.php?id=${id}`;
	}
}

function previewImage(event) {
	var reader = new FileReader();
	reader.onload = function () {
		var output = document.getElementById("previewImg");
		output.src = reader.result;
		output.style.display = "block";
	};
	reader.readAsDataURL(event.target.files[0]);
}
