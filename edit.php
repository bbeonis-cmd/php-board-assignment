<?php
include 'db.php';
$post_id = (int)$_GET['id'];

$res = $conn->query("SELECT * FROM posts WHERE id = $post_id");
$post = $res->fetch_assoc();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $post['user_id']) {
    die("<script>alert('권한이 없습니다.'); location.href='index.php';</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    
    $conn->query("UPDATE posts SET title = '$title', content = '$content' WHERE id = $post_id");

    // 새로운 파일 업로드 시 기존 파일 수정 처리
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $ori_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        $ext = pathinfo($ori_name, PATHINFO_EXTENSION);
        $save_name = time() . '_' . uniqid() . '.' . $ext;
        $upload_dir = '/var/www/html/uploads/';

        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $save_name)) {
            // 기존 파일 서버에서 완전 삭제 연동
            $old_file = $conn->query("SELECT * FROM files WHERE post_id = $post_id")->fetch_assoc();
            if ($old_file) {
                @unlink($upload_dir . $old_file['save_name']);
                $conn->query("UPDATE files SET ori_name='$ori_name', save_name='$save_name', file_size=$file_size WHERE post_id=$post_id");
            } else {
                $conn->query("INSERT INTO files (post_id, ori_name, save_name, file_size) VALUES ($post_id, '$ori_name', '$save_name', $file_size)");
            }
        }
    }
    header("Location: view.php?id=$post_id"); exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>게시글 수정</title>
    <style>body { font-family: sans-serif; margin: 40px; } .container { max-width: 700px; margin: auto; } input[type=text], textarea { width: 100%; padding: 8px; margin: 8px 0; } button { background: #0066cc; color: white; padding: 10px 15px; border: none; cursor: pointer; }</style>
</head>
<body>
    <div class="container">
        <h2>✏️ 게시글 및 첨부파일 수정하기</h2>
        <form action="edit.php?id=<?= $post_id ?>" method="post" enctype="multipart/form-data">
            <label><b>제목</b></label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <label><b>내용</b></label>
            <textarea name="content" rows="8" required><?= htmlspecialchars($post['content']) ?></textarea>
            <label><b>새로운 파일로 교체 (수정 시 선택)</b></label><br>
            <input type="file" name="file"><br><br>
            <button type="submit">수정 완료</button>
        </form>
    </div>
</body>
</html>
