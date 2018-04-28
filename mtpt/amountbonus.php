<?php
header('Content-Type: text/html; charset=utf-8');
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
if (get_user_class() < UC_ADMINISTRATOR)
stderr("出错啦！", "没有权限进行本操作！");
function bark($msg) {
	stdhead();
	stdmsg("提示信息", $msg);
	stdfoot();
	exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (get_user_class() < 15)stderr("出错啦", "只有站长方有本权限");
	if ($_POST['doit'] == 'yes') {
		sql_query("UPDATE users SET seedbonus = seedbonus + 25.0 WHERE status='confirmed'");
		write_log("25.0 bonus point is sent to everyone. -" . $CURUSER["username"] ,'normal');
		stderr("成功！", "成功为每个人增加了25个麦粒");
		die;
	}
	
	if ($_POST['wage'] == 'yes') {
	$point=$_POST['amount'];
	$class=$_POST['class'];
	if(!is_numeric($point))stderr("别玩了！", "填写的麦粒不是正常的数值！");
	if($point<=0)stderr("别玩了！", "工资怎么会是负数呢？");
	$dt = sqlesc(date("Y-m-d H:i:s"));
	$reason_2=$_POST["reason_2"];
	if($class=="forummods")//论坛版主
	{
	$classname="论坛版主";
	$query1 = sql_query("SELECT userid FROM forummods GROUP by userid");
		while($dat=mysql_fetch_assoc($query1))
		{	sql_query("UPDATE users SET seedbonus = seedbonus + $point WHERE id=$dat[userid]");
			if(mysql_num_rows(sql_query("SELECT * FROM users WHERE id=$dat[userid]"))>0)sql_query("INSERT INTO messages (sender, receiver, added,  subject, msg) VALUES (0, $dat[userid], $dt, '福利来啦！', '管理组为所有"."$classname"."发放了$point"."个麦粒，请笑纳！原因是:{$reason_2}')") or sqlerr(__FILE__,__LINE__);
			writeBonusComment($dat['userid'],"麦粒增加 $point ，原因是:$reason_2") ;
		}
	}
	elseif($class=="picker")//美工
	{
	$classname="美工/技术组";
	$query3 = sql_query("SELECT * FROM users WHERE picker='yes'");
	while($dat=mysql_fetch_assoc($query3))
		{	sql_query("UPDATE users SET seedbonus = seedbonus + $point WHERE id=".$dat[id]);
			if(mysql_num_rows(sql_query("SELECT * FROM users WHERE id=".$dat[id]))>0)sql_query("INSERT INTO messages (sender, receiver, added,  subject, msg) VALUES (0, $dat[id], $dt, '福利来啦！', '管理组为所有"."$classname"."发放了$point"."个麦粒，请笑纳！原因是:{$reason_2}')") or sqlerr(__FILE__,__LINE__);
			writeBonusComment($dat['id'],"麦粒增加 $point ，原因是:$reason_2") ;}}
	elseif($class=="support")//客服
	{
	$classname="客服";
	$query3 = sql_query("SELECT * FROM users WHERE support='yes'");
		while($dat=mysql_fetch_assoc($query3))
		{	sql_query("UPDATE users SET seedbonus = seedbonus + $point WHERE id=".$dat[id]);
			if(mysql_num_rows(sql_query("SELECT * FROM users WHERE id=".$dat[id]))>0)sql_query("INSERT INTO messages (sender, receiver, added,  subject, msg) VALUES (0, $dat[id], $dt, '福利来啦！', '管理组为所有"."$classname"."发放了$point"."个麦粒，请笑纳！原因是:{$reason_2}')") or sqlerr(__FILE__,__LINE__);
			writeBonusComment($dat[id],"麦粒增加 $point ，原因是:{$reason_2}") ;}}
	elseif($class=="all")
{	$classname="用户";
	$query2 = sql_query("SELECT * FROM users");
		while($dat=mysql_fetch_assoc($query2))
		{	sql_query("UPDATE users SET seedbonus = seedbonus + $point WHERE id=".$dat[id]);
			if(mysql_num_rows(sql_query("SELECT * FROM users WHERE id=".$dat[id]))>0)sql_query("INSERT INTO messages (sender, receiver, added,  subject, msg) VALUES (0, $dat[id], $dt, '福利来啦！', '管理组为所有"."$classname"."发放了$point"."个麦粒，请笑纳！原因是:{$reason_2}')") or sqlerr(__FILE__,__LINE__);
			writeBonusComment($dat[id],"麦粒增加 $point ，原因是:{$reason_2}");}}
	
	else{
	$classname=($class==10?"贵宾":($class==11?"养老族":($class==12?"保种员":($class==13?"发布员":($class==14?"管理员":($class==15?"站长":"主管"))))));
		sql_query("UPDATE users SET seedbonus = seedbonus + $point WHERE class = $class");
		$query = sql_query("SELECT id FROM users WHERE class  = $class");

		while($dat=mysql_fetch_assoc($query))
		{	sql_query("INSERT INTO messages (sender, receiver, added,  subject, msg) VALUES (0, $dat[id], $dt, '福利来啦！', '管理组为所有"."$classname"."发放了$point"."个麦粒，请笑纳！原因是:{$reason_2}')") or sqlerr(__FILE__,__LINE__);
		writeBonusComment($dat[id],"麦粒增加 $point ，原因是:{$reason_2}");
		}
	}
//		write_log("25.0 bonus point is sent to everyone. -" . $CURUSER["username"] ,'normal');
		stderr("成功", "成功给所有$classname"."发放$point"."个麦粒$dat[id]");
		die;
	}

	if ($_POST["username"] == "" || $_POST["seedbonus"] == "")
	stderr("错误！", "丢失了重要数据");
	$reason_1=$_POST['reason_1'];
	$username=explode(",",$_POST["username"]);
	$count = count($username);
	for ($i = 0; $i < $count; $i++) {
		 $username[$i]=sqlesc($username[$i]);
	}
	//$username = sqlesc($_POST["username"]);
	$seedbonus = sqlesc($_POST["seedbonus"]);
/*	if ( $seedbonus == 0 )
	{
		stderr("错误！", "麦粒未变动");
		header("Location: " . get_protocol_prefix() . "$BASEURL/userdetails.php?id=".htmlspecialchars($arr[0]));
		die;
	} */
	for ($i = 0; $i < $count; $i++) {
		$arr= mysql_fetch_assoc(sql_query("SELECT id, bonuscomment FROM users WHERE username={$username[$i]}"));
		if (!$arr) {
				echo "<script type='text/javascript'> alert('用户名出错,请重新输入!');history.go(-1) </script>";die;
		}
		
	}	
	for ($i = 0; $i < $count; $i++) {
	$res = sql_query("SELECT id FROM users WHERE username={$username[$i]}");
	$arr = mysql_fetch_assoc($res);
	if (!$arr){
		stderr("Error", "Unable to update account.");
		header("Location: " . get_protocol_prefix() . "$BASEURL/userdetails.php?id=".htmlspecialchars($arr[0]));
		die;	
	}
	$oper = $CURUSER["username"];
		addBonus($arr[id], $seedbonus, "麦粒变动$seedbonus 个，原因是：".$reason_1."by---$CURUSER[username]");
		writeBonusComment($arr[id],"麦粒变动 ".$seedbonus."个 ，原因是:{$reason_1},操作人: $oper");
	$dt = date("Y-m-d H:i:s");
	//print_r( $arr );
	$bonuschangedmsg = "您的麦粒被改变了[color=red][b]".$_POST["seedbonus"]."[/b][/color]个，";
	// if ( $_POST["seedbonus"] > 0 )
		// $bonuschangedmsg = "您的麦粒被增加了[color=red][b]".abs($_POST["seedbonus"])."[/b][/color]个，";
	// elseif ( $_POST["$seedbonus"] < 0 )
		// $bonuschangedmsg = "您的麦粒被减少了[color=red][b]".abs($_POST["seedbonus"])."[/b][/color]个，";
	// else
		// $bonuschangedmsg = "麦粒好像没有变化，今天不会是4月1日吧？！";
	if($reason_1){
		$bonuschangedmsg .= chr(10).chr(10)."原因是：$reason_1" ;
	}
		$bonuschangedmsg .= chr(10).chr(10)."操作人：$oper";
	// print($bonuschangedmsg);
	sql_query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES ( 0, '{$arr[id]}','$dt', '麦粒变动', '".$bonuschangedmsg."')" ) or sqlerr(__FILE__,__LINE__);
	
	}
	echo "<script type='text/javascript'> alert('操作成功，点确定返回上个页面');history.go(-1) </script>";die();
}
stdhead("改变用户的麦粒");
?>
<h1>改变用户的麦粒</h1>
<?php
begin_main_frame("",false, 50);
begin_main_frame("调整某个特定用户的麦粒",false,50);
echo "<form method=\"post\" action=\"amountbonus.php\">";
print("<table width=100% border=1 cellspacing=0 cellpadding=4>\n");
?>
<tr><td class="rowhead" width="100px">用户名：</td><td class="rowfollow"><textarea type="text" name="username" rows="1" cols="20"></textarea>(多个用户用","隔开)</td></tr>
<tr><td class="rowhead">麦粒调整量：</td><td class="rowfollow"><input type="text" name="seedbonus" size="5"/></td></tr>
<tr><td class="rowhead">原因：</td><td class="rowfollow"><textarea rows="10" name="reason_1" cols="60"></textarea></td></tr>
<tr><td colspan="2" class="toolbox" align="center"><input type="submit" value="就这样！" class="btn"/></td></tr>
<?php end_table();?>
</form>
<?php end_main_frame();?>
<?php begin_main_frame("给每个人发放25个麦粒",false,50);?>
<form action="amountbonus.php" method="post">
<table width=100% border=1 cellspacing=0 cellpadding=5>
<tr><td class="rowfollow" width="100%">
你确定要给每个人都增加25个麦粒？<br /><br /></td></tr>
<tr><td class="toolbox" align="center"><input type = "hidden" name = "doit" value = "yes" />
<input type="submit" class="btn" value="确定" />
</td></tr>
<?php end_table();?>
</form>
<?php
//UC_STAFFLEADER
if (get_user_class() >= 14)
{
 
begin_main_frame("发工资",false,50);
echo "
<form action=\"amountbonus.php\" method=\"post\" >
<table width=100% border=1 cellspacing=0 cellpadding=5>
<tr><td class=\"rowfollow\" width=\"100%\">
给<select name=\"class\"><option value=\"all\">所有用户<option value=10>贵宾</option><option value=11>养老族</option><option value=12>保种员</option><option value=13 selected=\"selected\">发布员</option><option value=14>管理员</option><option value=15>站长</option><option value=16>主管</option><option value=\"forummods\">论坛版主</option><option value=\"picker\">美工/技术组</option><option value=\"support\">客服</option></select>等级用户增加<input name = \"amount\" value=\"5000\" size=\"8\"/>个麦粒
<br /></td><br /></tr><tr><td class=\"rowfollow\"><span style=\"font-weight:bolder;margin-right:10px;\">原因:</span><textarea rows=\"1\" name=\"reason_2\" cols=\"40\"></textarea></td></tr>
<tr><td class=\"toolbox\" align=\"center\"><input type = \"hidden\" name = \"wage\" value = \"yes\" />
<input type=\"submit\" class=\"btn\" value=\"确定\" />
</td></tr>";
end_table();
echo "</form>";
}
?>


<?php
end_main_frame();
end_main_frame();
stdfoot();
