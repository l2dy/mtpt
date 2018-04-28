<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
parked();
function bark($title,$msg)
{
	global $lang_userdetails;
	stdhead();
	stdmsg($title, $msg);
	stdfoot();
	exit;
}

if ($CURUSER['cardnum'] != null) bark('错误','你已经登记过了学号，如果需要修改请联系管理组');

if ($_POST['stuid']){
$stuid = mysql_real_escape_string($_POST['stuid']);       //在此加入一卡通验证代码
$cardpass = $_POST['cardpass'];
$cardinfo = getOneCard($stuid,$cardpass);
if(!$cardinfo)
    bark('错误','锐捷账号验证失败,请确认密码正确');
else{
	$a = (@mysql_fetch_row(@sql_query("select count(*) from users where cardnum='".$stuid."'"))) or sqlerr(__FILE__, __LINE__);
	if ($a[0] != 0)
 		bark('错误',$stuid.'已经注册');
}
sql_query("UPDATE users SET cardnum = $stuid WHERE id = $CURUSER[id]") or sqlerr();
addBonus($CURUSER['id'], 1000);
writeBonusComment($CURUSER['id'],"成功绑定锐捷账号，系统增加1000麦粒");
bark('成功',"你成功将一卡通$stuid 绑定至 $CURUSER[username] ,系统为你增加了1000麦粒");
}
stdhead();
begin_main_frame();
echo "<form method=\"post\" action=\"getcardnum.php\">
<table border=\"1\" cellspacing=\"0\" cellpadding=\"10\">
<tr><td class=rowhead>学号、工号</td><td class=rowfollow align=left><input type=\"text\" style=\"width: 200px\" name=\"stuid\" id=\"stuid\"/><span id=\"stuidspan\" style=\"color:red\">&nbsp;</span></td></tr>
<tr><td class=rowhead>锐捷平台密码</td><td class=rowfollow align=left><input name=\"cardpass\" type=\"password\" style=\"width: 200px\" id=\"cardpass\" /><span id=\"cardpassspan\" style=\"color:red\">&nbsp;</span></td></tr>

</table><input type='submit' id='cardsubmit' value='点击验证锐捷密码' style='height: 25px'>
学工号是登陆锐捷时候输入的账号，密码是登陆锐捷的密码。非西农用户请忽略此功能。
</form>";

end_main_frame();
stdfoot();
?>