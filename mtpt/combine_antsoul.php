<?php
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();

function randomstring($length, $buffer = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'){
	$str = '';
	for ($i = 0; $i < $length; $i++)
		$str .= $buffer[mt_rand(0, strlen($buffer) - 1)];
	return $str;
}

if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] != 'on') {
	header('Location: https://pt.nwsuaf6.edu.cn/combine_antsoul.php');
	die('We don\'t accept unsecure connection.');
}

$msgs = array();

if (!empty($_POST)) {
	// Check CSRF-Token
	if ($_POST['_csrftoken'] == $_COOKIE['MTPT_COMBANTSOUL_CSRFTOKEN']) {
		// CSRF-Token Valid
		$res = sql_query("SELECT * FROM users WHERE email = " . sqlesc($_POST['antemail']));
		$row = mysql_fetch_array($res);
		if ($row) {
			if ($row['passhash'] == md5($row['secret'] . $_POST['antpwd'] . $row['secret'])) {
				// Passed password check
				// Check if this user is migrated from Antsoul by modcomment
				$modcomment = str_replace("\r", "", $row['modcomment']);
				$modcomment = explode("\n", $modcomment);
				$mark = '2017-11-19 - 蚂蚁PT (Antsoul) 用户迁移至本站';
				$mark1 = 'Antsoul数据合并至此账号';
				if (in_array($mark, $modcomment)){
					if (!in_array($mark1, $modcomment)){
						if($row['id'] == $CURUSER['id']){
							$msgs[] = '请使用另一个账号合并该账号';
						}else{
							// This user is migrated from Antsoul
							$modcomment = implode('\n', $modcomment);
							$modcomment = date('Y-m-d') . " - 蚂蚁 PT 用户 ".$row['username'].":{$row['email']} Antsoul数据合并至此账号\n" . $modcomment . "\n";
							$modcomment = sqlesc($modcomment);
							$res1 = sql_query("UPDATE users SET `uploaded` = greatest(`uploaded`, {$row['uploaded']}), `seedtime` = `seedtime` + {$row['seedtime']}, `leechtime` = `leechtime` + {$row['leechtime']}, `seedbonus` = `seedbonus` + 30000, `modcomment` = concat({$modcomment}, `modcomment`) where `id` = {$CURUSER['id']};");
							record_op_log(0, $row['id'], htmlspecialchars($row['username']), "del", "{$CURUSER['username']} ({$CURUSER['id']}) 自助合并蚂蚁PT账号，删除用户 {$row['username']} ({$row['id']})");
							$res2 = sql_query("DELETE FROM users WHERE `email` = '{$row['email']}';");
							if ($res1 === true && $res2 === true){
								// Combination succeeded
								header("Location: https://pt.nwsuaf6.edu.cn/");
								die('账户已合并。');
							} else {
								$msgs[] = '账户合并过程中发生异常错误，请和管理员联系。';
							}
						}
					} else{
						$msgs[] = '你已经合并过了！！！不要合并其他人的账号！';
					}
				} else {
					$msgs[] = '该用户不是从蚂蚁 PT 迁移而来，不能被合并。';
				}
			} else {
				$msgs[] = '密码错误';
			}
		} else {
			$msgs[] = '用户不存在';
		}
	} else {
		$msgs[] = '检测到潜在的攻击';
	}
}

// Generate new CSRF-Token
$csrftoken = randomstring(32);
setcookie('MTPT_COMBANTSOUL_CSRFTOKEN', $csrftoken, time() + 300, '/', '', true, true);

//添加判断，防止原蚂蚁PT用户利用合并系统自杀
	header('Content-Type: text/html; charset=utf-8');
	$modcomment_ = str_replace("\r", "", $CURUSER['modcomment']);
	$modcomment_ = explode("\n", $modcomment_);
	$mark_ = '2017-11-19 - 蚂蚁PT (Antsoul) 用户迁移至本站';
	if (in_array($mark_, $modcomment_)){
		die("此账户是原蚂蚁PT账户，请使用原麦田账户进行合并!");
	}

?><html>
	<head>
		<title>将蚂蚁 PT 账号数据合并至此账号 - 麦田 PT</title>
	</head>
	<body>
		<p>此页面仅用于在麦田 PT 和蚂蚁 PT 均有账号且未用同一邮箱注册，导致站点导入蚂蚁 PT 数据后拥有两个账号的用户使用。</p>
		<p>请使用您自行注册的麦田 PT 账号（而非从蚂蚁 PT 导入而来的账号）操作。</p>
		<p>合并账户时，账户数据将按照在两站均有账号且用同一邮箱注册方法处理。合并后，从蚂蚁 PT 导入而来的账号将被删除。合并操作将被写入日志。</p>
		<form method="post">
			<p>请填写以下信息：</p>
			<input type="hidden" name="_csrftoken" value="<?=$csrftoken?>">
			<p>
				<label for="antemail">蚂蚁 PT 账号邮箱：</label>
				<input id="antemail" type="email" name="antemail" required >
			</p>
			<p>
				<label for="antpwd">蚂蚁 PT 账号密码：</label>
				<input id="antpwd" type="password" name="antpwd" required>
			</p>
			<input type="submit" value="提交">
		</form>
<?php
if (!empty($msgs)) { ?>
<p>啊哦，出现了一些问题：</p>
<ol><?php foreach ($msgs as $msg) echo '<li>'.htmlspecialchars($msg).'</li>'; ?></ol>
<?php } ?>
	</body>
</html>
