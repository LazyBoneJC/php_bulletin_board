<?php
session_start();
require_once("conn.php");
require_once("utils.php");

$id = $_GET['id'];
$adjustedRole = $_GET['role'];

$username = NULL;
$user = NULL;
$userRole = NULL;

if (!empty($_SESSION['username'])) {
  $username = $_SESSION['username'];
  $user = getUserFromUsername($username);
  $row = getUserRoleFromUsername($username);
  $userRole = $row['role'];
}

// 檢查是否為管理員身份
if ($userRole == 2) {
  $stmt = $conn->prepare("UPDATE users SET users.role = ? WHERE users.id = ?");
  $stmt->bind_param("ii", $adjustedRole, $id);
  try {
    $stmt->execute();
  } catch (Exception $e) {
    die("Error：" . $e->getMessage());
  }
} else {
  header("Location: index.php?errCode=2");
  exit();
}

header("Location: admin_panel.php");
exit();
?>