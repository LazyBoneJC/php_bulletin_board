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

  header("Location: index.php");
?>
<a href="index.php">Go back</a>