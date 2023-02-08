<?php
require_once("conn.php");

// 計算留言總數
$stmt = $conn->prepare("SELECT * FROM comments WHERE is_deleted IS NULL");

try {
  $stmt->execute();
} catch (Exception $e) {
  die("Error：" . $e->getMessage());
}
$result = $stmt->get_result();
$total_comments = $result->num_rows;

// Select 留言
$items_per_load = 6;
$from = $total_comments;

if (!empty($_GET['load'])) {
  $from -= $items_per_load * (intval($_GET['load']) - 1);
}

// mysqli_stmt::prepare
$stmt = $conn->prepare(
  "SELECT " .
  "C.id AS id, C.content AS content, C.created_at AS created_at, " .
  "U.nickname AS nickname, U.username AS username " .
  "FROM comments AS C " .
  "LEFT JOIN users AS U ON C.username = U.username " .
  "WHERE C.is_deleted IS NULL AND C.id <= ? " .
  "ORDER BY C.id DESC " .
  "LIMIT ?"
);

$stmt->bind_param("ii",$from ,$items_per_load);

try {
  $stmt->execute();
} catch (Exception $e) {
  die("查詢錯誤：" . $e->getMessage());
}
$result = $stmt->get_result();

$comments = array();

while ($row = $result->fetch_assoc()) {
  array_push($comments, array(
    "id" => $row['id'],
    "username" => $row['username'],
    "nickname" => $row['nickname'],
    "content" => $row['content'],
    "created_at" => $row['created_at']
  )
  );
}

$json = array(
  "comments" => $comments,
  "total_comments" => $total_comments,
  "items_per_load" => $items_per_load
);

$response = json_encode($json);
header("Content-type:application/json;charset=utf-8");
echo $response;
exit();
?>