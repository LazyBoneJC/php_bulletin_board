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

  // 改用 Prepared statement
  // Hard Delete
  // $sql = "DELETE FROM comments WHERE id = ? AND username = ?";

  // Soft Delete
  $sql = "UPDATE comments SET is_deleted = 1 WHERE id = ? AND username = ?";

  // create a prepared statement
  $stmt = $conn->prepare($sql);

  // bind parameters
  $stmt->bind_param('is', $id, $username);

  try {
    // execute query
    $stmt->execute();
    // $result = $conn->query($sql);
  } catch (Exception $e) {
    echo $sql . "<br>";
    die("刪除錯誤：" . $e->getMessage());
  }

  header("Location: index.php");
?>
<a href="index.php">Go back</a>