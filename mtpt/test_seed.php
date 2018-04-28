<?php
require "include/bittorrent.php";
dbconn(true);
loggedinorreturn();
parked();
stdhead("test_seed");
if (get_user_class() < UC_DOWNLOADER){
	stdmsg("就不给你看", "亲，等级不够啊，不给看！就是不给看！");
	stdfoot();
	exit;
}
$get_user2size = sql_query("SELECT DISTINCT peers.userid,users.totalseed FROM `peers` left join users on peers.userid=users.id WHERE `seeder`  =  'yes'") or sqlerr(__FILE__, __LINE__);

$user_list = array();
while ($arr = mysql_fetch_assoc($get_user2size)){
	$user_list[] = $arr;
}
function compare ($a, $b){
   return $b['totalseed'] - $a['totalseed'];
}
?>
<table class="main" border="1" cellspacing="0" cellpadding="5">
<tr>
<td class="colhead" align="center">排名</td>
<td class="colhead" align="center">用户名</td>
<td class="colhead" align="center">总保种数量</td>
</tr>
<?php
echo "当前保种用户数：<b>",count($user_list),"</b><br/>";
usort($user_list, "compare");
for ($i=0;$i<count($user_list);$i++){
	$j=$i+1;
	print("<tr><td class=\"rowfollow\" align=\"center\">$j</td><td class=\"rowfollow\" align=\"left\">" . get_username($user_list[$i]["userid"]) ."</td><td class=\"rowfollow\" align=\"right\">".mksize_compact_totalseeding($user_list[$i]['totalseed']). "</td></tr>");
}
print("</table>");
stdfoot();
?>
