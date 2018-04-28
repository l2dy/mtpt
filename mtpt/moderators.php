<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();

if (get_user_class() < UC_MODERATOR)
    permissiondenied();

		$upnum = 15;//上传种子数
		$other = 30;//其他操作总数
		$other2 = 60;
		$salary = 20000;//工资
	
	function searchlog($text)
	{	
		global $year;global $month;
		$timestart=strtotime($year."-".$month."-01 00:00:00");
		$sqlstarttime=date("Y-m-d H:i:s", $timestart);
		$timeend=strtotime("+1 month", $timestart);
		$sqlendtime=date("Y-m-d H:i:s", $timeend);
		$res = sql_query("SELECT COUNT(*) FROM sitelog WHERE txt LIKE '%$text%'  AND sitelog.added >= ".sqlesc($sqlstarttime)." AND sitelog.added <= ".sqlesc($sqlendtime)."") or sqlerr(__FILE__,__LINE__);
		$href = "log.php?query=".rawurlencode($text)."&search=all&action=dailylog";
		$row = mysql_fetch_array($res);
		$logcount = $row[0];
		$result = array("<a href=\"$href\" >$logcount </a>","$logcount");
			return $result;

	}
	function check($uplog,$all)
	{	
		global $upnum;global $other;global $other2;
		if ($uplog >= $upnum && $all>$other) return true ;
		elseif ($all > $other2) return true;
		else return false;
		
	}
	$unpassname = "";$passname="";
$year=0+$_GET['year'];
if (!$year || $year < 2000)
$year=date('Y');
$month=0+$_GET['month'];
if (!$month || $month<=0 || $month>12)
$month=date('m');

	
$order=$_GET['order'];
if (!in_array($order, array('username', 'torrent_size', 'torrent_count')))
	$order='username';
if ($order=='username')
	$order .=' ASC';
else $order .= ' DESC';
stdhead($lang_uploaders['head_uploaders']);
begin_main_frame();
?>
<div style="width: 940px">
<?php
$year2 = substr($datefounded, 0, 4);
$yearfounded = ($year2 ? $year2 : 2007);
$yearnow=date("Y");

$timestart=strtotime($year."-".$month."-01 00:00:00");
$sqlstarttime=date("Y-m-d H:i:s", $timestart);
$timeend=strtotime("+1 month", $timestart);
$sqlendtime=date("Y-m-d H:i:s", $timeend);

print("<h1 align=\"center\">".$lang_uploaders['text_uploaders']." - ".date("Y-m",$timestart)."月 - 考核情况</h1>");
$date = date("Y-m",$timestart);

$yearselection="<select name=\"year\">";
for($i=$yearfounded; $i<=$yearnow; $i++)
	$yearselection .= "<option value=\"".$i."\"".($i==$year ? " selected=\"selected\"" : "").">".$i."</option>";
$yearselection.="</select>";

$monthselection="<select name=\"month\">";
for($i=1; $i<=12; $i++)
	$monthselection .= "<option value=\"".$i."\"".($i==$month ? " selected=\"selected\"" : "").">".$i."</option>";
$monthselection.="</select>";
echo "如果发布种子数目大于$upnum 个，需要其他各项操作数目之和大于$other 。<br/>如果发布种子数目小于$upnum 个，需要其他各项操作数目之和大于$other2 。<br/>工资为$salary 。<br/>管理员同时参加发布员考核，考核合格的奖励与发布员相同，不合格没有关系。<a href=\"uploaders.php?year=$year&month=$month&moderator=1\" class='faqlink' target=\"blank\">uploaders.php?year=$year&month=$month&moderator=1</a>";
?>
<div>
<form method="get" action="?">
<span>
<?php echo $lang_uploaders['text_select_month']?><?php echo $yearselection?>&nbsp;&nbsp;<?php echo $monthselection?>&nbsp;&nbsp;<input type="submit" value="<?php echo $lang_uploaders['submit_go']?>" />
</span>
</form>
</div>

<?php
$numres = sql_query("SELECT COUNT(users.id) FROM users WHERE class = ".UC_MODERATOR) or sqlerr(__FILE__, __LINE__);
$numrow = mysql_fetch_array($numres);
$num=$numrow[0];
if (!$num)
	print("<p align=\"center\">".$lang_uploaders['text_no_uploaders_yet']."</p>");
else{
?>

<div style="margin-top: 8px">
<?php
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"940\"><tr>");
	print("<td class=\"colhead\">用户名</td>");
	print("<td class=\"colhead\">上传种子</td>");
	print("<td class=\"colhead\">编辑种子</td>");
	print("<td class=\"colhead\">删除种子</td>");
	print("<td class=\"colhead\">操作候选</td>");
	print("<td class=\"colhead\">操作求种</td>");
	print("<td class=\"colhead\">操作回收站</td>");
	print("<td class=\"colhead\">删除字幕</td>");
	print("<td class=\"colhead\">是 否 合 格</td>");
	if (get_user_class() >= UC_SYSOP)
	print("<td class=\"colhead\">发工资</td>");
	print("</tr>");
	$res = sql_query("SELECT id,username,class FROM users WHERE class >= ".UC_MODERATOR) or sqlerr(__LINE__,__FILE__);
	$hasupuserid=array();
	$usernameall = null;
	$unpasseduser = null;
	while($row = mysql_fetch_array($res))
	{
		$uplog = searchlog("用户 $row[username] 上传");
		$delself = searchlog("发布者 $row[username] 删除");
		$editlog = searchlog("管理员 $row[username] 编辑");
		$dellog = searchlog("管理员 $row[username] 删除");
		$offerlog = searchlog("管理员 $row[username] 发布了 候选");
		$reqlog = searchlog("求种：管理员 $row[username]");
		$uptitlelog = searchlog("管理员 $row[username] 移入回收站");
		$deltitlelog = searchlog("字幕：管理员 $row[username] 删除了字幕");
		
		$all = $editlog[1]+$dellog[1]+$offerlog[1]+$reqlog[1]+$uptitlelog[1]+$deltitlelog[1];
		print("<tr>");
		print("<td class=\"colfollow\">".get_username($row['id'], false, true, true, false, false, true)."</td>");
		print("<td class=\"colfollow\">".($uplog[0]."-".$delself[0]."=".($uplog[1]-$delself[1]))."</td>");
		print("<td class=\"colfollow\">".$editlog[0]."</td>");
		print("<td class=\"colfollow\">".($dellog[0])."</td>");
		print("<td class=\"colfollow\">".($offerlog[0])."</td>");
		print("<td class=\"colfollow\">".($reqlog[0])."</td>");
		echo ("<td class=\"colfollow\">$uptitlelog[0]</td>");
		echo ("<td class=\"colfollow\">$deltitlelog[0]</td>");
		echo ("<td class=\"colfollow\">".(check($uplog[1]-$delself[1],$all)?"<b style=\"color:green\">合格</b>":"<b style=\"color:red\">$all 不合格</b>")."</td>");
		if (get_user_class() >= UC_SYSOP)
		if (check($uplog[1],$all))
		{
		print("<td class=\"colfollow\">最后统一发工资$delself[0]</td>");
		if ($row['class'] < UC_SYSOP) $passname .= $row['username'].",";
		}
		else
		{if ($row['class'] < UC_SYSOP){
		print("<td class=\"colfollow\">最后统一提醒$delself[0]</td>");
		 $unpassname .= $row['username'].",";
		 }
		 else print("<td class=\"colfollow\">你管不着人家</td>");
		}
		print("</tr>");
	}	
	print("</table>");
	if (get_user_class() >= UC_SYSOP){
		$passname = rtrim($passname,",");
		$unpassname = rtrim($unpassname,",");
		print("<table><tr><td class='colhead'>给管理员发警告</td><td class='colhead'>给管理员发工资</td></tr><tr><td class=\"colfollow\"><form method=\"post\" action=\"amountbonus.php\" ><input type='text' value=5000 name='seedbonus'/><br/><input type='text' value='$unpassname' name='username'/><br/><input type='text' value='很遗憾$date 月管理员考核没有通过，如果在岗的话请对自己的版块负责。特此警告' name='reason_1'/><br/><input type=\"submit\" value=\"给他们警告\" class=\"btn\"/></form></td><td class=\"colfollow\"><form method=\"post\" action=\"amountbonus.php\" ><input type='text' value=$salary name='seedbonus'/><br/><input type='text' value='$passname' name='username'/><br/><input type='text' value='恭喜$date 月管理员考核通过，工资奉上' name='reason_1'/><br/><input type=\"submit\" value=\"给他们发工资\" class=\"btn\"/></form></td></tr></table>");
		}
	
	
?>
</div>

<?php
}
?>
</div>
<?php
end_main_frame();
stdfoot();
?>
