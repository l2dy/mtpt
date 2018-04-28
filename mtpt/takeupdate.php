<?php
require_once("include/bittorrent.php"); 
function bark($msg) { 
 stdhead(); 
   stdmsg("Failed", $msg); 
 stdfoot(); 
 exit; 
} 
dbconn(); 
loggedinorreturn(); 
if (get_user_class() < $staffmem_class)
       permissiondenied();
if ($_POST['setdealt']){
$res = sql_query ("SELECT * FROM reports WHERE dealtwith=0 AND id IN (" . implode(", ", $_POST[delreport]) . ")");
$num = $_POST['bonus'];
$num2 = $_POST['bonus2'];
while ($arr = mysql_fetch_assoc($res))
{
	if ($CURUSER['id'] == $arr['reported']||$CURUSER['id'] == $arr['addedby']) break;
	sql_query ("UPDATE reports SET dealtwith=1, dealtby = $CURUSER[id] WHERE id = $arr[id]") or sqlerr();
	if ($num == 0) break;
	addBonus($arr['addedby'], $num);sendMessage(0, $arr['addedby'], "因举报而被改变麦粒$num 个", "您在 $arr[added] 举报 $arr[type] $arr[reportid],原因是$arr[reason] \n 管理员 审核之后决定对您的麦粒做出 $num 的变动，请继续积极合理举报不良现象，维护麦田良好氛围");
	if ($num2 == 0) break;
	addBonus($arr['reported'], $num2);sendMessage(0, $arr['reported'], "因被举报而被改变麦粒$num2 个", "您的 $arr[type] $arr[reportid] 被用户举报 ,原因是$arr[reason] \n 管理员 审核之后决定对您的麦粒做出 $num2 的变动，请积极合理举报不良现象，维护麦田良好氛围" );
}
	$Cache->delete_value('staff_new_report_count');
}
elseif ($_POST['delete']){
$res = sql_query ("SELECT * FROM reports WHERE id IN (" . implode(", ", $_POST[delreport]) . ")");
while ($arr = mysql_fetch_assoc($res)){
	if ($CURUSER['id'] == $arr['reported']||$CURUSER['id'] == $arr['addedby']) break;
	sql_query ("DELETE from reports WHERE id = $arr[id]") or sqlerr();
	}
	$Cache->delete_value('staff_new_report_count');
	$Cache->delete_value('staff_report_count');
} 

header("Refresh: 0; url=reports.php"); 
