<script type="text/javascript" >

//js语句
function changenameOnClicked()
{
	 var newname = document.getElementById("newname").value;
	 if(newname=="")
	 {
		alert("无输入");
		return false;
	 }
	 if(newname.length<3||newname.length>13)
	 {
		alert("长度不符合要求，请重新输入");
		document.getElementById("newname").focus();
		return false;
	 }
	 if(confirm("你确定要使用"+newname+"作为你的用户名吗?"))
	 {
		var postForm = document.createElement("form");//表单对象
		postForm.method="post" ;
		postForm.action = 'bonusapp.php' ;
        var actioninput=document.createElement("input") ;
	    actioninput.setAttribute("name", "action") ;
		actioninput.setAttribute("value", "changename");
		postForm.appendChild(actioninput) ;
		var newnameinput = document.createElement("input") ;
		newnameinput.setAttribute("name", "newname") ;
		newnameinput.setAttribute("value", newname);
		postForm.appendChild(newnameinput) ;

		document.body.appendChild(postForm) ;
		postForm.submit() ;
		document.body.removeChild(postForm) ;
	 }
	 return false;
}

</script>
<?php
require_once('include/bittorrent.php');
dbconn();
require_once(get_langfile_path());
require(get_langfile_path("",true));
loggedinorreturn();
parked();

function bonusarray($option){
	global $onegbdownload_bonus,$fivegbdownload_bonus,$tengbdownload_bonus,$onegbupload_bonus,$fivegbupload_bonus,$tengbupload_bonus,$oneinvite_bonus,$customtitle_bonus,$vipstatus_bonus, $basictax_bonus, $taxpercentage_bonus, $bonusnoadpoint_advertisement, $bonusnoadtime_advertisement;
	global $lang_mybonus;
	$bonus = array();
	switch ($option)
	{
		case 1: {//1.0 GB Uploaded
			$bonus['points'] = $onegbupload_bonus;
			$bonus['art'] = 'traffic';
			$bonus['menge'] = 1073741824;
			$bonus['name'] = $lang_mybonus['text_uploaded_one'];
			$bonus['description'] = $lang_mybonus['text_uploaded_note'];
			break;
			}
		case 2: {//5.0 GB Uploaded
			$bonus['points'] = $fivegbupload_bonus;
			$bonus['art'] = 'traffic';
			$bonus['menge'] = 5368709120;
			$bonus['name'] = $lang_mybonus['text_uploaded_two'];
			$bonus['description'] = $lang_mybonus['text_uploaded_note'];
			break;
			}
		case 3: {//10.0 GB Uploaded
			$bonus['points'] = $tengbupload_bonus;
			$bonus['art'] = 'traffic';
			$bonus['menge'] = 10737418240;
			$bonus['name'] = $lang_mybonus['text_uploaded_three'];
			$bonus['description'] = $lang_mybonus['text_uploaded_note'];
			break;
			}
		case 4: {//Invite
			$bonus['points'] = $oneinvite_bonus;
			//$bonus['points'] = 2000000;
			$bonus['art'] = 'invite';
			$bonus['menge'] = 1;
			$bonus['name'] = $lang_mybonus['text_buy_invite'];
			$bonus['description'] = $lang_mybonus['text_buy_invite_note'];
			break;
			}
		case 5: {//Custom Title
			$bonus['points'] = $customtitle_bonus;
			$bonus['art'] = 'title';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_custom_title'];
			$bonus['description'] = $lang_mybonus['text_custom_title_note'];
			break;
			}
		case 6: {//VIP Status
			$bonus['points'] = $vipstatus_bonus;
			$bonus['art'] = 'class';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_vip_status'];
			$bonus['description'] = $lang_mybonus['text_vip_status_note'];
			break;
			}
		case 7: {//Bonus Gift
			$bonus['points'] = 25;
			$bonus['art'] = 'gift_1';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_bonus_gift'];
			$bonus['description'] = $lang_mybonus['text_bonus_gift_note'];
			if ($basictax_bonus || $taxpercentage_bonus){
				$onehundredaftertax = 100 - $taxpercentage_bonus - $basictax_bonus;
				$bonus['description'] .= "<br /><br />".$lang_mybonus['text_system_charges_receiver']."<b>".($basictax_bonus ? $basictax_bonus.$lang_mybonus['text_tax_bonus_point'].add_s($basictax_bonus).($taxpercentage_bonus ? $lang_mybonus['text_tax_plus'] : "") : "").($taxpercentage_bonus ? $taxpercentage_bonus.$lang_mybonus['text_percent_of_transfered_amount'] : "")."</b>".$lang_mybonus['text_as_tax'].$onehundredaftertax.$lang_mybonus['text_tax_example_note']."</br>送麦粒给麦萌萌他就能升级哦";
				}
			break;
			}
		case 8: {
			$bonus['points'] = $bonusnoadpoint_advertisement;
			$bonus['art'] = 'noad';
			$bonus['menge'] = $bonusnoadtime_advertisement * 86400;
			$bonus['name'] = $bonusnoadtime_advertisement.$lang_mybonus['text_no_advertisements'];
			$bonus['description'] = $lang_mybonus['text_no_advertisements_note'];
			break;
			}
		case 9: {
			$bonus['points'] = 1000;
			$bonus['art'] = 'gift_2';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_charity_giving'];
			$bonus['description'] = $lang_mybonus['text_charity_giving_note'];
			break;
			}
		case 10:
		{//UNInvite
			$bonus['points'] = $oneinvite_bonus * 0.8;
			//$bonus['points'] = 10000;
			$bonus['art'] = 'uninvite';
			$bonus['menge'] = 1;
			$bonus['name'] = '出售邀请码';
			$bonus['description'] = '将不需要的邀请码打八折出售换取麦粒。';
			//$bonus['description'] = '邀请码原价出售。';
			break;
			}
		case 12:
			{//交换奖品1
				$bonus['points']  = 30000;
				$bonus['art'] = 'zhanqing';
				//$bonus['menge']  = 1;
				$bonus['name'] = '手环U盘';
				$bonus['description'] = '麦田四周年站庆礼物第二波，手环U盘，每人限购一个。2014年10月29日晚21:00开放购买，数量有限，先到先得。';
				break;
			}
		case 13: {//1.0 GB Downloaded
			$bonus['points'] = $onegbdownload_bonus;
			$bonus['art'] = 'traffic_down';
			$bonus['menge'] = 1073741824;
			$bonus['name'] = $lang_mybonus['text_downloaded_one'];
			$bonus['description'] = $lang_mybonus['text_downloaded_note'];
			break;
			}
		case 14: {//5.0 GB Downloaded
			$bonus['points'] = $fivegbdownload_bonus;
			$bonus['art'] = 'traffic_down';
			$bonus['menge'] = 5368709120;
			$bonus['name'] = $lang_mybonus['text_downloaded_two'];
			$bonus['description'] = $lang_mybonus['text_downloaded_note'];
			break;
			}
		case 15: {//10.0 GB Downloaded
			$bonus['points'] = $tengbdownload_bonus;
			$bonus['art'] = 'traffic_down';
			$bonus['menge'] = 10737418240;
			$bonus['name'] = $lang_mybonus['text_downloaded_three'];
			$bonus['description'] = $lang_mybonus['text_downloaded_note'];
			break;
			}
		default: break;
	}
	return $bonus;
}

if ($bonus_tweak == "disable" || $bonus_tweak == "disablesave")
	stderr($lang_mybonus['std_sorry'],$lang_mybonus['std_karma_system_disabled'].($bonus_tweak == "disablesave" ? "<b>".$lang_mybonus['std_points_active']."</b>" : ""),false);

$action = htmlspecialchars($_GET['action']);
$do = htmlspecialchars($_GET['do']);
unset($msg);
if (isset($do)) {
	if ($do == "upload")
	$msg = $lang_mybonus['text_success_upload'];
	elseif ($do == "download")
	$msg = $lang_mybonus['text_success_download'];
	elseif ($do == "invite")
	$msg = $lang_mybonus['text_success_invites'];
	elseif ($do == "vip")
	$msg =  $lang_mybonus['text_success_vip']."<b>".get_user_class_name(UC_VIP,false,false,true)."</b>".$lang_mybonus['text_success_vip_two'];
	elseif ($do == "vipfalse")
	$msg =  $lang_mybonus['text_no_permission'];
	elseif ($do == "title")
	$msg = $lang_mybonus['text_success_custom_title'];
	elseif ($do == "transfer")
	$msg =  $lang_mybonus['text_success_gift'];
	elseif ($do == "noad")
	$msg =  $lang_mybonus['text_success_no_ad'];
	elseif ($do == "charity")
	$msg =  $lang_mybonus['text_success_charity'];
	elseif ($do == "uninvite")
	$msg = "出售邀请码成功，你失去了一个邀请码，获得了".$oneinvite_bonus * 0.8 ."个麦粒。";
	//$msg = "出售邀请码成功，你失去了一个邀请码，获得了10000个麦粒。";

	else
	$msg = '';
}
	stdhead($CURUSER['username'] . $lang_mybonus['head_karma_page']);

	$bonus = number_format((int)$CURUSER['seedbonus'], 0);
if (!$action) {
	print("<table align=\"center\" width=\"940\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n");
	print("<tr><td class=\"colhead\" colspan=\"4\" align=\"center\"><font class=\"big\">".$SITENAME.$lang_mybonus['text_karma_system']."</font></td></tr>\n");
	if ($msg)
	print("<tr><td align=\"center\" colspan=\"4\"><font class=\"striking\">". $msg ."</font></td></tr>");
?>
<tr><td class="text" align="center" colspan="4"><?php echo $lang_mybonus['text_exchange_your_karma']?><?php echo $bonus?><?php echo $lang_mybonus['text_for_goodies'] ?>
<br /><b><?php echo $lang_mybonus['text_no_buttons_note'] ?></b></td></tr>
<?php

print("<tr><td class=\"colhead\" align=\"center\">".$lang_mybonus['col_option']."</td>".
"<td class=\"colhead\" align=\"left\">".$lang_mybonus['col_description']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_points']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_trade']."</td>".
"</tr>");
for ($i=1; $i <=15; $i++)
{
	$bonusarray = bonusarray($i);
	if (($i == 7 && $bonusgift_bonus == 'no') || ($i == 8 && ($enablead_advertisement == 'no' || $bonusnoad_advertisement == 'no')))
		continue;
	print("<tr>");
	print("<form action=\"?action=exchange\" method=\"post\">");
	print("<td class=\"rowhead_center\"><input type=\"hidden\" name=\"option\" value=\"".$i."\" /><b>".$i."</b></td>");
	if ($i==5){ //for Custom Title!
	$otheroption_title = "<input type=\"text\" name=\"title\" style=\"width: 200px\" maxlength=\"30\" />";
	print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."<br /><br />".$lang_mybonus['text_enter_titile'].$otheroption_title.$lang_mybonus['text_click_exchange']."</td><td class=\"rowfollow\" align='center'>".number_format($bonusarray['points'])."</td>");
	}
	elseif ($i==7){  //for Give A Karma Gift
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b>".$lang_mybonus['text_username']."</b><input type='hidden' name='where' value=\"[url=mybonus.php]麦粒使用[/url]\"/><input type=\"text\" name=\"username\" style=\"width: 200px\" maxlength=\"24\" value=\"麦萌萌\"/></td><td class=\"embedded\"><b>".$lang_mybonus['text_to_be_given']."</b><select name=\"bonusgift\" id=\"giftselect\" onchange=\"customgift();\"> <option value=\"25\"> 25</option><option value=\"50\"> 50</option><option value=\"100\"> 100</option> <option value=\"200\"> 200</option> <option value=\"300\"> 300</option> <option value=\"400\"> 400</option><option value=\"500\"> 500</option><option value=\"1000\" selected=\"selected\"> 1,000</option><option value=\"5000\"> 5,000</option><option value=\"10000\"> 10,000</option><option value=\"0\">".$lang_mybonus['text_custom']."</option></select><input type=\"text\" name=\"bonusgift\" id=\"giftcustom\" style='width: 80px' disabled=\"disabled\" />".$lang_mybonus['text_karma_points']."</td></tr><tr><td class=\"embedded\" colspan=\"2\"><b>".$lang_mybonus['text_message']."</b><input type=\"text\" name=\"message\" style=\"width: 400px\" maxlength=\"100\" /></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."<br /><br />".$lang_mybonus['text_enter_receiver_name']."<br />$otheroption</td><td class=\"rowfollow nowrap\" align='center'>".$lang_mybonus['text_min']."25<br />".$lang_mybonus['text_max']."10,000</td>");
	}
	elseif ($i==9){  //charity giving
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\">".$lang_mybonus['text_ratio_below']."<select name=\"ratiocharity\"> <option value=\"0.1\"> 0.1</option><option value=\"0.2\"> 0.2</option><option value=\"0.3\" selected=\"selected\"> 0.3</option> <option value=\"0.4\"> 0.4</option> <option value=\"0.5\"> 0.5</option> <option value=\"0.6\"> 0.6</option><option value=\"0.7\"> 0.7</option><option value=\"0.8\"> 0.8</option></select>".$lang_mybonus['text_and_downloaded_above']." 10 GB</td><td class=\"embedded\"><b>".$lang_mybonus['text_to_be_given']."</b><select name=\"bonuscharity\" id=\"charityselect\" > <option value=\"1000\"> 1,000</option><option value=\"2000\"> 2,000</option><option value=\"3000\" selected=\"selected\"> 3000</option> <option value=\"5000\"> 5,000</option> <option value=\"8000\"> 8,000</option> <option value=\"10000\"> 10,000</option><option value=\"20000\"> 20,000</option><option value=\"50000\"> 50,000</option></select>".$lang_mybonus['text_karma_points']."</td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."<br /><br />".$lang_mybonus['text_select_receiver_ratio']."<br />$otheroption</td><td class=\"rowfollow nowrap\" align='center'>".$lang_mybonus['text_min']."1,000<br />".$lang_mybonus['text_max']."50,000</td>");
	}
	elseif ($i==11)
	{if(@$res=sql_query("SELECT namecharge from bonusapp where userid ='".$CURUSER[id]."'"))
		{
		$row = mysql_fetch_array( $res );
		$namecharge = $row['namecharge'];
		if ($namecharge ==0||$namecharge==null)$namecharge = 10000;
		elseif ($namecharge >=100000) $namecharge =100000;
		}

			print("<td><h1>改头换面</h1>如果你觉得自己名字不好听或者因为其他原因需要改名字，那么你可以使用你的麦粒换取一个改名字的机会。<br>提示：第一次价格为10000，每一次修改都是上一次的2倍，最高10万.纯数字账号修改半价<br>只允许中文、字母、数字，4至14个字符<br>在此输入新名字<input type=\"text\" name=\"newname\" id=\"newname\" style='width: 160px' /></td><td>本次修改所需麦粒$namecharge</td>");
	}
	elseif($i>=13 && $i<=15){  //for Download
		print("<td class=\"rowfollow\" align='left' style='color:#FF0000'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."</td><td class=\"rowfollow\" align='center'>".number_format($bonusarray['points'])."</td>");
	}
	else{  //for VIP or Upload
		print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."</td><td class=\"rowfollow\" align='center'>".number_format($bonusarray['points'])."</td>");
	}

	if($CURUSER['seedbonus'] >= $bonusarray['points'])
	{
		if ($i==7){
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_karma_gift']."\" /></td>");
		}
		elseif ($i==8){
			if ($enablenoad_advertisement == 'yes' && get_user_class() >= $noad_advertisement)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_class_above_no_ad']."\" disabled=\"disabled\" /></td>");
			elseif (strtotime($CURUSER['noaduntil']) >= TIMENOW)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_already_disabled']."\" disabled=\"disabled\" /></td>");
			elseif (get_user_class() < $bonusnoad_advertisement)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".get_user_class_name($bonusnoad_advertisement,false,false,true).$lang_mybonus['text_plus_only']."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif ($i==9){
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_charity_giving']."\" /></td>");
		}
		elseif($i==4)
		{
			if(get_user_class() < $buyinvite_class)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".get_user_class_name($buyinvite_class,false,false,true).$lang_mybonus['text_plus_only']."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif($i==10)
		{
			if(get_user_class() < $buyinvite_class)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".get_user_class_name($buyinvite_class,false,false,true).$lang_mybonus['text_plus_only']."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif($i==11)
		{
		echo "<td class=\"rowfollow\" align=\"center\"><input type=button onclick=changenameOnClicked() value='改名'/></td>";
		}
		elseif ($i==6)
		{
			if (get_user_class() >= UC_VIP)
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['std_class_above_vip']."\" disabled=\"disabled\" /></td>");
			else
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif ($i==5)
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		elseif($i <= 3 && $i >=1)
		{
			if ($CURUSER['downloaded'] > 0){
				if ($CURUSER['uploaded'] > $dlamountlimit_bonus * 1073741824)//Uploaded amount reach limit
					$ratio = $CURUSER['uploaded']/$CURUSER['downloaded'];
				else $ratio = 0;
			}
			else $ratio = $ratiolimit_bonus + 1; //Ratio always above limit
			if ($ratiolimit_bonus > 0 && $ratio > $ratiolimit_bonus){
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['text_ratio_too_high']."\" disabled=\"disabled\" /></td>");
			}
			else print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif($i>=13 && $i<=15)
		{
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
		elseif($i==12)
		{
			$now = getdate();
			$purchaseLimitNum = 0;
			$allPurchaseNum = 10;
			//$openTime = array('y' => 2014, 'm' => 10, 'd' => 15, 'h' => 22,  'i' =>00, 'a' => 0);
			$openTime = mktime(21, 00, 0, 10, 29, 2014);
			$endTime = mktime(21, 50, 0, 10, 29, 2014);
			$interval = 5;//间隔多少分钟
			if(time() >$endTime){
				$purchaseLimitNum = $allPurchaseNum;
			}
			else {
				$temp =  intval(date('i', time()));
				$temp == 0? $purchaseLimitNum = 1:$purchaseLimitNum = floor($temp/ $interval ) + 1  ;
			}
			if(time() < $openTime){
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"购买未开放\" disabled=\"disabled\" /></td>");
			}
			elseif((int)$CURUSER['seedbonus'] < 30000){
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"麦粒不足\" disabled=\"disabled\" /></td>");
			}
			else {

				$isbuy = get_row_count('dollarprizes', 'where userid='.$CURUSER['id']);
				$exchangedNum = get_row_count('dollarprizes', 'where userid is not null');
				if($isbuy >= 1 ){
					print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"一人一个！\" disabled=\"disabled\" /></td>");
				}
				elseif($exchangedNum  >= $allPurchaseNum + 60){
					print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"你来晚了\" disabled=\"disabled\" /></td>");
				}
				elseif($exchangedNum >= $purchaseLimitNum + 60){
					print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"已送出".($exchangedNum-60)."个U盘\" disabled=\"disabled\" /></td>");
				}
				else{
					print("<td class=\"rowfollow\" align=\"center\"><input type=\"button\" value=\"抢购\" onclick=\"this.disabled='disabled';document.getElementById('qiang').click();\" /><input id=\"qiang\"  style=\"display:none\" type=\"submit\" name=\"submit\" value=\"抢购\" /></td>");
				}
			}
		}
	}
	else
	{
		print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['text_more_points_needed']."\" disabled=\"disabled\" /></td>");
	}
	print("</form>");
	print("</tr>");

}
print("</table><br />");
?>

<table width="940" cellpadding="3">
<tr><td class="colhead" align="center"><font class="big"><?php echo $lang_mybonus['text_what_is_karma'] ?></font></td></tr>
<tr><td class="text" align="left">
<?php
print("<h1>".$lang_mybonus['text_get_by_updown']."</h1>");
print("<ul>");
	print("<li>".$lang_mybonus['text_upload']."</li>");
	print("<li>".$lang_mybonus['text_down']."</li>");
print("</ul>");
print("<h1>".$lang_mybonus['text_get_by_seeding']."</h1>");
print("<ul>");
if ($perseeding_bonus > 0)
	print("<li>".$perseeding_bonus.$lang_mybonus['text_point'].add_s($perseeding_bonus).$lang_mybonus['text_for_seeding_torrent'].$maxseeding_bonus.$lang_mybonus['text_torrent'].add_s($maxseeding_bonus).")</li>");
print("<li>".$lang_mybonus['text_bonus_formula_one'].$tzero_bonus.$lang_mybonus['text_bonus_formula_two'].$nzero_bonus.$lang_mybonus['text_bonus_formula_three'].$bzero_bonus.$lang_mybonus['text_bonus_formula_four'].$l_bonus.$lang_mybonus['text_bonus_formula_five']."</li>");
if ($donortimes_bonus)
	print("<li>".$lang_mybonus['text_donors_always_get'].$donortimes_bonus.$lang_mybonus['text_times_of_bonus']."</li>");
print("</ul>");

		$sqrtof2 = sqrt(2);
		$logofpointone = log(0.1);
		$valueone = $logofpointone / $tzero_bonus;
		$pi = 3.141592653589793;
		$valuetwo = $bzero_bonus * ( 2 / $pi);
		$valuethree = $logofpointone / ($nzero_bonus - 1);
		$timenow = strtotime(date("Y-m-d H:i:s"));
		$sectoweek = 7*24*60*60;
		$A = 0;
		$count = 0;
		$torrentres = sql_query("select torrents.id, torrents.added, torrents.size, torrents.seeders from torrents LEFT JOIN peers ON peers.torrent = torrents.id WHERE peers.userid = $CURUSER[id] AND peers.seeder ='yes' GROUP BY torrents.id")  or sqlerr(__FILE__, __LINE__);
		while ($torrent = mysql_fetch_array($torrentres))
		{
			$weeks_alive = ($timenow - strtotime($torrent[added])) / $sectoweek;
			$gb_size = $torrent[size] / 1073741824;
			$temp = (1 - exp($valueone * $weeks_alive)) * $gb_size * (1 + $sqrtof2 * exp($valuethree * ($torrent[seeders] - 1)));
			$A += $temp;
			$count++;
		}
		if ($count > $maxseeding_bonus)
			$count = $maxseeding_bonus;
		$all_bonus = $valuetwo * atan($A / $l_bonus) + ($perseeding_bonus * $count);
		$percent = $all_bonus * 100 / ($bzero_bonus + $perseeding_bonus * $maxseeding_bonus);
	print("<div align=\"center\">".$lang_mybonus['text_you_are_currently_getting'].round($all_bonus,3).$lang_mybonus['text_point'].add_s($all_bonus).$lang_mybonus['text_per_hour']." (A = ".round($A,1).")</div><table align=\"center\" border=\"0\" width=\"400\"><tr><td class=\"loadbarbg\" style='border: none; padding: 0px;'>");

	if ($percent <= 30) $loadpic = "loadbarred";
	elseif ($percent <= 60) $loadpic = "loadbaryellow";
	else $loadpic = "loadbargreen";
	$width = $percent * 4;
	print("<img class=\"".$loadpic."\" src=\"pic/trans.gif\" style=\"width: ".$width."px;\" alt=\"".$percent."%\" /></td></tr></table>");

print("<h1>".$lang_mybonus['text_other_things_get_bonus']."</h1>");
print("<ul>");
if ($uploadtorrent_bonus > 0)
	print("<li>".$lang_mybonus['text_upload_torrent'].$uploadtorrent_bonus.$lang_mybonus['text_point'].add_s($uploadtorrent_bonus)."</li>");
if ($uploadsubtitle_bonus > 0)
	print("<li>".$lang_mybonus['text_upload_subtitle'].$uploadsubtitle_bonus.$lang_mybonus['text_point'].add_s($uploadsubtitle_bonus)."</li>");
if ($starttopic_bonus > 0)
	print("<li>".$lang_mybonus['text_start_topic'].$starttopic_bonus.$lang_mybonus['text_point'].add_s($starttopic_bonus)."</li>");
if ($makepost_bonus > 0)
	print("<li>".$lang_mybonus['text_make_post'].$makepost_bonus.$lang_mybonus['text_point'].add_s($makepost_bonus)."</li>");
if ($addcomment_bonus > 0)
	print("<li>".$lang_mybonus['text_add_comment'].$addcomment_bonus.$lang_mybonus['text_point'].add_s($addcomment_bonus)."</li>");
if ($pollvote_bonus > 0)
	print("<li>".$lang_mybonus['text_poll_vote'].$pollvote_bonus.$lang_mybonus['text_point'].add_s($pollvote_bonus)."</li>");
if ($offervote_bonus > 0)
	print("<li>".$lang_mybonus['text_offer_vote'].$offervote_bonus.$lang_mybonus['text_point'].add_s($offervote_bonus)."</li>");
if ($funboxvote_bonus > 0)
	print("<li>".$lang_mybonus['text_funbox_vote'].$funboxvote_bonus.$lang_mybonus['text_point'].add_s($funboxvote_bonus)."</li>");
if ($ratetorrent_bonus > 0)
	print("<li>".$lang_mybonus['text_rate_torrent'].$ratetorrent_bonus.$lang_mybonus['text_point'].add_s($ratetorrent_bonus)."</li>");
if ($saythanks_bonus > 0)
	print("<li>".$lang_mybonus['text_say_thanks'].$saythanks_bonus.$lang_mybonus['text_point'].add_s($saythanks_bonus)."</li>");
if ($receivethanks_bonus > 0)
	print("<li>".$lang_mybonus['text_receive_thanks'].$receivethanks_bonus.$lang_mybonus['text_point'].add_s($receivethanks_bonus)."</li>");
if ($adclickbonus_advertisement > 0)
	print("<li>".$lang_mybonus['text_click_on_ad'].$adclickbonus_advertisement.$lang_mybonus['text_point'].add_s($adclickbonus_advertisement)."</li>");
if ($prolinkpoint_bonus > 0)
	print("<li>".$lang_mybonus['text_promotion_link_clicked'].$prolinkpoint_bonus.$lang_mybonus['text_point'].add_s($prolinkpoint_bonus)."</li>");
if ($funboxreward_bonus > 0)
	print("<li>".$lang_mybonus['text_funbox_reward']."</li>");
print($lang_mybonus['text_howto_get_karma_four']);
if ($ratiolimit_bonus > 0)
	print("<li>".$lang_mybonus['text_user_with_ratio_above'].$ratiolimit_bonus.$lang_mybonus['text_and_uploaded_amount_above'].$dlamountlimit_bonus.$lang_mybonus['text_cannot_exchange_uploading']."</li>");
print($lang_mybonus['text_howto_get_karma_five'].$uploadtorrent_bonus.$lang_mybonus['text_point'].add_s($uploadtorrent_bonus).$lang_mybonus['text_howto_get_karma_six']);
?>
</td></tr></table>
<?php
}


// Bonus exchange
if ($action == "exchange") {
	if ($_POST["userid"] || $_POST["points"] || $_POST["bonus"] || $_POST["art"]){
		write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is trying to cheat at bonus system",'mod');
		die($lang_mybonus['text_cheat_alert']);
	}
	$option = (int)$_POST["option"];
	$bonusarray = bonusarray($option);

	$points = $bonusarray['points'];
	$userid = $CURUSER['id'];
	$art = $bonusarray['art'];

	$bonuscomment = $CURUSER['bonuscomment'];
	$seedbonus=$CURUSER['seedbonus']-$points;

	if($CURUSER['seedbonus'] >= $points) {
		//=== trade for upload
		if($art == "traffic") {
			if ($CURUSER['uploaded'] > $dlamountlimit_bonus * 1073741824)//uploaded amount reach limit
			$ratio = $CURUSER['uploaded']/$CURUSER['downloaded'];
			else $ratio = 0;
			if ($ratiolimit_bonus > 0 && $ratio > $ratiolimit_bonus)
				die($lang_mybonus['text_cheat_alert']);
			else {
			$upload = $CURUSER['uploaded'];
			$up = $upload + $bonusarray['menge'];
			$bonuscomment = date("Y-m-d") . " - " .$points. " Points for upload bonus.\n " .$bonuscomment;
			sql_query("UPDATE users SET uploaded = ".sqlesc($up).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=upload");
			}
		}
		elseif($art == "traffic_down") {
			$download = $CURUSER['downloaded'];
			$down = $download + $bonusarray['menge'];
			$bonuscomment = date("Y-m-d") . " - " .$points. " Points for download bonus.\n " .$bonuscomment;
			sql_query("UPDATE users SET downloaded = ".sqlesc($down).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=download");
		}
		//=== trade for one month VIP status ***note "SET class = '10'" change "10" to whatever your VIP class number is
		elseif($art == "class") {
			if (get_user_class() >= UC_VIP) {
				stdmsg($lang_mybonus['std_no_permission'],$lang_mybonus['std_class_above_vip'], 0);
				stdfoot();
				die;
			}
			$vip_until = date("Y-m-d H:i:s",(strtotime(date("Y-m-d H:i:s")) + 7*86400));
			$bonuscomment = date("Y-m-d") . " - " .$points. " Points for 1 month VIP Status.\n " .htmlspecialchars($bonuscomment);
			sql_query("UPDATE users SET class = '".UC_VIP."', vip_added = 'yes', vip_until = ".sqlesc($vip_until).", seedbonus = seedbonus - $points WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=vip");
		}
		//=== trade for invites
		elseif($art == "invite") {
			if(get_user_class() < $buyinvite_class)
				die(get_user_class_name($buyinvite_class,false,false,true).$lang_mybonus['text_plus_only']);
			$invites = $CURUSER['invites'];
			$inv = $invites+$bonusarray['menge'];
			$bonuscomment = date("Y-m-d") . " - " .$points."麦粒购买了一个邀请码.\n " .htmlspecialchars($bonuscomment);
			sql_query("UPDATE users SET invites = ".sqlesc($inv).", seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)."  WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=invite");
		}
		elseif($art == "uninvite") {
			if(get_user_class() < $buyinvite_class)
				die(get_user_class_name($buyinvite_class,false,false,true).$lang_mybonus['text_plus_only']);
			if ($CURUSER['invites'] <= 0)
			die("你的邀请码不足");
			$invites = $CURUSER['invites'];
			$inv = $invites-$bonusarray['menge'];
			//$bonuscomment = date("Y-m-d") . "出售一个邀请码获得 + " .$points. " 麦粒.\n " .htmlspecialchars($bonuscomment);
			$bonuscomment = date("Y-m-d") . " +" .$points."麦粒出售了一个邀请码\n " .htmlspecialchars($bonuscomment);
			sql_query("UPDATE users SET invites = ".sqlesc($inv).", seedbonus = seedbonus + $points, bonuscomment = ".sqlesc($bonuscomment)."  WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=uninvite");
		}
		//=== trade for special title
		/**** the $words array are words that you DO NOT want the user to have... use to filter "bad words" & user class...
		the user class is just for show, but what the hell tongue.gif Add more or edit to your liking.
		*note if they try to use a restricted word, they will recieve the special title "I just wasted my karma" *****/
		elseif($art == "title") {
			//===custom title
			$title = $_POST["title"];
			$title = sqlesc($title);
			$words = array("fuck", "shit", "pussy", "cunt", "nigger", "Staff Leader","SysOp", "Administrator","Moderator","Uploader","Retiree","VIP","Nexus Master","Ultimate User","Extreme User","Veteran User","Insane User","Crazy User","Elite User","Power User","User","Peasant","Champion");
			$title = str_replace($words, $lang_mybonus['text_wasted_karma'], $title);
			$bonuscomment = date("Y-m-d") . " - " .$points. " Points for custom title. Old title is ".htmlspecialchars(trim($CURUSER["title"]))." and new title is $title\n " .htmlspecialchars($bonuscomment);
			sql_query("UPDATE users SET title = $title, seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
			sendshoutbox("[@$CURUSER[username]] 换了一个新的自定义称号叫[b]“$title ”[/b]，真是高端大气上档次，快来围观啊围观啊(づ￣ 3￣)づ");
			redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=title");
		}
		elseif($art == "noad" && $enablead_advertisement == 'yes' && $enablebonusnoad_advertisement == 'yes') {
			if (($enablenoad_advertisement == 'yes' && get_user_class() >= $noad_advertisement) || strtotime($CURUSER['noaduntil']) >= TIMENOW || get_user_class() < $bonusnoad_advertisement)
				die($lang_mybonus['text_cheat_alert']);
			else{
				$noaduntil = date("Y-m-d H:i:s",(TIMENOW + $bonusarray['menge']));
				$bonuscomment = date("Y-m-d") . " - " .$points. " Points for ".$bonusnoadtime_advertisement." days without ads.\n " .htmlspecialchars($bonuscomment);
				sql_query("UPDATE users SET noad='yes', noaduntil='".$noaduntil."', seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id=".sqlesc($userid));
				redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=noad");
			}
		}
		elseif($art == 'gift_2') // charity giving
		{
			$points = 0+$_POST["bonuscharity"];
			if ($points < 1000 || $points > 50000){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_amount_not_allowed_two'], 0);
				stdfoot();
				die();
			}
			$ratiocharity = 0.0+$_POST["ratiocharity"];
			if ($ratiocharity < 0.1 || $ratiocharity > 0.8){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_ratio_not_allowed']);
				stdfoot();
				die();
			}
			if($CURUSER['seedbonus'] >= $points) {
				$points2= number_format($points,1);
				$bonuscomment = date("Y-m-d") . " - " .$points2. " Points as charity to users with ratio below ".htmlspecialchars(trim($ratiocharity)).".\n " .htmlspecialchars($bonuscomment);
				$charityReceiverCount = get_row_count("users", "WHERE enabled='yes' AND 10737418240 < downloaded AND $ratiocharity > uploaded/downloaded");
				if ($charityReceiverCount) {
					sql_query("UPDATE users SET seedbonus = seedbonus - $points, charity = charity + $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
					$charityPerUser = $points/$charityReceiverCount;
					sql_query("UPDATE users SET seedbonus = seedbonus + $charityPerUser WHERE enabled='yes' AND 10737418240 < downloaded AND $ratiocharity > uploaded/downloaded") or sqlerr(__FILE__, __LINE__);
					sendshoutbox("[@$CURUSER[username]] 给分享率低于$ratiocharity 的用户赠送了$points 个麦粒，简直是菩萨再世，快来跪拜啊跪拜啊啊啊啊乀(ˉεˉ乀)");
					redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=charity");
				}
				else
				{
					stdmsg($lang_mybonus['std_sorry'], $lang_mybonus['std_no_users_need_charity']);
					stdfoot();
					die;
				}
			}
		}
		elseif($art == "gift_1" && $bonusgift_bonus == 'yes') {
			//=== trade for giving the gift of karma
			$points = 0+$_POST["bonusgift"];
			$message = $_POST["message"];
			//==gift for peeps with no more options以下
			$usernamegift = explode(",",$_POST["username"]);
			$count = count($usernamegift);
			$added = sqlesc(date("Y-m-d H:i:s"));//$postid = $_POST['postsid'] + 0;
			$postidarr = explode(",",$_POST["postsid"]);


			for ($i = 0; $i < $count; $i++) {


				//$usernamegift[$i] = sqlesc(trim($usernamegift[$i]));
				$arr= mysql_fetch_assoc(sql_query("SELECT id, bonuscomment FROM users WHERE username=".sqlesc($usernamegift[$i]) ));
			if (!$arr ) {
				echo "<script type='text/javascript'> alert('用户名出错,请重新输入!');history.go(-1) </script>";die;
						}
				}

			//$usernamegift = sqlesc(trim($_POST["username"]));以上
			//$res = sql_query("SELECT id, bonuscomment FROM users WHERE username=" . $usernamegift);
			//$arr = mysql_fetch_assoc($res);
			if ($points < 25 || $points > 10000) {
				//write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is hacking bonus system",'mod');
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_amount_not_allowed']);
				stdfoot();
				die();
			}
			$userseedbonusnow = $CURUSER['seedbonus'] ;
			for ($i = 0; $i < $count; $i++)
		{
			//$useridgift[$i] = $arr[id];
			$arr= mysql_fetch_assoc(sql_query("SELECT id, bonuscomment FROM users WHERE username=".sqlesc($usernamegift[$i]) ));
			//$userseedbonus = $arr['seedbonus'];
			$receiverbonuscomment = $arr['bonuscomment'];
			//$usernamegift[$i] = sqlesc($usernamegift[$i]);

			if($userseedbonusnow >= $points) {
				$points2= number_format($points,1);
				$bonuscomment = date("Y-m-d") . " - " .$points2. " Points as gift to ".$usernamegift[$i].".\n " .htmlspecialchars($bonuscomment);
				$userseedbonusnow -= $points;
				$aftertaxpoint = $points;
				if ($taxpercentage_bonus)
					$aftertaxpoint -= $aftertaxpoint * $taxpercentage_bonus * 0.01;
				if ($basictax_bonus)
					$aftertaxpoint -= $basictax_bonus;

				$points2receiver = number_format($aftertaxpoint,1);
				$newreceiverbonuscomment = date("Y-m-d") . " + " .$points2receiver. " Points (after tax) as a gift from ".($CURUSER["username"]).".\n " .htmlspecialchars($receiverbonuscomment);
			//	if ($userid==$arr[id]){
			//		stdmsg($lang_mybonus['text_huh'], $lang_mybonus['text_karma_self_giving_warning'], 0);
			//		stdfoot();
			//		die;
			//	}
			//	if (!$arr[id]){
			//		stdmsg($lang_mybonus['text_error'], $lang_mybonus['text_receiver_not_exists'], 0);
			//		stdfoot();
			//		die;
			//	}

				sql_query("UPDATE users SET seedbonus = seedbonus - $points, bonuscomment = ".sqlesc($bonuscomment)." WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
				if ($arr[id] == 11)
				{
					$addup = $points * 4456543;
					$adddown = $addup/5;
					$newreceiverbonuscomment = date("Y-m-d") . " + " .$points2receiver. " Points (after tax) as a gift from ".($CURUSER[username]).".把他们变成了上传下载量，努力升级~\n"  .htmlspecialchars($receiverbonuscomment) ;
					sql_query("UPDATE users SET  bonuscomment = ".sqlesc($newreceiverbonuscomment).", last_access=now() , last_login=now(), uploaded = uploaded + $addup , downloaded = downloaded + $adddown WHERE id = ".sqlesc($arr[id])) or sqlerr(__FILE__, __LINE__);
					$addup = mksize($addup);
					$adddown = mksize($adddown);
					sendshoutbox("亲爱的[@$CURUSER[username]] 送了 $points 个麦粒给我（¯﹃¯） ，爱死你了~我增长了$addup 上传和$adddown 下载，我要努力升级，我可是要当站长的机器人ノ￣ー￣)ノ");
				}
				else
				sql_query("UPDATE users SET seedbonus = seedbonus + $aftertaxpoint, bonuscomment = ".sqlesc($newreceiverbonuscomment)." WHERE id = ".sqlesc($arr[id])) or sqlerr(__FILE__, __LINE__);

				//为saythanks开辟的渠道 start SamuraiMe,2013.05.19
				if (isset($_POST["torrent_id"])) {
					$ch = curl_init();
					$res = sql_query("INSERT INTO thanks (torrentid, userid, bonus) VALUES (".sqlesc($_POST["torrent_id"]).", " .sqlesc($CURUSER['id']) .", " . sqlesc($points) . ")");
				}
				//为saythanks开辟的渠道 end SamuraiMe,2013.05.19

				//为论坛添加赠送麦粒显示
				if($postidarr[$i] != 0){
					$postarr = array('info' => $CURUSER['username']."于".date("Y-m-d H:i:s")."赠送给".sqlesc($usernamegift[$i])."&nbsp&nbsp".$points."个麦粒"."<br />");
					sql_query("UPDATE posts SET sendlog = CONCAT(sendlog,".sqlesc(implode($postarr)).") WHERE id = ".$postidarr[$i]);
				}
				//===send message
				if ($_POST['where'])
				{$where = $_POST['where'];}
				else $where = "[b]未知页面[/b]（请向管理员反映此错误）";
				$wheremsg = "发信者[b]".$CURUSER['username']."[/b]在".$where."中为您赠送礼物，";
				$subject = sqlesc("收到礼物");
				$msg = $wheremsg."你收到".$points2."个麦粒（扣除手续费后为".$points2receiver."）个麦粒作为礼物。祝福来自".$CURUSER['username']."，此信息由系统代替用户发送。";
				if ($message)
					$msg .= "\n".$CURUSER['username']."说：".$message;
				$msg = sqlesc($msg);
				sql_query("INSERT INTO messages (sender, subject, receiver, msg, added) VALUES({$CURUSER['id']}, $subject, {$arr['id']}, $msg, $added)") or sqlerr(__FILE__, __LINE__);
				//$usernamegift = unesc($_POST["username"]);
				//redirect("" . get_protocol_prefix() . "$BASEURL/mybonus.php?do=transfer");
				echo "成功送给 <b>$usernamegift[$i]</b><i>$points</i>麦粒<br/>";
			}

			else{
				print("<table width=\"940\"><tr><td class=\"colhead\" align=\"left\" colspan=\"2\"><h1>".$lang_mybonus['text_oups']."</h1></td></tr>");
				print("<tr><td align=\"left\"></td><td align=\"left\">".$lang_mybonus['text_not_enough_karma']."<br /><br /></td></tr></table>");
			}

		}
		echo "<script type='text/javascript'> alert('操作成功，具体信息请查看被盖在提示框下面的页面信息。点确定返回上个页面');history.go(-1) </script>";
		}
		elseif($art == 'zhanqing'){
			$isbuy = get_row_count('dollarprizes', 'where userid='.$CURUSER['id']);
			$exchangeNum = get_row_count('dollarprizes', 'where userid is not null');
			$openTime = mktime(21, 00, 0, 10, 29, 2014);
			$endTime = mktime(21, 50, 0, 10, 29, 2014);
			$interval = 5;
			$allPurchaseNum = 10;
			if(time() >$endTime){
				$purchaseLimitNum = $allPurchaseNum;
			}
			else {
				$temp =  intval(date('i', time()));
				$temp == 0? $purchaseLimitNum = 1:$purchaseLimitNum = floor($temp/ $interval ) + 1;
			}
			if($isbuy == 0 && (int)$CURUSER['seedbonus'] >= 30000 && time() > $openTime && $exchangeNum < $purchaseLimitNum + 60){
				sql_query("update dollarprizes set userid=$CURUSER[id] where userid is null and id=$purchaseLimitNum") or sqlerr(__FILE__, __LINE__);
				$isok = get_row_count('dollarprizes', "where id<=$purchaseLimitNum and userid=$CURUSER[id]");
				if($isok == 1 ){
					sql_query("UPDATE users SET seedbonus = seedbonus - 30000 WHERE id =$userid;") or sqlerr(__FILE__, __LINE__);
					$row = mysql_fetch_array(sql_query("select * from dollarprizes where userid=$CURUSER[id] and id<=$purchaseLimitNum"));
					$randNum = $row['randNum'];
					//echo "<script type='text/javascript'> alert('操作成功，具体信息等待站内信。点确定返回上个页面');history.go(-1) </script>";
					echo "<script type='text/javascript'> alert('操作成功，具体信息等待站内信。点确定返回上个页面');history.go(-1) </script>";
				 	sendMessage(0,$CURUSER['id'], "抢到礼品了~", '兑奖码为'.$randNum.'。一定记好兑奖码，不然东西就没了。领取方式稍后再通知。');
					sendshoutbox("[@$CURUSER[username]] 抢到了一个麦田PT纪念U盘，o(*≧▽≦)ツ分我点麦粒让我也抢一个呗");

				}
				else{
					echo "<script type='text/javascript'> alert('手慢一步，这个时间段东西已经被抢完啦~点确定返回上一个页面。');history.go(-1) </script>";
				}
			}
				else{
					echo "<script type='text/javascript'> alert('手慢一步，这个时间段东西已经被抢完啦~点确定返回上一个页面。');history.go(-1)</script>";
				}

		}
	}
}
stdfoot();
?>
