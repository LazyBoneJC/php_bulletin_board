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
		<a class="board__btn" href="index.php">回留言板</a>
		<a class="board__btn" href="register.php">註冊</a>
		<h1 class="board__title">登入</h1>
		<?php
			if(!empty($_GET['errCode'])) {
				$msg = "Error";
				if($_GET['errCode'] === '1') {
					$msg = "資料不齊全！";
				} else if($_GET['errCode'] === '2') {
					$msg = "帳號或密碼輸入錯誤！";
				}
				echo "<h3 class='errmsg'>錯誤：" . $msg . "</h3>";
			}
		?>
		<form class="board__new-comment-form" action="handle_login.php" method="post">
			<div class="board__nickname">
				<span>帳號：</span>
				<input type="text" name="username">
			</div>
			<div class="board__nickname">
				<span>密碼：</span>
				<input type="password" name="password">
			</div>
			<input class="board__submit-btn" type="submit">
		</form>
	</main>
</body>
</html>