<?php
	session_start();

	require_once("conn.php");
	require_once("utils.php");

	/*
		1. 從 cookie 讀取 PHPSESSID (token)
		2. 從檔案裡讀取 session id 的內容
		3. 放到 $_SESSION
		* ID 有對的話，$username 就會有值，錯誤就是空的 *
	*/
	$username = NULL;
	$user = NULL;
	$userRole = NULL;
	
	if (!empty($_SESSION['username'])) {
		$username = $_SESSION['username'];
		$user = getUserFromUsername($username);
	}

	// 先檢查有沒有 token 在 cookie，有的話再去 DB 裡查表
	// $username = NULL;
	// if (!empty($_COOKIE['token'])) {
	// 	$user = getUserFromToken($_COOKIE['token']);
	// 	$username = $user['username'];
	// }

	$page = 1;
	if(!empty($_GET['page'])) {
		$page = intval($_GET['page']);
	}
	$items_per_page = 5;
	$offset = ($page - 1) * $items_per_page;

	$stmt = $conn->prepare(
		"SELECT ".
			"C.id as id, C.content as content, C.created_at as created_at, ".
			"U.nickname as nickname, U.username as username ".
		"FROM comments AS C ".
		"LEFT JOIN users as U on C.username = U.username ".
		"WHERE C.is_deleted IS NULL ".
		"ORDER BY C.id DESC ".
		"limit ? offset ? "
	);
	$stmt->bind_param("ii", $items_per_page, $offset);

	try {
		// 後新增的留言會排在前面
		// $result = $conn->query("SELECT * FROM comments ORDER BY id DESC");
		$stmt->execute();
	} catch (Exception $e) {
		die("查詢錯誤：" . $e->getMessage());
	}
	$result = $stmt->get_result();

	if($username) {
		$row = getUserRoleFromUsername($username);
		$userRole = $row['role'];
	}
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
		<div>
			<?php if (!$username) { ?>
				<a class="board__btn" href="register.php">註冊</a>
				<a class="board__btn" href="login.php">登入</a>
			<?php } else { ?>
				<a class="board__btn" href="logout.php">登出</a>
				<span class="board__btn update-nickname">編輯暱稱</span>
				<?php if ($userRole == 2) { ?>
					<a class="board__btn" href="admin_panel.php">後台管理</a>
				<?php } ?>
				<form action="update_user.php" method="POST" class="hide board__nickname-form">
					<div class="board__nickname">
						<span>新的暱稱：</span>
						<input type="text" name="nickname">
					</div>
					<input type="submit" class="board__submit-btn">
				</form>
				<h3>你好！<?php echo escape($user['nickname']); ?></h3>
			<?php } ?>
		</div>
		<h1 class="board__title">Comments</h1>
		<?php
			if(!empty($_GET['errCode'])) {
				$msg = "Error";
				if($_GET['errCode'] === '1') {
					$msg = "資料不齊全！";
				} elseif ($_GET['errCode'] === '2') {
					$msg = "無此權限！";
				}
				echo "<h3 class='errmsg'>錯誤：" . $msg . "</h3>";
			}
		?>
		<form class="board__new-comment-form" action="handle_add_comment.php" method="post">
			<!-- <div class="board__nickname">
				<span>暱稱：</span>
				<input type="text" name="nickname">
			</div> -->
			<textarea name="content" rows="5"></textarea>
			<?php if ($username) { ?>
			<input class="board__submit-btn" type="submit">
			<?php } else { ?>
				<h3>登入即可發布留言</h3>
			<?php } ?>
		</form>
		<div class="board__hr"></div>
		<section>
			<?php
				while($row = $result->fetch_assoc()) {
			?>
				<div class="card">
					<div class="card__avatar"></div>
					<div class="card__body">
						<div class="card__info">
							<span class="card__author">
								<?php echo escape($row['nickname']); ?>
								(@<?php echo escape($row['username']); ?>)
							</span>
							<span class="card__time"> | <?php echo escape($row['created_at']); ?></span>
							<?php if($row['username'] === $username || $userRole == 2) { ?>
								<span class="card__edit-btn">
									<a href="update_comment.php?id=<?php echo $row['id'] ?>">編輯</a>
									<a href="delete_comment.php?id=<?php echo $row['id'] ?>">刪除</a>
								</span>
							<?php } ?>
						</div>
						<p class="card__content"><?php echo escape($row['content']); ?></p>
					</div>
				</div>
			<?php } ?>
		</section>
		<!-- 以下：實作分頁功能 -->
		<div class="board__hr"></div>
		<?php
			$stmt = $conn->prepare("SELECT COUNT(id) as count FROM `comments` WHERE is_deleted IS NULL");
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$count = $row['count'];
			$total_page = (int) ceil($count / $items_per_page); // 回傳的是 float 所以要轉換型態
		?>
		<div class="page-info">
			<span>總共有 <?php echo $count; ?> 筆留言，頁數：</span>
			<span><?php echo $page ?> / <?php echo $total_page ?></span>
		</div>
		<div class="paginator">
			<?php if($page !== 1) { ?>
				<a href="index.php?page=1">首頁</a>
				<a href="index.php?page=<?php echo $page - 1 ?> ">上一頁</a>
			<?php } ?>
			<?php if($page !== $total_page) { ?>
				<a href="index.php?page=<?php echo $page + 1 ?> ">下一頁</a>
				<a href="index.php?page=<?php echo $total_page ?>">最後一頁</a>
			<?php } ?>
		</div>
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