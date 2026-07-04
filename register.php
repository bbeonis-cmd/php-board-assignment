<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql)) {
        echo "<script>alert('회원가입 성공! 로그인해 주세요.'); location.href='login.php';</script>";
    } else {
        echo "<script>alert('이미 존재하는 아이디입니다.'); history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>회원가입</title>
    <style>body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; } .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; } input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; } button { width: 100%; padding: 10px; background: #2196F3; color: white; border: none; cursor: pointer; }</style>
</head>
<body>
    <div class="box">
        <h2> 회원가입</h2>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <button type="submit">가입하기</button>
        </form>
        <p style="text-align:center; font-size:14px;"><a href="login.php">로그인하러 가기</a></p>
    </div>
</body>
</html>
