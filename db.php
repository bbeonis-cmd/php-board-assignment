<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

$host = "localhost";
$user = "board_user";
$pass = "password123!";
$dbName = "board_db";

$conn = @new mysqli($host, $user, $pass, $dbName);
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
?>
