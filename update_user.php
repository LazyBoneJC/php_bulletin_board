<?php
  session_start();

  require_once("conn.php");
  require_once("utils.php");

  if (empty($_POST['nickname'])) {
    header("Location: index.php?errCode=1");
    die("錯誤：資料不齊全！");
  }

  $username = $_SESSION['username'];
  $nickname = $_POST['nickname'];

  // 改用 Prepared statement
  $sql = "UPDATE users SET nickname=? WHERE username=?";

  // create a prepared statement
  $stmt = $conn->prepare($sql);

  // bind parameters
  $stmt->bind_param('ss', $nickname, $username);

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