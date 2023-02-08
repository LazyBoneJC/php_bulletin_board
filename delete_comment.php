<?php
session_start();

require_once("conn.php");
require_once("utils.php");

if (empty($_GET['id'])) {
  header("Location: index.php?errCode=1");
  die("錯誤：資料不齊全！");
}

$id = $_GET['id'];
$username = $_SESSION['username'];

// [2022/11/30 update]: 查詢會員權限，1代表一般使用者，0代表遭到停權的使用者，2代表管理員。
$row = getUserRoleFromUsername($username);
$userRole = $row['role'];

// 改用 Prepared statement
// Hard Delete
// $sql = "DELETE FROM comments WHERE id = ? AND username = ?";

if ($userRole == 2) {
  // 如果身份是 Admin，SQL query 就不檢查 username 是否與留言者相同
  $stmt = $conn->prepare("UPDATE comments SET is_deleted = 1 WHERE id = ?");

  // bind parameters
  $stmt->bind_param('i', $id);
} else {
  // Soft Delete
  $sql = "UPDATE comments SET is_deleted = 1 WHERE id = ? AND username = ?";

  // create a prepared statement
  $stmt = $conn->prepare($sql);

  // bind parameters
  $stmt->bind_param('is', $id, $username);
}

try {
  // execute query
  $stmt->execute();
  // $result = $conn->query($sql);
} catch (Exception $e) {
  echo $sql . "<br>";
  die("刪除錯誤：" . $e->getMessage());
}

header("Location: index.php");
exit();
?>