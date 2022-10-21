<?php
  // require_once("conn.php");

  // 刪除 Token
  // $token = $_COOKIE['token'];
  // $sql = sprintf("DELETE FROM tokens WHERE token = '%s'", $token);
  // try {
  //   $conn->query($sql);
  // } catch (Exception $e) {
  //   die("出現錯誤：" . $e->getMessage());
  // }

  // 把 cookie 設置為過期的 -> 就會被拋棄 = cookie 被清除
  // 瀏覽器在帶下一個 request 上來就不會有 token 這個 cookie 了
  // setcookie("token", "", time() - 3600);

  // 非常簡單，直接把 session 清除掉即可
  // 注意：要用到 session 的地方都必須 call session_start()
  session_start();
  session_destroy();
  header("Location: index.php");
?>