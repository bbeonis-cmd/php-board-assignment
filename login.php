<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        }
    }
    echo "<script>alert('아이디 또는 비밀번호가 틀렸습니다.'); history.back();</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>로그인</title>
    <style>body { font-family: sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; } .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; } input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; } button { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; cursor: pointer; }</style>
</head>
<body>
    <div class="box">
        <h2> 로그인</h2>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="아이디" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <button type="submit">로그인</button>
        </form>
        <p style="text-align:center; font-size:14px;"><a href="register.php">회원가입 하러 가기</a></p>
    </div>
</body>
</html>
