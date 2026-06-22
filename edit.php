<?php
include 'db.php';
$post_id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    
    $conn->query("UPDATE posts SET title = '$title', content = '$content' WHERE id = $post_id");
    header("Location: view.php?id=$post_id");
    exit;
}

$res = $conn->query("SELECT * FROM posts WHERE id = $post_id");
$post = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>게시글 수정</title>
    <style>
        body { font-family: sans-serif; margin: 40px; background: #f9f9f9; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border: 1px solid #ddd; border-radius: 5px; }
        input[type=text], textarea { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { background: #0066cc; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>✏️ 게시글 수정하기</h2>
        <form action="edit.php?id=<?= $post_id ?>" method="post">
            <label><b>제목</b></label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <label><b>내용</b></label>
            <textarea name="content" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
            <br><br>
            <button type="submit">수정 완료</button>
            <a href="view.php?id=<?= $post_id ?>" style="margin-left: 10px; color:#666; text-decoration:none;">취소</a>
        </form>
    </div>
</body>
</html>
