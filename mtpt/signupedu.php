<?php
require_once("include/bittorrent.php");
dbconn();
//require_once(get_langfile_path("", false, $CURLANGDIR));
cur_user_check ();
registration_check("edureg");
failedloginscheck ("Signup");

$select = 'signupedu';
$smarty->assign("select",$select);
$smarty->assign("show",'yes');
$signuplist = $smarty->fetch(MTPTTEMPLATES.'/signuplist.html');
$smarty->assign("signuplist",$signuplist);

$smarty->display(MTPTTEMPLATES.'/signupedu.html');
//stdfoot();
