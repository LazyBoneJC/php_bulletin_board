<?php
session_start();

require_once("conn.php");
require_once("utils.php");

$username = NULL;
$user = NULL;
$userRole = NULL;

if (!empty($_SESSION['username'])) {
	$username = $_SESSION['username'];
	$user = getUserFromUsername($username);
}

$page = 1;
if (!empty($_GET['page'])) {
	$page = intval($_GET['page']);
}
$items_per_page = 5; // 一頁可以有幾筆資料
$offset = ($page - 1) * $items_per_page; // 要從第幾比資料以後才開始拿

$stmt = $conn->prepare(
	"SELECT " .
	"U.nickname as nickname, U.username as username, U.id as id, U.created_at as created_at, U.role as role " .
	"FROM users as U " .
	"LIMIT ? OFFSET ? "
);
$stmt->bind_param("ii", $items_per_page, $offset);

try {
	$stmt->execute();
} catch (Exception $e) {
	die("查詢錯誤：" . $e->getMessage());
}
$result = $stmt->get_result();

if ($username) {
	$row = getUserRoleFromUsername($username);
	$userRole = $row['role'];
}

// 如果使用者身份不是 admin，就跳轉回首頁，不能進到後台管理系統
if ($userRole != 2) {
	header("Location: index.php?errCode=2");
	exit();
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
			<a class="board__btn" href="index.php">回首頁</a>
			<h3>你好！
				<?php echo escape($user['nickname']); ?>
			</h3>
			<?php } ?>
		</div>
		<h1 class="board__title">後台管理系統</h1>
		<div class="board__hr"></div>
		<section>
			<?php
      while ($row = $result->fetch_assoc()) {
      ?>
			<div class="admin_panel_card">
				<div class="card__avatar"></div>
				<div class="card__body">
					<div class="card__info">
						<span class="card__user">
							<?php echo escape($row['nickname']); ?>
							(@<?php echo escape($row['username']); ?>)
							<span class="card__time"> |
								<?php echo escape("Created at: " . $row['created_at']); ?>
							</span>
						</span>
						<?php if ($userRole == 2) { ?>
						<select class="card__select-role" data-id="<?php echo $row['id'] ?>">
							<?php if ($row['role'] == 0) { ?>
							<option value="0" selected>遭停權的使用者</option>
							<option value="1" >一般使用者</option>
							<option value="2" >管理員</option>
							<?php } else if ($row['role'] == 1) { ?>
							<option value="0" >遭停權的使用者</option>
							<option value="1" selected>一般使用者</option>
							<option value="2" >管理員</option>
							<?php } else if ($row['role'] == 2) { ?>
							<option value="0" >遭停權的使用者</option>
							<option value="1" >一般使用者</option>
							<option value="2" selected>管理員</option>
							<?php } ?>
						</select>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
		</section>

		<!-- 以下：實作分頁功能 -->
		<div class="board__hr"></div>
		<?php
    $stmt = $conn->prepare("SELECT COUNT(id) as count FROM `users`");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $total_page = (int) ceil($count / $items_per_page); // 回傳的是 float 所以要轉換型態
    ?>
		<div class="page-info">
			<span>總共有
				<?php echo $count; ?> 筆資料，頁數：
			</span>
			<span>
				<?php echo $page ?> / <?php echo $total_page ?>
			</span>
		</div>
		<div class="paginator">
			<?php if ($page !== 1) { ?>
			<a href="admin_panel.php?page=1">首頁</a>
			<a href="admin_panel.php?page=<?php echo $page - 1 ?> ">上一頁</a>
			<?php } ?>
			<?php if ($page !== $total_page) { ?>
			<a href="admin_panel.php?page=<?php echo $page + 1 ?> ">下一頁</a>
			<a href="admin_panel.php?page=<?php echo $total_page ?>">最後一頁</a>
			<?php } ?>
		</div>
	</main>
	<script>
		const selects = document.querySelectorAll(".card__select-role");

		for (const select of selects) {
			select.addEventListener('change', (e) => {
				const optionValue = select.value;
				const userID = select.dataset.id;
				// const userID = document.querySelector(`option:checked`).dataset.id;
				location.href = `handle_adjust_role.php?id=${userID}&role=${optionValue}`;
			});
		}

	</script>
</body>

</html>