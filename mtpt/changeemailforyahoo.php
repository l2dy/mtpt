<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path("takeinvite.php","",""));
loggedinorreturn();
parked();
stdhead("修改邮箱");
function bark($msg) {
	stdmsg('失败！', $msg);
  stdfoot();
  exit;
}
if (!(strstr($CURUSER['email'],'@yahoo.cn')||strstr($CURUSER['email'],'@yahoo.com.cn')))
		bark('您的邮箱不是中国雅虎邮箱，暂时不支持修改邮箱服务.如果有特殊情况需要修改邮箱请联系管理组，并且详细说明原因。');
else{
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$email=$_POST['email'];
	//$email=safe_email($email);
	$password=$_POST['password'];
	if (!$email)
    bark($lang_takeinvite['std_must_enter_email']);
	if (!check_email($email))
	bark($lang_takeinvite['std_invalid_email_address']);
	if(EmailBanned($email))
    bark($lang_takeinvite['std_email_address_banned']);

	if(!EmailAllowed($email))
    bark($lang_takeinvite['std_wrong_email_address_domains'].allowedemails());
  if ($CURUSER["passhash"] != md5($CURUSER["secret"] . $password . $CURUSER["secret"]))
		bark('密码错误！');
	
	sql_query("UPDATE users SET email=".sqlesc($email)." WHERE id=$CURUSER[id]")or sqlerr(__FILE__, __LINE__);
	stdmsg('邮箱修改成功！', '请到<a class=faqlink href=usercp.php>个人页面</a>查看。');
  stdfoot();
  exit;
}
?>
<h1>修改邮箱</h1>
<form method=post action=changeemailforyahoo.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead>请输入新邮箱</td><td><input type=text name=email size=40>注意：修改后没有验证环节，因此请谨慎修改，避免填错。</td></tr>
<tr><td class=rowhead>请输入你的密码</td><td><input type=password name=password size=40>如果在此页面发现任何bug请反馈至管理组，谢谢</td></tr>
<tr><td colspan=2 align=center><input type=submit value="确定" class=btn></td></tr>
</table>
<?php
}
  stdfoot();
?>