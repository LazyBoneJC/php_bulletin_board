<?php
	session_start();
	require_once("conn.php");
	require_once("utils.php");

  $id = $_GET['id'];

	$username = NULL;
	$user = NULL;
	if (!empty($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$user = getUserFromUsername($username);
	}

	$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ?");
  $stmt->bind_param("i", $id);
	try {
		$stmt->execute();
	} catch (Exception $e) {
		die("查詢錯誤：" . $e->getMessage());
	}
	$result = $stmt->get_result();
  $row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>留言板</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header class="warning">
		注意！本站為練習用網站，因教學用途刻意忽略資安的實作，註冊時請勿使用任何真實的帳號密碼。
	</header>
	<main class="board">
		<h1 class="board__title">編輯留言</h1>
		<?php
			if(!empty($_GET['errCode'])) {
				$msg = "Error";
				if($_GET['errCode'] === '1') {
					$msg = "資料不齊全！";
				}
				echo "<h3 class='errmsg'>錯誤：" . $msg . "</h3>";
			}
		?>
		<form class="board__new-comment-form" action="handle_update_comment.php" method="post">
			<textarea name="content" rows="5"><?php echo $row['content']; ?></textarea>
      <!-- 因為按下 update 按鈕後，會跳轉到 handle_update_comment.php，id的值也必須以某種形式傳遞過去 -->
      <!-- 這時後就可以放在隱藏的 input -->
      <input type="hidden" name="id" value="<?php echo $row['id']?>">
			<input class="board__submit-btn" type="submit">
		</form>
	</main>
	<script>
		let btn = document.querySelector(".update-nickname");
		btn.addEventListener("click", function() {
			let form = document.querySelector(".board__nickname-form");
			form.classList.toggle("hide");
		});
	</script>
</body>
</html>