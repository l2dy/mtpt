<?php
if (isset($_GET['target'])) {
	$target = $_GET['target'];
	header('Location:'.$target);
	exit();
	echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
	echo "<p>你将在 3 秒内被重定向至 <a href=\"$target\">$target</a> 。<br>如果浏览器没有反应，请手动点击上面的链接。</p>";
	echo "<p>You will be redirected to <a href=\"$target\">$target</a> in 3 seconds.<br>If your browser has no reaction, please click the link above manually.</p>";
}
