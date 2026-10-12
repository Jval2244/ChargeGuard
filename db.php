<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Biarkan kosong jika XAMPP bawaan
$dbname = 'chargeguard';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>