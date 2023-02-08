<?php
require_once("conn.php");

function getUserRoleFromUsername($username)
{
  global $conn;

  $sql = "SELECT users.role FROM users WHERE users.username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $username);
  try {
    $stmt->execute();
  } catch (Exception $e) {
    die("權限查詢錯誤：" . $e->getMessage());
  }
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  
  return $row;
}

function getUserFromUsername($username)
{
  global $conn; // 特別注意！要用到 global 的變數，php 需要先宣告

  // $sql = sprintf("SELECT * FROM users WHERE username='%s'", $username);
  $sql = "SELECT * FROM users WHERE username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $username);

  try {
    $stmt->execute();
    // $result = $conn->query($sql);
  } catch (Exception $e) {
    die("出現錯誤：" . $e->getMessage());
  }

  $result = $stmt->get_result();
  $row = $result->fetch_assoc();

  return $row; // 會有 id, nickname, username, password
}

function escape($str)
{
  return htmlspecialchars($str);
}
?>