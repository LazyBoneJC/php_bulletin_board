<?php
require_once("conn.php");

header("Content-type:application/json;charset=utf-8");

// 實作 API 時，如過遇到 error 通常是回傳一個 JSON 格式的錯誤訊息
// 而不是導回 index.php
// 回傳的 json 格式可以自定義
if (empty($_POST['content'])) {
  $json = array(
    "ok" => false,
    "message" => "Error: empty content."
  );
  $response = json_encode($json);
  // echo $response;
  die("Error: empty content.");
}

// 也可以用 SESSION 機制實作，但會比較麻煩
// 所以本例子會先寫死一個 username，方便示範
$username = $_POST['username'];
$content = $_POST['content'];

// 改用 Prepared statement
$sql = "INSERT INTO comments (username, content) VALUES (?, ?)";

// create a prepared statement
$stmt = $conn->prepare($sql);

// bind parameters
$stmt->bind_param('ss', $username, $content);

try {
  $stmt->execute();
} catch (Exception $e) {
  $json = array(
    "ok" => false,
    "message" => $e->getMessage()
  );
  $response = json_encode($json);
  // echo $response;
  die("Error:" . $e->getMessage());
}

$json = array(
  "ok" => true,
  "content" => "Success!"
);

$response = json_encode($json);
echo $response;
exit();
?>