<?php
session_start();
session_unset();    // Bersihkan semua variabel session
session_destroy();  // Hancurkan session
header("Location: login.php"); // Lempar kembali ke pintu depan
exit();
?>