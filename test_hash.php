<?php
$plain = "password123"; // Cambia esto por la contraseña que quieras
$hash = password_hash($plain, PASSWORD_DEFAULT);
echo "Contraseña original: $plain<br>";
echo "Hash para guardar en la BD:<br><textarea rows='2' cols='80'>$hash</textarea>";
?>