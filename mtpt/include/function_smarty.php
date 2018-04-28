<?php
function stdmsg($heading, $text, $htmlstrip = false)
{
	if ($htmlstrip) {
		$heading = htmlspecialchars(trim($heading));
		$text = htmlspecialchars(trim($text));
	}
	global $smarty;
	$smarty->assign("heading",$heading);
	$smarty->assign("text",$text);
	//$content=$smarty->fetch(MTPTTEMPLATES.'/function_smarty/stdmsg.html');
	//echo $content;
	$smarty->display(FUNCTIONSMARTY.'/stdmsg.html');
}
/* function begin_main_frame($caption = "", $center = false, $width = 100)
{
	global $smarty;
	$tdextra = "";
	if ($center)
	$tdextra .= " align=\"center\"";
	$width = 940 * $width /100;
	$smarty->assign("tdextra",$tdextra);
	$smarty->assign("caption",$caption);
	$smarty->assign("width",$width);
	$smarty->display(FUNCTIONSMARTY.'/begin_main_frame.html');
}
function end_main_frame()
{
	global $smarty;
	$smarty->display(FUNCTIONSMARTY.'/end_main_frame.html');
}
function begin_frame($caption = "", $center = false, $padding = 10, $width="100%", $caption_center="left")
{
	global $smarty;
	$tdextra = "";
	if ($center)
	$tdextra .= " align=\"center\"";
	$smarty->assign("caption",$caption);
	$smarty->assign("caption_center",$caption_center);
	$smarty->assign("width",$width);
	$smarty->assign("padding",$padding);
	$smarty->assign("tdextra",$tdextra);
	$smarty->display(FUNCTIONSMARTY.'/begin_frame.html');
}
function end_frame()
{
	global $smarty;
	$smarty->display(FUNCTIONSMARTY.'/end_frame.html');
}
function begin_table($fullwidth = false, $padding = 5)
{
	global $smarty;
	$width = "";
	if ($fullwidth)
	$width .= " width=50%";
	$smarty->assign("width",$width);
	$smarty->assign("padding",$padding);
	$smarty->display(FUNCTIONSMARTY.'/begin_table.html');
}
function end_table()
{
	global $smarty;
	$smarty->display(FUNCTIONSMARTY.'/end_table.html');
} */
function begin_main_frame($caption = "", $center = false, $width = 100)
{
	$tdextra = "";
	if ($caption)
	print("<h2>".$caption."</h2>");

	if ($center)
	$tdextra .= " align=\"center\"";

	$width = 940 * $width /100;
	print("<table class=\"main\" width=\"".$width."\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">" .
	"<tr><td class=\"embedded\" $tdextra>");
}

function end_main_frame()
{
	print("</td></tr></table>\n");
}

function begin_frame($caption = "", $center = false, $padding = 10, $width="100%", $caption_center="left")
{
	$tdextra = "";

	if ($center)
	$tdextra .= " align=\"center\"";

	print(($caption ? "<h2 align=\"".$caption_center."\">".$caption."</h2>" : "") . "<table width=\"".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">" . "<tr><td class=\"text\" $tdextra>\n");

}

function end_frame()
{
	print("</td></tr></table>\n");
}

function begin_table($fullwidth = false, $padding = 5)
{
	$width = "";

	if ($fullwidth)
	$width .= " width=50%";
	print("<table class=\"main".$width."\" border=\"1\" cellspacing=\"0\" cellpadding=\"".$padding."\">");
}

function end_table()
{
	print("</table>\n");
}

/* function tr($x,$y,$noesc=0,$relation='') {
	global $smarty;
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	$smarty->assign("relation",$relation);
	$smarty->assign("x",$x);
	$smarty->assign("a",$a);
	$smarty->display(FUNCTIONSMARTY.'/tr.html');
}
function tr_small($x,$y,$noesc=0,$relation='') {
	global $smarty;
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		//$a = str_replace("\n", "<br />\n", $a);
	}
	$smarty->assign("relation",$relation);
	$smarty->assign("x",$x);
	$smarty->assign("a",$a);
	$smarty->display(FUNCTIONSMARTY.'/tr_small.html');
 }*/
function tr($x,$y,$noesc=0,$relation='') {
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		$a = str_replace("\n", "<br />\n", $a);
	}
	print("<tr".( $relation ? " relation = \"$relation\"" : "")."><td class=\"rowhead nowrap\" valign=\"top\" align=\"right\">$x</td><td class=\"rowfollow\" valign=\"top\" align=\"left\">".$a."</td></tr>\n");
}

function tr_small($x,$y,$noesc=0,$relation='') {
	if ($noesc)
	$a = $y;
	else {
		$a = htmlspecialchars($y);
		//$a = str_replace("\n", "<br />\n", $a);
	}
	print("<tr".( $relation ? " relation = \"$relation\"" : "")."><td width=\"1%\" class=\"rowhead nowrap\" valign=\"top\" align=\"right\">".$x."</td><td width=\"99%\" class=\"rowfollow\" valign=\"top\" align=\"left\">".$a."</td></tr>\n");
}

function quickreply($formname, $taname,$submit){
	global $smarty;
	$smarty->assign("taname",$taname);
	$smarty->assign("submit",$submit);
	$smarty->assign("smile_row",smile_row($formname,$taname));
	$smarty->display(FUNCTIONSMARTY.'/quickreply.html');
	}

function findip() {
        if (isset($_SERVER)) {
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ipv6ip($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && ipv6ip($_SERVER['HTTP_CLIENT_IP'])) {
                        $ip = $_SERVER['HTTP_CLIENT_IP'];
                } else {
                        $ip = $_SERVER['REMOTE_ADDR'];
                }
        } else {
                if (getenv('HTTP_X_FORWARDED_FOR') && ipv6ip(getenv('HTTP_X_FORWARDED_FOR'))) {
                        $ip = getenv('HTTP_X_FORWARDED_FOR');
                } elseif (getenv('HTTP_CLIENT_IP') && ipv6ip(getenv('HTTP_CLIENT_IP'))) {
                        $ip = getenv('HTTP_CLIENT_IP');
                } else {
                        $ip = getenv('REMOTE_ADDR');
                }
        }
        return $ip;
}
/**
* @date 2013-12-4 下午7:33:21
* @describe 网站header部分
*/
function stdhead($title = "", $msgalert = true, $script = "", $place = "")
{
//require_once ('include/bittorrent.php');
global $smarty;
global $lang_functions;
global $CURUSER, $CURLANGDIR, $USERUPDATESET, $iplog1, $oldip, $SITE_ONLINE, $FUNDS, $SITENAME, $SLOGAN, $logo_main, $BASEURL, $offlinemsg, $showversion,$enabledonation, $staffmem_class, $titlekeywords_tweak, $metakeywords_tweak, $metadescription_tweak, $cssdate_tweak, $deletenotransfertwo_account, $neverdelete_account, $iniupload_main;
global $tstart;
global $Cache;
global $Advertisement;
$ismtpt=true; //判断是否ipv4代理访问
$istunnel=false; //判断是否隧道访问
$Cache->setLanguage($CURLANGDIR);
//$title=$lang_index['head_home'];
$Advertisement = new ADVERTISEMENT($CURUSER['id']);

$cssupdatedate = $cssdate_tweak;
// Variable for Start Time
$tstart = getmicrotime(); // Start time
//Insert old ip into iplog
if ($CURUSER){
	if ($iplog1 == "yes") {
		if (($oldip != $CURUSER["ip"]) && $CURUSER["ip"])
			sql_query("INSERT INTO iplog (ip, userid, access) VALUES (" . sqlesc($CURUSER['ip']) . ", " . $CURUSER['id'] . ", '" . $CURUSER['last_access'] . "')");
	}
	$USERUPDATESET[] = "last_access = ".sqlesc(date("Y-m-d H:i:s"));
	$USERUPDATESET[] = "ip = ".sqlesc($CURUSER['ip']);
}
header("Content-Type: text/html; charset=utf-8; Cache-control:private");
//header("Pragma: No-cache");
if ($title == "")
	$title = $SITENAME;
else
	$title = $SITENAME." :: " . htmlspecialchars($title);
if ($titlekeywords_tweak)
	$title .= " ".htmlspecialchars($titlekeywords_tweak);
$title .= $showversion;
if ($SITE_ONLINE == "no") {
	if (get_user_class() < UC_ADMINISTRATOR) {
		die($lang_functions['std_site_down_for_maintenance']);
	}
	else
	{
		$offlinemsg = true;
	}
}
if(!ipv6ip(getip())) //不是ipv6地址就提示，
		$ismtpt=false;
$istunnel=!ip_filter(getip(),true,true);//判断是否隧道
$smarty->assign("title",$title);
$smarty->assign("metakeywords_tweak",htmlspecialchars($metakeywords_tweak));
$smarty->assign("metadescription_tweak",htmlspecialchars($metadescription_tweak));
$smarty->assign("project_name",PROJECTNAME);
$smarty->assign("style_addicode",get_style_addicode());
$css_uri = get_css_uri();
$smarty->assign("css_uri",$css_uri);
$cssupdatedate=($cssupdatedate ? "?".htmlspecialchars($cssupdatedate) : "");
$smarty->assign("cssupdatedate",$cssupdatedate);
$smarty->assign("site_name",$SITENAME);
$smarty->assign("font_css_uri",get_font_css_uri());
$smarty->assign("forum_pic_folder",get_forum_pic_folder());
if ($CURUSER){
	$caticonrow = get_category_icon_row($CURUSER['caticon']);
$smarty->assign("css_file",htmlspecialchars($caticonrow['cssfile']));
	}else{
$smarty->assign("css_file","");	
	}

$smarty->assign("logo_main",$logo_main);
$smarty->assign("slogan",htmlspecialchars($SLOGAN));

if ($Advertisement->enable_ad()){
		$headerad=$Advertisement->get_ad('header');
		if ($headerad){
			$smarty->assign("headerad",$headerad[0]);
		}
}else{
	$smarty->assign("headerad","");
}
$smarty->assign("enabledonation",$enabledonation);
$smarty->assign("cur_user",$CURUSER);
$smarty->assign("ismtpt",$ismtpt);
$smarty->assign("istunnel",$istunnel);

$smarty->assign("lang_functions",$lang_functions);
if (!$CURUSER) {
	$smarty->display(MTPTTEMPLATES."/function_smarty/header.html");	
} 
else {
//导航菜单
	 $script_name = $_SERVER["SCRIPT_FILENAME"];
	if (preg_match("/index/i", $script_name)) {
		$selected = "home";
	}elseif (preg_match("/forums/i", $script_name)) {
		$selected = "forums";
	}elseif (preg_match("/torrents/i", $script_name)) {
		$selected = "torrents";
	}elseif (preg_match("/music/i", $script_name)) {
		$selected = "music";
	}elseif (preg_match("/offers/i", $script_name) OR preg_match("/offcomment/i", $script_name)) {
		$selected = "offers";
	}elseif (preg_match("/upload/i", $script_name)) {
		$selected = "upload";
	}elseif (preg_match("/subtitles/i", $script_name)) {
		$selected = "subtitles";
	}elseif (preg_match("/usebonus/i", $script_name)) {
		$selected = "usebonus";
	}elseif (preg_match("/topten/i", $script_name)) {
		$selected = "topten";
	}elseif (preg_match("/log/i", $script_name)) {
		$selected = "log";
	}elseif (preg_match("/rules/i", $script_name)) {
		$selected = "rules";
	}elseif (preg_match("/faq/i", $script_name)) {
		$selected = "faq";
	}elseif (preg_match("/staff/i", $script_name)) {
		$selected = "staff";
	}elseif (preg_match("/signin/i", $script_name)) {
		$selected = "signin";
	}elseif (preg_match("/recycle/i", $script_name)) {
		$selected = "recycle";
	}
	elseif(preg_match("/viewrequest/i", $script_name)){
		$selected='viewrequest';
	}
	else
		$selected = "";
$smarty->assign("select",$selected);
/* begin_main_frame();
	menu ();
	end_main_frame(); */
$smarty->assign("enableextforum",$enableextforum);
$smarty->assign("extforumurl",$extforumurl);
$smarty->assign("enablespecial",$enablespecial);
$smarty->assign("enableoffer",$enableoffer);
	if ($CURUSER){
		if ($where_tweak == 'yes')
			$USERUPDATESET[] = "page = ".sqlesc($selected);
	}

	$datum = getdate();
	$datum["hours"] = sprintf("%02.0f", $datum["hours"]);
	$datum["minutes"] = sprintf("%02.0f", $datum["minutes"]);
	$ratio = get_ratio($CURUSER['id']);

	//// check every 15 minutes //////////////////
	$messages = $Cache->get_value('user_'.$CURUSER["id"].'_inbox_count');
	if ($messages == ""){
		$messages = get_row_count("messages", "WHERE receiver=" . sqlesc($CURUSER["id"]) . " AND location<>0");
		$Cache->cache_value('user_'.$CURUSER["id"].'_inbox_count', $messages, 900);
	}
	$outmessages = $Cache->get_value('user_'.$CURUSER["id"].'_outbox_count');
	if ($outmessages == ""){
		$outmessages = get_row_count("messages","WHERE sender=" . sqlesc($CURUSER["id"]) . " AND saved='yes'");
		$Cache->cache_value('user_'.$CURUSER["id"].'_outbox_count', $outmessages, 900);
	}
	if (!$connect = $Cache->get_value('user_'.$CURUSER["id"].'_connect')){
		$res3 = sql_query("SELECT connectable FROM peers WHERE userid=" . sqlesc($CURUSER["id"]) . " LIMIT 1");
		if($row = mysql_fetch_row($res3))
			$connect = $row[0];
		else $connect = 'unknown';
		$Cache->cache_value('user_'.$CURUSER["id"].'_connect', $connect, 900);
	}
$smarty->assign("connect",$connect);
	//// check every 60 seconds //////////////////
	$activeseed = $Cache->get_value('user_'.$CURUSER["id"].'_active_seed_count');
	if ($activeseed == ""){
		$activeseed = get_row_count("peers","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='yes'");
		$Cache->cache_value('user_'.$CURUSER["id"].'_active_seed_count', $activeseed, 60);
	}
	$activeleech = $Cache->get_value('user_'.$CURUSER["id"].'_active_leech_count');
	if ($activeleech == ""){
		$activeleech = get_row_count("peers","WHERE userid=" . sqlesc($CURUSER["id"]) . " AND seeder='no'");
		$Cache->cache_value('user_'.$CURUSER["id"].'_active_leech_count', $activeleech, 60);
	}
	$unread = $Cache->get_value('user_'.$CURUSER["id"].'_unread_message_count');
	if ($unread == ""){
		$unread = get_row_count("messages","WHERE receiver=" . sqlesc($CURUSER["id"]) . " AND unread='yes'");
		$Cache->cache_value('user_'.$CURUSER["id"].'_unread_message_count', $unread, 60);
	}
	
if($connect == 'no'){
	if(!$Cache->get_value('connectfaq_'.$CURUSER['id'])){
	$Cache->cache_value('connectfaq_'.$CURUSER['id'],'1',60);
	}
}
$smarty->assign("username",get_username($CURUSER['id']));
$smarty->assign("userclass",get_user_class());
$smarty->assign("UC_UPLOADER",UC_UPLOADER);
$smarty->assign("UC_MODERATOR",UC_MODERATOR);
$smarty->assign("UC_SYSOP",UC_SYSOP);
$smarty->assign("seedbonus",number_format((int)$CURUSER['seedbonus'], 0));
$smarty->assign("CURUSERID",$CURUSER['id']);
$smarty->assign("CURUSER_INVITES",$CURUSER['invites']);
$smarty->assign("ratio",$ratio);
$smarty->assign("CURUSER_UPLOADED",mksize($CURUSER['uploaded']));
$smarty->assign("CURUSER_DOWNLOADED",mksize($CURUSER['downloaded']));
$smarty->assign("activeseed",$activeseed);
$smarty->assign("activeleech",$activeleech);
$smarty->assign("connectable",$connectable);
$smarty->assign("datum",$datum);
$smarty->assign("staffmem_class",$staffmem_class);

	if (get_user_class() >= $staffmem_class){
	$totalreports = $Cache->get_value('staff_report_count');
	if ($totalreports == ""){
		$totalreports = get_row_count("reports");
		$Cache->cache_value('staff_report_count', $totalreports, 900);
	}
	$totalsm = $Cache->get_value('staff_message_count');
	if ($totalsm == ""){
		$totalsm = get_row_count("staffmessages");
		$Cache->cache_value('staff_message_count', $totalsm, 900);
	}
	$totalcheaters = $Cache->get_value('staff_cheater_count');
	if ($totalcheaters == ""){
		$totalcheaters = get_row_count("cheaters");
		$Cache->cache_value('staff_cheater_count', $totalcheaters, 900);
	}
$smarty->assign("totalcheaters",$totalcheaters);
$smarty->assign("totalreports",$totalreports);
$smarty->assign("totalsm",$totalsm);
	}
$smarty->assign("unread",$unread);
	$messages=$messages ? $messages." (".$unread.$lang_functions['text_message_new'].")" : "0";
$smarty->assign("messages",$messages);
$smarty->assign("outmessages",$outmessages ? $outmessages : "0");

//每日登陆奖励
global $loginadd;require "./memcache.php";
if($loginadd == 'yes'){
if($memcache){
	if($memcache->get('continuelogin_'.$CURUSER['id'])!='1'){
	$res = sql_query("SELECT salary,salarynum,maxsalarynum FROM users WHERE id=".$CURUSER['id']) or sqlerr();
    $arr = mysql_fetch_assoc($res);
    $showtime=date("Y-m-d",time());
	$d1=strtotime($showtime);
	$d2=strtotime($arr['salary']);
	$Days=round(($d1-$d2)/3600/24);
  
		$addbonus = 4;
if($Days == 1){
	$salarynum = $arr['salarynum'];
	if($salarynum > 7) 
	{$addbonus= 15;}
	else
	$addbonus = $salarynum + 4;
	mysql_query("UPDATE users SET seedbonus=seedbonus+$addbonus , salary=now(), salarynum=salarynum + 1 ,maxsalarynum = IF (salarynum>maxsalarynum,salarynum,maxsalarynum) WHERE id=".$CURUSER['id']);
}else if($Days > 0){
	mysql_query("UPDATE users SET seedbonus=seedbonus+$addbonus , salary=now(), salarynum=salarynum + 1 ,maxsalarynum = IF (salarynum>maxsalarynum,salarynum,maxsalarynum) WHERE id=".$CURUSER['id']);

		}
	}
	$memcache->set('continuelogin_'.$CURUSER['id'],'1',false,3600) or die ("");
}
$smarty->assign("loginadd",$loginadd);
$smarty->assign("Days",$Days);
$smarty->assign("addbonus",$addbonus);
$smarty->assign("salarynum",$salarynum);
}


	if ($Advertisement->enable_ad()){
			$belownavad=$Advertisement->get_ad('belownav');
			if ($belownavad)
			$belownavad = "<div align=\"center\" style=\"margin-bottom: 10px\" id=\"belownav\">".$belownavad[0]."</div>";
	}
$smarty->assign("belownavad",$belownavad);
//信息色块提示
if ($msgalert)
{
	global $msgalerttab;
	function msgalert($url, $text, $bgcolor = "red")
	{
		global $msgalerttab;
		$msgalerttab .= ("<p><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td style='border: none; padding: 9px; background: ".$bgcolor."'>\n");
		$msgalerttab .= ("<b><a href=\"".$url."\"><font face=\"verdana\" size=\"3\" color=\"white\">".$text."</font></a></b>");
		$msgalerttab .= ("</td></tr></table></p><br />");//修改于2011-11-23 @zhaojiajia
	}
	if($CURUSER['leechwarn'] == 'yes')
	{
		$kicktimeout = gettime($CURUSER['leechwarnuntil'], false, false, true);
		$text = $lang_functions['text_please_improve_ratio_within'].$kicktimeout.$lang_functions['text_or_you_will_be_banned'];
		msgalert("faq.php#id17", $text, "orange");
	}
	if($deletenotransfertwo_account) //inactive account deletion notice
	{
		if ($CURUSER['downloaded'] == 0 && ($CURUSER['uploaded'] == 0 || $CURUSER['uploaded'] == $iniupload_main))
		{
			$neverdelete_account = ($neverdelete_account <= UC_VIP ? $neverdelete_account : UC_VIP);
			if (get_user_class() < $neverdelete_account)
			{
				$secs = $deletenotransfertwo_account*24*60*60;
				$addedtime = strtotime($CURUSER['added']);
				if (TIMENOW > $addedtime+($secs/3)) // start notification if one third of the time has passed
				{
					$kicktimeout = gettime(date("Y-m-d H:i:s", $addedtime+$secs), false, false, true);
					$text = $lang_functions['text_please_download_something_within'].$kicktimeout.$lang_functions['text_inactive_account_be_deleted'];
					msgalert("rules.php", $text, "gray");
				}
			}
		}
	}
	if($CURUSER['showclienterror'] == 'yes')
	{
		$text = $lang_functions['text_banned_client_warning'];
		msgalert("faq.php#id29", $text, "black");
	}

	if ($unread) 
	{
		$unreadidres = sql_query("SELECT id FROM messages WHERE receiver=" . sqlesc($CURUSER["id"]) . " AND unread='yes'") or sqlerr(__FILE__,__LINE__);
		$unreadidrow = mysql_fetch_assoc($unreadidres);
		$text = $lang_functions['text_you_have'].$unread.$lang_functions['text_new_message'] . add_s($unread) . $lang_functions['text_click_here_to_read'];
		msgalert("messages.php?action=viewmessage&id=".$unreadidrow['id'],$text, "indigo");
	}

	/* if ($unread) // new message sound reminder,2010-12-23
	{
		$unreadsound = $Cache->get_value("unread_sound_".$CURUSER['id']);
		if($unreadsound != '1'){
		echo "<object type=\"application/x-mplayer2\" data=\"sound/message.wav\" width=\"0\" height=\"0\"> <param name=\"src\" value=\"sound/message.wav\"> <param name=\"autoplay\" value=\"1\"> </object>"; //support ie,firefox,chrome..
		}
		$Cache->cache_value("unread_sound_".$CURUSER['id'], '1', 120);
	}*/


/*
	$pending_invitee = $Cache->get_value('user_'.$CURUSER["id"].'_pending_invitee_count');
	if ($pending_invitee == ""){
		$pending_invitee = get_row_count("users","WHERE status = 'pending' AND invited_by = ".sqlesc($CURUSER[id]));
		$Cache->cache_value('user_'.$CURUSER["id"].'_pending_invitee_count', $pending_invitee, 900);
	}
	if ($pending_invitee > 0)
	{
		$text = $lang_functions['text_your_friends'].add_s($pending_invitee).is_or_are($pending_invitee).$lang_functions['text_awaiting_confirmation'];
		msgalert("invite.php?id=".$CURUSER[id],$text, "red");
	}*/
	$settings_script_name = $_SERVER["SCRIPT_FILENAME"];
	if (!preg_match("/index/i", $settings_script_name))
	{
		$new_news = $Cache->get_value('user_'.$CURUSER["id"].'_unread_news_count');
		if ($new_news == ""){
			$new_news = get_row_count("news","WHERE notify = 'yes' AND added > ".sqlesc($CURUSER['last_home']));
			$Cache->cache_value('user_'.$CURUSER["id"].'_unread_news_count', $new_news, 300);
		}
		if ($new_news > 0)
		{
			$text = $lang_functions['text_there_is'].is_or_are($new_news).$new_news.$lang_functions['text_new_news'];
			msgalert("index.php",$text, "green");
		}
	}

	if (get_user_class() >= $staffmem_class)
	{
		$numreports = $Cache->get_value('staff_new_report_count');
		if ($numreports == ""){
			$numreports = get_row_count("reports","WHERE dealtwith=0");
			$Cache->cache_value('staff_new_report_count', $numreports, 900);
		}
		if ($numreports){
			$text = $lang_functions['text_there_is'].is_or_are($numreports).$numreports.$lang_functions['text_new_report'] .add_s($numreports);
			msgalert("reports.php",$text, "blue");
		}
		$nummessages = $Cache->get_value('staff_new_message_count');
		if ($nummessages == ""){
			$nummessages = get_row_count("staffmessages","WHERE answered='no'");
			$Cache->cache_value('staff_new_message_count', $nummessages, 900);
		}
		if ($nummessages > 0) {
			$text = $lang_functions['text_there_is'].is_or_are($nummessages).$nummessages.$lang_functions['text_new_staff_message'] . add_s($nummessages);
			msgalert("staffbox.php",$text, "blue");
		}
		$numcheaters = $Cache->get_value('staff_new_cheater_count');
		if ($numcheaters == ""){
			$numcheaters = get_row_count("cheaters","WHERE dealtwith=0");
			$Cache->cache_value('staff_new_cheater_count', $numcheaters, 900);
		}
		if ($numcheaters){
			$text = $lang_functions['text_there_is'].is_or_are($numcheaters).$numcheaters.$lang_functions['text_new_suspected_cheater'] .add_s($numcheaters);
			msgalert("cheaterbox.php",$text, "blue");
		}
	}
}
$smarty->assign("msgalerttab",$msgalerttab);
$smarty->assign("offlinemsg",$offlinemsg);
$smarty->display(MTPTTEMPLATES."/function_smarty/header.html");
	// if(!$ismtpt) //ip地址检测，假期可用来签到
	// {
		// stdmsg("你访问了假麦田", "<h1>您使用IPV4地址访问了假麦田,该地址目前只能用来签到。<br/><b>请认准麦田PT官方地址：(pt.nwsuaf6.edu.cn)复制括号中的内容到浏览器地址栏粘贴访问。</b><br/>麦田PT只支持IPV6访问，西农用户请使用锐捷有线认证在宿舍及部分办公区获取IPV6地址。</h1></div>");
		// stdfoot();
		// exit;
	// }
}
}

?>
