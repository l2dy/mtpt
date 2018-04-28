<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
// Reset Lost Password ACTION
if (get_user_class() < UC_ADMINISTRATOR)
stderr("Error", "Permission denied, Administrator Only.");

stdhead("显示统计数据");
// $datenow = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")));
?>
<form action="" method="POST">
  请选择要查询几天前的数据（不选择默认显示今天）：<select name="day_before">
    <option value=0>0</option>
    <option value=1>1</option>
    <option value=2>2</option>
    <option value=3>3</option>
    <option value=4>4</option>
    <option value=5>5</option>
    <option value=6>6</option>
    <option value=7>7</option>
    <option value=8>8</option>
    <option value=9>9</option>
    <option value=10>10</option>
  </select>
  <input type="submit" value="OK">
</form>
<?php
$day=mysql_real_escape_string($_POST['day_before']);
// $day=1;
$stime=mktime(0,0,0,date("m"),date("d"),date("Y"))-86400*$day;
$etime=$stime+86400;
$datenow = date("Y-m-d H:i:s",$stime);
$dateup = date("Y-m-d H:i:s",$etime);
$datenow_ = date("Y-m-d",$stime);
// $dateweek = date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y"))-86400*7);
$res = sql_query("SELECT id,username,ip,cardnum,cardconfirm,status,added FROM users WHERE added >= '".$datenow."' AND added <= '".$dateup."'");
$invitenum =0;
$cardconfirmnum = 0;
$cardunconnum = 0;
$nwsuafnum = 0;
while ($row = mysql_fetch_assoc($res))
{
	if ($row['cardnum'] == null){
		$row['cardstatus']="校外";
		$row['grade']="校外";
		$invitenum++;
	}
	if($row['cardnum'] != null){
		if($row['cardconfirm'] == 'yes'){
			$row['cardstatus']="已验证";
			$cardconfirmnum++;
		}elseif($row['cardconfirm'] == 'no'){
			$row['cardstatus']="未验证";
			$cardunconnum++;
		}else{
			$row['cardstatus']="未知";
		}
		if(strlen($row['cardnum']) != 10) {
			$row['grade']="不标准";
			$row['cardstatus'] .= $row['cardnum'];
		}else{
			$u_str=substr($row['cardnum'],4,2);
			if($u_str == '01'){
				$u_type="本科";
			}elseif($u_str == '05'){
				$u_type="硕士";
			}elseif($u_str == '06'){
				$u_type="博士";
			}elseif($u_str == '11'){
				$u_type="教职工";
			}else{
				$u_type="未知".$u_str;
			}
			$row['grade'] = substr($row['cardnum'],0,4)."级".$u_type;
		}
	}
	$school = school_ip_location($row[ip]);
	if ($school == '[西北农林科技大学]') $nwsuafnum++;
	$user_list[] = $row;
}

for ($i=0;$i<count($user_list);$i++){//统计各年级用户数
  $grade_str .= $user_list[$i]["grade"].',';
}
$grade_str = substr($grade_str, 0, -1);
$grade_row = explode(",", $grade_str);
$grade_row = array_count_values($grade_row);
asort($grade_row);
echo "<h1>".$datenow_."注册信息</h1>";
echo "今日注册用户<b>".count($user_list)."人</b>,邀请注册<b>".$invitenum."</b>人,西农用户<b>".$nwsuafnum."</b>人，已验证<b>".$cardconfirmnum."</b>人，未验证<b>".$cardunconnum."</b>人<br/>";
?>
<table class="main" border="1" cellspacing="0" cellpadding="5">
<tr>
<td class="colhead" align="center">年级</td>
<td class="colhead" align="center">数目</td>
<td class="colhead" align="center">百分比</td>
</tr>
<?php
while(list($key,$value)=each($grade_row)){
	// echo "$key : $value <br/>";
	$percent = (float)$value * 100 / count($user_list);
	print("<tr><td class=\"rowfollow\" align=\"center\">".$key."</td><td class=\"rowfollow\" align=\"left\">".$value."</td><td class=\"rowfollow\" align=\"left\">".number_format($percent,2)."%</td></tr>");
}
print("</table>");
echo "<br/>";
?>
<table class="main" border="1" cellspacing="0" cellpadding="5">
<tr>
<td class="colhead" align="center">序号</td>
<td class="colhead" align="center">用户名</td>
<td class="colhead" align="center">ip学校</td>
<td class="colhead" align="center">学号状态</td>
<td class="colhead" align="center">账户状态</td>
<td class="colhead" align="center">年级类型</td>
<td class="colhead" align="center">注册时间</td>
</tr>
<?php
for ($i=0;$i<count($user_list);$i++){
	$j=$i+1;
	$school = school_ip_location($user_list[$i][ip]);
	print("<tr><td class=\"rowfollow\" align=\"center\">$j</td><td class=\"rowfollow\" align=\"left\">".get_username($user_list[$i]["id"])."</td><td class=\"rowfollow\" align=\"left\">".$user_list[$i]["ip"].$school."</td><td class=\"rowfollow\" align=\"left\">".$user_list[$i]["cardstatus"]."</td><td class=\"rowfollow\" align=\"left\">".$user_list[$i]["status"]."</td><td class=\"rowfollow\" align=\"left\">".$user_list[$i]["grade"]."</td><td class=\"rowfollow\" align=\"center\">".$user_list[$i]["added"]."</td></tr>");
}
print("</table>");
?>
<h1>近七天注册信息</h1>
<?php
stdfoot();
?>
