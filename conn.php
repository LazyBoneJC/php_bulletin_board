<?php
  $server_name = "localhost";
  $username = "walter";
  $password = "walter";
  $db_name = "walter";

  try {
    $conn = new mysqli($server_name, $username, $password, $db_name);
    $conn->query("SET NAMES UTF8");
    $conn->query("SET time_zone = '+8:00'");
  } catch (Exception $e) {
    die("資料庫連線錯誤：" . $e->getMessage());
  }
?>