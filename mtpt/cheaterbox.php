<?php
require "include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

if (get_user_class() < $staffmem_class)
	permissiondenied();


if ($_POST['setdealt']){
	$res = sql_query ("SELECT id FROM cheaters WHERE dealtwith=0 AND id IN (" . implode(", ", $_POST[delcheater]) . ")");
	while ($arr = mysql_fetch_assoc($res))
		sql_query ("UPDATE cheaters SET dealtwith=1, dealtby = $CURUSER[id] WHERE id = $arr[id]") or sqlerr();
	$Cache->delete_value('staff_new_cheater_count');
}
elseif ($_POST['delete']){
	$res = sql_query ("SELECT id FROM cheaters WHERE id IN (" . implode(", ", $_POST[delcheater]) . ")");
	while ($arr = mysql_fetch_assoc($res))
		sql_query ("DELETE from cheaters WHERE id = $arr[id]") or sqlerr();
	$Cache->delete_value('staff_new_cheater_count');
}

$count = get_row_count("cheaters");
if (!$count){
	stderr($lang_cheaterbox['std_oho'], $lang_cheaterbox['std_no_suspect_detected']);
}
$perpage = 10;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "cheaterbox.php?");
stdhead($lang_cheaterbox['head_cheaterbox']);
?>
<style type="text/css">
table.cheaterbox td
{
	text-align: center;
}
</style>
<?php
begin_main_frame();
print("<h1 align=center>".$lang_cheaterbox['text_cheaterbox']."</h1>");
echo('作弊判断方法：<br/>1、如果是服务器卡顿可能造成统计出错,可以忽略.<br/>2、每个上传速度超过11mb/s的都有一定可疑性，如果多次被系统检测则非常可疑<br/>3、如果上传下载速度都很高则可能是带宽较大，如果不放心可以询问<br/>4、如果上传人数明显大于下载人数仍然被系统检测上传速度过快，则非常非常可疑；没有下载者的几乎肯定是作弊（需要确认确实没有下载者而不是系统问题）<br/>5、系统判定不可靠，是否作弊需要多方面人工判断，比如发过种子的可疑性降低，论坛活跃的可疑性降低，长得帅的可疑性降低_(:з」∠)_<br/>就酱，你们慢慢玩吧撒由那拉了您呐by扬扬');
print("<table class=cheaterbox border=1 cellspacing=0 cellpadding=5 align=center>\n");
print("<tr><td class=colhead><nobr>".$lang_cheaterbox['col_added']."</nobr></td><td class=colhead>".$lang_cheaterbox['col_suspect']."</td><td class=colhead><nobr>".$lang_cheaterbox['col_hit']."</nobr></td><td class=colhead>".$lang_cheaterbox['col_torrent']."</td><td class=colhead>".$lang_cheaterbox['col_ul']."</td><td class=colhead>".$lang_cheaterbox['col_dl']."</td><td class=colhead><nobr>".$lang_cheaterbox['col_ann_time']."</nobr></td><td class=colhead><nobr>".$lang_cheaterbox['col_seeders']."</nobr></td><td class=colhead><nobr>".$lang_cheaterbox['col_leechers']."</nobr></td><td class=colhead>".$lang_cheaterbox['col_comment']."</td><td class=colhead><nobr>".$lang_cheaterbox['col_dealt_with']."</nobr></td><td class=colhead><nobr>".$lang_cheaterbox['col_action']."</nobr></td></tr>");

print("<form method=post action=cheaterbox.php>");
$cheatersres = sql_query("SELECT * FROM cheaters ORDER BY dealtwith ASC, id DESC $limit");

while ($row = mysql_fetch_array($cheatersres))
{
	$upspeed = ($row['uploaded'] > 0 ? $row['uploaded'] / $row['anctime'] : 0);
	$lespeed = ($row['downloaded'] > 0 ? $row['downloaded'] / $row['anctime'] : 0);
	$torrentres = sql_query("SELECT name FROM torrents WHERE id=".sqlesc($row['torrentid']));
	$torrentrow = mysql_fetch_array($torrentres);
	if ($torrentrow)
		$torrent = "<a href=details.php?id=".$row['torrentid'].">".htmlspecialchars($torrentrow['name'])."</a>";
	else $torrent = $lang_cheaterbox['text_torrent_does_not_exist'];
	if ($row['dealtwith'])
		$dealtwith = "<font color=green>".$lang_cheaterbox['text_yes']."</font> - " . get_username($row['dealtby']);
	else
		$dealtwith = "<font color=red>".$lang_cheaterbox['text_no']."</font>";

	print("<tr><td class=rowfollow>".gettime($row['added'])."</td><td class=rowfollow>" . get_username($row['userid']) . "</td><td class=rowfollow>" . $row['hit'] . "</td><td class=rowfollow>" . $torrent . "</td><td class=rowfollow>".mksize($row['uploaded']).($upspeed ? " @ ".mksize($upspeed)."/s" : "")."</td><td class=rowfollow>".mksize($row['downloaded']).($lespeed ? " @ ".mksize($lespeed)."/s" : "")."</td><td class=rowfollow>".$row['anctime']." sec"."</td><td class=rowfollow>".$row['seeders']."</td><td class=rowfollow>".$row['leechers']."</td><td class=rowfollow>".htmlspecialchars($row['comment'])."</td><td class=rowfollow>".$dealtwith."</td><td class=rowfollow><input type=\"checkbox\" name=\"delcheater[]\" value=\"" . $row[id] . "\" /></td></tr>\n");
}
?>
<tr><td class="colhead" colspan="12" style="text-align: right"><input type="submit" name="setdealt" value="<?php echo $lang_cheaterbox['submit_set_dealt']?>" /><input type="submit" name="delete" value="<?php echo $lang_cheaterbox['submit_delete']?>" /></td></tr> 
</form>
<?php
print("</table>");
print($pagerbottom);
end_main_frame();
stdfoot();
?>
