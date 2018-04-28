<?php
require_once("include/bittorrent.php");
dbconn();

$langid = 0 + $_GET['sitelanguage'];
if ($langid)
{
	$lang_folder = validlang($langid);
	if(get_langfolder_cookie() != $lang_folder)
	{
		set_langfolder_cookie($lang_folder);
		header("Location: " . $_SERVER['PHP_SELF']);
	}
}
require_once(get_langfile_path("", false, $CURLANGDIR));

failedloginscheck ();
cur_user_check () ;
$ismtpt=true; //判断是否ipv4代理访问
if(!ipv6ip(getip())) //不是ipv6地址就提示，
		$ismtpt=false;
$smarty->assign("ismtpt",$ismtpt);


unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		$showwarn = 1;
	}
}
$smarty->assign("showwarn",$showwarn);
//show_image_code ();

//stdhead($lang_login['head_login']);
$select = 'login';
$smarty->assign("select",$select);
$smarty->assign("show",'no');
$signuplist = $smarty->fetch(MTPTTEMPLATES.'/signuplist.html');
$smarty->assign("signuplist",$signuplist);

$smarty->assign("returnto",$returnto);
$smarty->assign("showhelpbox_main",$showhelpbox_main);
$smarty->assign("BASEURL",$BASEURL);
$smarty->assign("prefix",get_protocol_prefix());
$smarty->assign("smtptype",$smtptype);
$smarty->display(MTPTTEMPLATES.'/login.html');
//stdfoot();
?>
