<?php
include 'db.php';

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    board_type VARCHAR(20) NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    ori_name VARCHAR(255) NOT NULL,
    save_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
";

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) { $result->free(); }
    } while ($conn->next_result());
    echo "<h2>✅ Azure 클라우드 데이터베이스 세팅 완료!</h2>";
    echo "<p><a href='index.php'>여기 부르기 클릭해서 메인 페이지로 이동하기</a></p>";
} else {
    echo " 테이블 생성 실패: " . $conn->error;
}
?>
