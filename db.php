<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

// Azure 환경변수가 설정되어 있으면 그것을 쓰고, 없으면 로컬 우분투 기본값을 씁니다.
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "board_user";
$pass = getenv('DB_PASS') ?: "password123!";
$dbName = getenv('DB_NAME') ?: "board_db";

$conn = @new mysqli($host, $user, $pass, $dbName);
if ($conn->connect_error) {
    die(" DB 연결 실패: " . $conn->connect_error);
}
?>
