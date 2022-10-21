<?php
  session_start();

  require_once("conn.php");
  require_once("utils.php");

  if (empty($_POST['content'])) {
    header("Location: update_comment.php?errCode=1&id=".$_POST['id']);
    die("錯誤：資料不齊全！");
  }

  $username = $_SESSION['username'];
  $id = $_POST['id'];
  $content = $_POST['content'];

  // 改用 Prepared statement
  $sql = "UPDATE comments SET content = ? WHERE id = ? AND username = ?";

  // create a prepared statement
  $stmt = $conn->prepare($sql);

  // bind parameters
  $stmt->bind_param('sis', $content, $id, $username);

  try {
    // execute query
    $stmt->execute();
    // $result = $conn->query($sql);
  } catch (Exception $e) {
    echo $sql . "<br>";
    die("更新錯誤：" . $e->getMessage());
  }

  header("Location: index.php");
?>
<a href="index.php">Go back</a>