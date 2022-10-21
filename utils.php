<?php
  require_once("conn.php");

  // 產生隨機 Token
  // function generateToken() {
	// 	$s = '';
	// 	for($i = 0; $i < 16; $i++) {
	// 		$s .= chr(rand(65, 90));
	// 	}
	// 	return $s;
	// }

  // function getUserFromToken($token) {
  //     global $conn; // 特別注意！要用到 global 的變數，php 需要先宣告

  //     $sql = sprintf("SELECT username FROM tokens WHERE token='%s'", $token);
  //     try {
  //       $result = $conn->query($sql);
  //     } catch (Exception $e) {
  //       die("出現錯誤：" . $e->getMessage());
  //     }
  //     $row = $result->fetch_assoc();
  //     $username = $row['username'];
      
  //     $sql = sprintf("SELECT * FROM users WHERE username='%s'", $username);
  //     try {
  //       $result = $conn->query($sql);
  //     } catch (Exception $e) {
  //       die("出現錯誤：" . $e->getMessage());
  //     }
  //     $row = $result->fetch_assoc();

  //     return $row; // 會有 id, nickname, username, password
  // }

  function getUserFromUsername($username) {
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

  function escape($str) {
    return htmlspecialchars($str);
  }
?>