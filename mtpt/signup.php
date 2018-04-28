<?php
require_once("include/bittorrent.php");
dbconn();


require_once(get_langfile_path("", false, $CURLANGDIR));
cur_user_check ();
$type = $_GET['type'];
if ($type == 'invite')
{
	registration_check();
	failedloginscheck ("Invite signup");
	$code = $_GET["invitenumber"];

	$nuIP = getip();
	$dom = @gethostbyaddr($nuIP);
	if ($dom == $nuIP || @gethostbyname($dom) != $nuIP)
	$dom = "";
	else
	{
	$dom = strtoupper($dom);
	preg_match('/^(.+)\.([A-Z]{2,3})$/', $dom, $tldm);
	$dom = $tldm[2];
	}

	$sq = sprintf("SELECT inviter FROM invites WHERE hash ='%s'",mysql_real_escape_string($code));
	$res = sql_query($sq) or sqlerr(__FILE__, __LINE__);
	$inv = mysql_fetch_assoc($res);
	$inviter = htmlspecialchars($inv["inviter"]);
	if (!$inv&&$code)
		stderr($lang_signup['std_error'], $lang_signup['std_uninvited'], 0);
		
$smarty->assign("code",$code);
$smarty->assign("inviter",$inviter);
$smarty->assign("type",$type);
$emailnotice = ($restrictemaildomain == 'yes' ? $lang_signup['text_email_note'].allowedemails() : "");
$smarty->assign("$emailnotice",$$emailnotice);

if ($_GET['type'] == 'invite')
$select = 'invite';
else
$select = 'signup';
$smarty->assign("select",$select);
$smarty->assign("show",'yes');
$signuplist = $smarty->fetch(MTPTTEMPLATES.'/signuplist.html');
$smarty->assign("signuplist",$signuplist);

$smarty->display(MTPTTEMPLATES.'/signup.html');
//stdfoot();

}
else {
	registration_check("normal");
	failedloginscheck ("Signup");
$emailnotice = ($restrictemaildomain == 'yes' ? $lang_signup['text_email_note'].allowedemails() : "");
$smarty->assign("$emailnotice",$$emailnotice);
if ($_GET['type'] == 'invite')
$select = 'invite';
else
$select = 'signup';
$smarty->assign("select",$select);
$smarty->assign("show",'yes');
$signuplist = $smarty->fetch(MTPTTEMPLATES.'/signuplist.html');
$smarty->assign("signuplist",$signuplist);

$smarty->display(MTPTTEMPLATES.'/signup.html');
//stdfoot();
}



