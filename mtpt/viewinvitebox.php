<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path("takeinvite.php","",""));
loggedinorreturn();
parked();
function bark($msg) {
	stdmsg('失败！', $msg);
  stdfoot();
  exit;
}
function invite($email)
{
	global $CURUSER;
	global $SITENAME;
	global $BASEURL;
	global $SITEEMAIL;
	global $lang_takeinvite;
$id = $CURUSER[id];
$email = unesc(htmlspecialchars(trim($email)));
$email = safe_email($email);
if (!$email)
    bark($lang_takeinvite['std_must_enter_email']);
if (!check_email($email))
	bark($lang_takeinvite['std_invalid_email_address']);
if(EmailBanned($email))
    bark($lang_takeinvite['std_email_address_banned']);

if(!EmailAllowed($email))
    bark($lang_takeinvite['std_wrong_email_address_domains'].allowedemails());
$body = "
你好,

我邀请你加入 $SITENAME, 这是一个拥有丰富资源的非开放社区.
如果你有兴趣加入我们请阅读规则并确认邀请.最后,确保维持一个良好的分享率
分享允许的资源.

欢迎到来! :)
";
$body = str_replace("<br />", "<br />", nl2br(trim(strip_tags($body))));
if(!$body)
	bark($lang_takeinvite['std_must_enter_personal_message']);


// check if email addy is already in use
$a = (@mysql_fetch_row(@sql_query("select count(*) from users where email=".sqlesc($email)))) or die(mysql_error());
if ($a[0] != 0)
  bark($lang_takeinvite['std_email_address'].htmlspecialchars($email).$lang_takeinvite['std_is_in_use']);
$b = (@mysql_fetch_row(@sql_query("select count(*) from invites where invitee=".sqlesc($email)))) or die(mysql_error());
if ($b[0] != 0)
  bark($lang_takeinvite['std_invitation_already_sent_to'].htmlspecialchars($email).$lang_takeinvite['std_await_user_registeration']);

$ret = sql_query("SELECT username FROM users WHERE id = ".sqlesc($id)) or sqlerr();
$arr = mysql_fetch_assoc($ret);

$hash  = md5(mt_rand(1,10000).$CURUSER['username'].TIMENOW.$CURUSER['passhash']);

$title = $SITENAME.$lang_takeinvite['mail_tilte'];

$message = <<<EOD
{$lang_takeinvite['mail_one']}{$arr[username]}{$lang_takeinvite['mail_two']}
<b><a href="http://$BASEURL/signup.php?type=invite&invitenumber=$hash" target="_blank">{$lang_takeinvite['mail_here']}</a></b><br />
http://$BASEURL/signup.php?type=invite&invitenumber=$hash
<br />{$lang_takeinvite['mail_three']}$invite_timeout{$lang_takeinvite['mail_four']}{$arr[username]}{$lang_takeinvite['mail_five']}<br />
$body
<br /><br />{$lang_takeinvite['mail_six']}
EOD;

sent_mail($email,$SITENAME,$SITEEMAIL,change_email_encode(get_langfolder_cookie(), $title),change_email_encode(get_langfolder_cookie(),$message),"invitesignup",false,false,'',get_email_encode(get_langfolder_cookie()));
//this email is sent only when someone give out an invitation

sql_query("INSERT INTO invites (inviter, invitee, hash, time_invited) VALUES ('".mysql_real_escape_string($id)."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($hash)."', " . sqlesc(date("Y-m-d H:i:s")) . ")");
}
if ($CURUSER ['class'] < 12)
stderr("错误","您没有权限");
stdhead();
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if ($_POST['setdealt']){
$res = sql_query ("SELECT Id FROM invitebox WHERE dealt_by='no' AND Id IN (" . implode(", ", $_POST[invitebox]) . ")");
while ($arr = mysql_fetch_assoc($res))
	sql_query ("UPDATE invitebox SET dealt_by = '忽略-".$CURUSER[username]."' WHERE Id = $arr[Id]") or sqlerr();
}
elseif ($_POST['delete']){
$res = sql_query ("SELECT * FROM invitebox WHERE Id IN (" . implode(", ", $_POST[invitebox]) . ")");
while ($arr = mysql_fetch_assoc($res)){
	$file=str_replace("%20", " ","$arr[pic]");
	unlink("$file");
sql_query ("DELETE from invitebox WHERE Id = $arr[Id]") or sqlerr();}}
elseif ($_POST['invite']){
	$res = sql_query ("SELECT * FROM invitebox WHERE dealt_by='no' AND Id IN (" . implode(", ", $_POST[invitebox]) . ")");
	while ($arr = mysql_fetch_assoc($res)){
	if ($arr ['dealt_by']=='no'){
	sql_query ("UPDATE invitebox SET dealt_by = '邀请-".$CURUSER[username]."' WHERE Id = $arr[Id]") or sqlerr();
	$email=$arr['email'];
	$email0 = unesc(htmlspecialchars(trim($email)));
	invite($email0);
	print "邀请'".$email0."'成功";}
	else print "邀请'".$email0."'失败，该申请已处理";
	}
}
}

?>
<h1>邀请申请区</h1>
<h1><a href=viewinvitebox.php>查看未处理</a>++++++++<a href=viewinvitebox.php?view=all>查看全部</a></h1>
<br/><h2>说明：</h2><table width="100%"><tbody><tr><td class="text" valign="top"><div style="margin-left: 16pt;">1.点击右面的复选框，勾选要处理的申请；<br/>2.“设为已处理”将忽略此申请；“邀请”将向该申请邮箱发送邀请码（不会占用你的邀请名额）；尽量不要一次勾选多个申请同时邀请，以免其中一个邮箱有问题而影响其他邮箱。<br/>3.请认真审核，仔细处理。优先考虑网络、硬盘条件较好以及经验丰富的用户加入。<br/></div></td></tr></tbody></table>
<table border="1" cellspacing="0" cellpadding="5" align="center" width="1100"><tbody><tr>
<form method=post action=viewinvitebox.php>
<td class="colhead">欲申请用户名</td>
<td class="colhead">IP地址</td>
<td class="colhead" align="center"> 邮箱 </td>
<td class="colhead"> 所在学校 </td>
<td class="colhead" align="center"> 年级 </td>
<td class="colhead"> 网络情况</td>
<td class="colhead" align="center"> 硬盘情况 </td>
<td class="colhead" align="center"> 补充说明 </td>
<td class="colhead" align="center"> 时间 </td>
<td class="colhead" align="center"> 其他站点截图 </td>
<td class="colhead" align="center"> 文件名 </td>
<td class="colhead" align="center"> 操作者 </td>
<td class="colhead" align="center"> 行为 </td>
</tr>
<?php
$url = "viewinvitebox.php?";
	$count = get_row_count("invitebox");
	$perpage = 10;



if ($_GET['view'] == 'all')
	{
	$where = "";
	$url = "viewinvitebox.php?view=all&";
	$count = get_row_count("invitebox");
	$perpage = 10;}
else {
	$where = " where dealt_by='no' ";
	$perpage = 1;
	$count = get_row_count("invitebox","where dealt_by='no'");
	}
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, $url);
$res = sql_query("SELECT * FROM invitebox $where ORDER BY id desc $limit");
while($row=mysql_fetch_assoc($res))
{
	$Id=$row[Id];
	$ip="<a href='ipsearch.php?ip=".$row['ip']."' target='_blank' class='faqlink'>$row[ip]</a><br>".school_ip_location2($row['ip']);
	$username=$row['username'];
	$email=$row['email'];
	$school=$row['school'];
	$grade=$row['grade'];
	$web=$row['web'];
	$disk=$row['disk'];
	$self_introduction=$row['self_introduction'];
	$added=$row['added'];
	$pic=$row['pic'];
	$ori_name=$row['ori_name'];
	$dealt_by=$row['dealt_by'];
print("<tr>
	<td class=\"rowfollow\" align=\"center\">$username</td>
	<td class=\"rowfollow\">$ip</td>
	<td class=\"rowfollow\">$email</td>
	<td class=\"rowfollow\">$school</td>
	<td class=\"rowfollow\">$grade</td>
	<td class=\"rowfollow\">$web</td>
	<td class=\"rowfollow\">$disk</td>
	<td class=\"rowfollow\">$self_introduction</td>
	<td class=\"rowfollow\">$added</td>");
	if($pic){
		print "<td class=\"rowfollow\"><a class=faqlink href=$pic target=_blank>点此查看</a></td>";
		print "<td class=\"rowfollow\">$ori_name</td>";
	}
	else{
		print "<td class=\"rowfollow\"></td>";
		print "<td class=\"rowfollow\"></td>";
	}
	print "<td class=\"rowfollow\">$dealt_by</td>
	<td class=\"rowfollow\"><input type=\"checkbox\" name=\"invitebox[]\" checked=\"checked\" value=\"$Id\"></td>
	</tr>";
	if ($_GET['view'] == 'all') continue;
	print("<tr>
	<td class=\"rowfollow\" align=\"center\"></td>
	<td class=\"rowfollow\">");
	$suggestlvl = 0;

	if (school_ip_location($row['ip'],0) == "西北农林科技大学")
		$suggestlvl -=5;
	$resip = sql_query("select count(*) from iplog where ip=".sqlesc($row['ip'])) or sqlerr(__LINE__,__FILE__);
	$rowip = mysql_fetch_array($resip);
	$countip=$rowip[0];
	$resemail = sql_query("select count(*) from users where email=".sqlesc($email)) or sqlerr(__LINE__,__FILE__);
	$rowemail = mysql_fetch_array($resemail);
	$countemail=$rowemail[0];
	if ($countip >=3){
		echo "<font color='red'><b>重复ip，请点ip进行查询</b></font>";
		$suggestlvl -= $countip;}
	elseif ($countip >=1){
		echo "<font color='red'><b>疑似重复ip，请点ip进行查询</b></font>";
		$suggestlvl -= $countip;}
	else
		echo "<font color='green'><b>没有检测到重复ip</b></font>";
	echo "</td><td class=\"rowfollow\">";

	if ($countemail>=1){
		echo "<font color='red'><b>此用户已注册</b></font>";
		$suggestlvl -= 5;}
	else echo "<font color='green'><b>邮箱应该可以注册</b></font>";
	echo "</td><td class=\"rowfollow\">";
	$school_name_by_ip = sqlesc(school_ip_location2($row['ip']), 0);
	if (strpos($school_name_by_ip, sqlesc($school)) !== false)
		echo "<font color='green'><b>检查通过</b></font>";
	else {
		echo "<font color='red'><b>请手动查询IP地址</b></font>";
		$suggestlvl -=1;}
	if ($suggestlvl>=0)
	$suggest = "<font color='green'><b>建议发送邀请码，等级$suggestlvl</b></font>";
	elseif($suggestlvl< (-3))
	$suggest = "<font color='red'><b>请勿发送邀请码，等级$suggestlvl</b></font>";
	else
	$suggest = "<font color='orange'><b>不建议发送邀请码，等级$suggestlvl</b></font>";
	echo "</td><td class=\"rowfollow\" align=\"right\"></td><td class=\"rowfollow\" align=\"right\"></td></tr>";

}
?>
<tr><td class="colhead"><input class="btn" type="button" value="全选" onclick="this.value=check(form,'全选','全不选')"></td><td class="colhead" colspan="12" align="right"><? echo $suggest;?><input type="submit" name="setdealt" value="忽略此申请" /><input type="submit" name="invite" value="邀请" /><input type="submit" name="delete" value="删除" /></td></tr>
</form>
<?
print "</table>";
echo $pagerbottom;

stdfoot();
?>
