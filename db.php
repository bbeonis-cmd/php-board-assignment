<?php
// 1. 브라우저 화면에 모든 에러를 강제로 표시하라는 명령어
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. PHP 8 이상에서 DB 연결 실패 시 에러로 멈추는 현상 방지
mysqli_report(MYSQLI_REPORT_OFF); 

$host = "localhost";
$user = "board_user";
$pass = ""; 
$dbName = "board_db";

$conn = @new mysqli($host, $user, $pass, $dbName);
if ($conn->connect_error) {
    // DB 연결에 실패하면 500 에러 대신 화면에 원인을 출력하고 멈춥니다.
    die("❌ DB 연결 실패 원인: " . $conn->connect_error);
}
?>
