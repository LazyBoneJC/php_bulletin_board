<?php
  session_start();
  require_once("conn.php");

  if (empty($_POST['nickname']) || empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: register.php?errCode=1");
    die("資料不齊全！");
  }

  $nickname = $_POST['nickname'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // $sql = sprintf("INSERT INTO users (`nickname`, `username`, `password`) VALUES ('%s', '%s', '%s')", $nickname, $username, $password);
  $sql = "INSERT INTO users (`nickname`, `username`, `password`) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('sss', $nickname, $username, $password);

  try {
    // $result = $conn->query($sql);
    $stmt->execute();
  } catch (Exception $e) {
    $code = $conn->errno;
    if($code === 1062) {
      header("Location: register.php?errCode=2");
    }
    die("出現錯誤：" . $e->getMessage());
  }

  echo "註冊成功！" . "<br>";

  // 跳轉回首頁
  $_SESSION['username'] = $username;
  header("Location: index.php");
?>