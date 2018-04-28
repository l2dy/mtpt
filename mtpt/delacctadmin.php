<?php
require "include/bittorrent.php";
dbconn();
if (get_user_class() < UC_ADMINISTRATOR)
stderr("Error", "Permission denied.");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
$userid = trim($_POST["userid"]);

if (!$userid)
  stderr("Error", "Please fill out the form correctly.");

$res = sql_query("SELECT * FROM users WHERE id=" . sqlesc($userid)) or sqlerr();
if (mysql_num_rows($res) != 1)
  stderr("Error", "Bad user id or password. Please verify that all entered information is correct.");
$arr = mysql_fetch_assoc($res);

$id = $arr['id'];
$name = $arr['username'];
$curenabled = $arr["enabled"];
$enabled = $_POST["enabled"];
$opreason = $_POST["opreason"];
$modcomment = $arr['modcomment'];
if ($opreason == null){
stderr("error","原因为空，不能继续执行！原因将写到封禁日志，请认真填写，只要不是空值就能通过，看着写吧");
die;}

if ($_POST['delenable'] == 'yes')
{
$res = sql_query("DELETE FROM users WHERE id=$id") or sqlerr();
record_op_log($CURUSER['id'],$id,htmlspecialchars($name),'del',$opreason);
if (mysql_affected_rows() != 1)
  stderr("Error", "Unable to delete the account.");
stderr("Success", "用户：".htmlspecialchars($name)." 删除成功，原因是".$opreason,false);
}

	if ($_POST['changedisable'] == 'yes')
	{
		if ($enabled != $curenabled)
		{
			if ($enabled == 'yes') {
		//sql_query("UPDATE users SET  enable='yes' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
			
			if (get_single_value("users","class","WHERE id = ".sqlesc($userid)) == UC_PEASANT){
				$length = 30*86400; // warn users until 30 days
				$until = sqlesc(date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) + $length)));
				sql_query("UPDATE users SET enabled='yes', leechwarn='yes', leechwarnuntil=$until WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
				$modcomment = date("Y-m-d") . " - Enabled by " . $CURUSER['username']." - Reason For $opreason";
				writeModComment($userid, $modcomment);
			}
			else{
				sql_query("UPDATE users SET enabled='yes', leechwarn='no' WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			}
			$modcomment = date("Y-m-d") . " - Enabled by " . $CURUSER['username']." - Reason For $opreason";
			writeModComment($userid, $modcomment);
			record_op_log($CURUSER['id'],$id,htmlspecialchars($name),'unban',$opreason);
			stderr("Success", "用户：".htmlspecialchars($name)."</b> 复活账号成功，原因是".$opreason,false);
		} else {
		sql_query("UPDATE users SET  enabled='no' WHERE id=$userid") or sqlerr(__FILE__, __LINE__);
			$modcomment = date("Y-m-d") . " - Disabled by " . $CURUSER['username']." - Reason For $opreason";	
			writeModComment($userid, $modcomment);
			record_op_log($CURUSER['id'],$id,htmlspecialchars($name),'ban',$opreason);
			stderr("Success", "用户：".htmlspecialchars($name)." 封禁成功，原因是".$opreason,true);
				}
		}
		else stderr("操作失败", "当前用户状态与要操作的状态相同。",false);
	}
}
stdhead("Delete account");
?>
<h1>Delete account</h1>
<table border=1 cellspacing=0 cellpadding=5>
<form method=post action=delacctadmin.php>
<tr><td class=rowhead>User id</td><td><input size=40 name=userid></td></tr>

<tr><td colspan=2><input type=submit class=btn value='Delete'></td></tr>
</form>
</table>
<?php
stdfoot();
