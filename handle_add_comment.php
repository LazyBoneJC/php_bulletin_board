<?php
session_start();

require_once("conn.php");
require_once("utils.php");

if (empty($_POST['content'])) {
  header("Location: index.php?errCode=1");
  die("錯誤：資料不齊全！");
}

// $user = getUserFromUsername($_SESSION['username']);
// $nickname = $user['nickname'];

// 資料庫正規化：update
$username = $_SESSION['username'];
$content = $_POST['content'];
// $sql = sprintf("INSERT INTO comments (nickname, content) VALUES ('%s', '%s')", $nickname, $content);

// [2022/11/23 update]: 查詢會員權限，1代表一般使用者，0代表遭到停權的使用者，2代表管理員。
$row = getUserRoleFromUsername($username);
$userRole = $row['role'];

// 判斷使用者權限
if ($userRole == 1 || $userRole == 2) {
  // 改用 Prepared statement
  $sql = "INSERT INTO comments (username, content) VALUES (?, ?)";

  // create a prepared statement
  $stmt = $conn->prepare($sql);

  // bind parameters
  $stmt->bind_param('ss', $username, $content);

  try {
    // execute query
    $stmt->execute();
    // $result = $conn->query($sql);
  } catch (Exception $e) {
    echo $sql . "<br>";
    die("新增錯誤：" . $e->getMessage());
  }
} else {
  header("Location: index.php?errCode=2");
  die("無此權限！");
}

header("Location: index.php");
exit();
?>