<?php
  // 使用內建 session 機制
  // 跟 php 說我要開始用 session 了
  session_start();

  require_once("conn.php");
  require_once("utils.php");

  if (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?errCode=1");
    die("資料不齊全！");
  }

  $username = $_POST['username'];
  $password = $_POST['password'];

  // $sql = sprintf("SELECT * FROM users WHERE username = '%s' AND `password` = '%s'", $username, $password);

  // 因應密碼改用 hash sum 儲存所做的更改
  // $sql = sprintf("SELECT * FROM users WHERE username = '%s'", $username);
  $sql = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $username);

  try {
    // $result = $conn->query($sql);
    $stmt->execute();
    $result = $stmt->get_result();
  } catch (Exception $e) {
    die("出現錯誤：" . $e->getMessage());
  }

  // 若沒有查到使用者
  if($result->num_rows === 0) {
    header("Location: login.php?errCode=2");
    exit(); //相當於 die()
  }

  // 有查到使用者
  $row = $result->fetch_assoc();
  if(password_verify($password, $row['password'])) {
    /* 
      這樣短短一行，背後做了很多事
      1. 產生 session id (token)
      2. 把 username 寫入檔案
      3. set-cookie: session-id
    */
    $_SESSION['username'] = $username;
    header("Location: index.php");
  } else {
    header("Location: login.php?errCode=2");
  }
?>