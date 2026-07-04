<?php
include 'db.php';

// 유저 검색 처리
$search_user = "";
$user_results = null;
if (isset($_GET['search_user']) && !empty(trim($_GET['search_user']))) {
    $search_user = $conn->real_escape_string($_GET['search_user']);
    $user_results = $conn->query("SELECT username, created_at FROM users WHERE username LIKE '%$search_user%'");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>통합 웹 서버 포털</title>
    <style>
        body { font-family: sans-serif; margin: 50px; background: #fafafa; text-align: center; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 8px; }
        .btn-box { display: flex; justify-content: space-around; margin: 30px 0; }
        .btn { padding: 20px 40px; font-size: 18px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-free { background: #3F51B5; }
        .btn-qna { background: #009688; }
        .search-box { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: left; }
        input[type=text] { width: 75%; padding: 8px; }
        button { padding: 8px 15px; background: #333; color: white; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <h1> 17기 박채영 게시판</h1>
        
        <?php if(isset($_SESSION['username'])): ?>
            <p>안녕하세요, <b><?= htmlspecialchars($_SESSION['username']) ?></b>님! 
               [<a href="logout.php" style="color:red; text-decoration:none;">로그아웃</a>]</p>
        <?php else: ?>
            <p>로그인이 필요합니다. [<a href="login.php">로그인</a>] | [<a href="register.php">회원가입</a>]</p>
        <?php endif; ?>

        <hr>
        <h3>💬 이동할 주제의 게시판을 선택하세요</h3>
        <div class="btn-box">
            <a href="board.php?type=free" class="btn btn-free">📢 자유 게시판</a>
            <a href="board.php?type=qna" class="btn btn-qna">❓ 질문 게시판</a>
        </div>

        <!-- 유저 검색 섹션 -->
        <div class="search-box">
            <h3>👥 시스템 유저 검색</h3>
            <form action="index.php" method="get">
                <input type="text" name="search_user" value="<?= htmlspecialchars($search_user) ?>" placeholder="검색할 유저 아이디 입력...">
                <button type="submit">검색</button>
            </form>
            
            <?php if ($user_results): ?>
                <table>
                    <tr><th>유저 ID</th><th>가입 일시</th></tr>
                    <?php if ($user_results->num_rows > 0): ?>
                        <?php while($u = $user_results->fetch_assoc()): ?>
                            <tr>
                                <td><b><?= htmlspecialchars($u['username']) ?></b></td>
                                <td><?= $u['created_at'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" style="color:#aaa; text-align:center;">검색된 유저가 없습니다.</td></tr>
                    <?php endif; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
