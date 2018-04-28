<?php
require_once("include/benc.php");
require_once("include/bittorrent.php");

ini_set("upload_max_filesize",$max_torrent_size);
dbconn();
require_once(get_langfile_path());
require(get_langfile_path("",true));
loggedinorreturn();

function bark($msg) {
	global $lang_takeupload;
	genbark($msg, $lang_takeupload['std_upload_failed']);
	die;
}


if ($CURUSER["uploadpos"] == 'no')
	die;

foreach(explode(":","descr:type:name") as $v) {
	if (!isset($_POST[$v]))
	bark($lang_takeupload['std_missing_form_data']);
}

if (!isset($_FILES["file"]))
bark($lang_takeupload['std_missing_form_data']);

$f = $_FILES["file"];
$fname = unesc($f["name"]);
if (empty($fname))
bark($lang_takeupload['std_empty_filename']);
if (get_user_class()>=$beanonymous_class && $_POST['uplver'] == 'yes') {
	$anonymous = "yes";
	$anon = "Anonymous";
}
else {
	$anonymous = "no";
	$anon = $CURUSER["username"];
}

$url = parse_imdb_id($_POST['url']);
$dburl = parse_imdb_id($_POST['dburl']);

$nfo = '';
if ($enablenfo_main=='yes'){
$nfofile = $_FILES['nfo'];
if ($nfofile['name'] != '') {

	if ($nfofile['size'] == 0)
	bark($lang_takeupload['std_zero_byte_nfo']);

	if ($nfofile['size'] > 65535)
	bark($lang_takeupload['std_nfo_too_big']);

	$nfofilename = $nfofile['tmp_name'];

	if (@!is_uploaded_file($nfofilename))
	bark($lang_takeupload['std_nfo_upload_failed']);
	$nfo = str_replace("\x0d\x0d\x0a", "\x0d\x0a", @file_get_contents($nfofilename));
}
}


$small_descr = unesc(RemoveXSS($_POST["small_descr"]));

$descr = unesc(RemoveXSS($_POST["descr"]));
if (!$descr)
bark($lang_takeupload['std_blank_description']);

$catid = (0 + $_POST["type"]);
$sourceid = (0 + $_POST["source_sel"]);
$mediumid = (0 + $_POST["medium_sel"]);
$codecid = (0 + $_POST["codec_sel"]);
$standardid = (0 + $_POST["standard_sel"]);
$processingid = (0 + $_POST["processing_sel"]);
$teamid = (0 + $_POST["team_sel"]);
$audiocodecid = (0 + $_POST["audiocodec_sel"]);

if (!is_valid_id($catid))
bark($lang_takeupload['std_category_unselected']);

if (!validfilename($fname))
bark($lang_takeupload['std_invalid_filename']);
if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
bark($lang_takeupload['std_filename_not_torrent']);
$shortfname = $torrent = $matches[1];
if (!empty($_POST["name"]))
$torrent = unesc(RemoveXSS($_POST["name"]));
if ($f['size'] > $max_torrent_size)
bark($lang_takeupload['std_torrent_file_too_big'].number_format($max_torrent_size).$lang_takeupload['std_remake_torrent_note']);
$tmpname = $f["tmp_name"];
if (!is_uploaded_file($tmpname))
bark("eek");
if (!filesize($tmpname))
bark($lang_takeupload['std_empty_file']);

$dict = bdec_file($tmpname, $max_torrent_size);
if (!isset($dict))
bark($lang_takeupload['std_not_bencoded_file']);
if ($sourceid == 0)
bark($lang_takeupload['std_category_unlidselected']);
function dict_check($d, $s) {
	global $lang_takeupload;
	if ($d["type"] != "dictionary")
	bark($lang_takeupload['std_not_a_dictionary']);
	$a = explode(":", $s);
	$dd = $d["value"];
	$ret = array();
	foreach ($a as $k) {
		unset($t);
		if (preg_match('/^(.*)\((.*)\)$/', $k, $m)) {
			$k = $m[1];
			$t = $m[2];
		}
		if (!isset($dd[$k]))
		bark($lang_takeupload['std_dictionary_is_missing_key']);
		if (isset($t)) {
			if ($dd[$k]["type"] != $t)
			bark($lang_takeupload['std_invalid_entry_in_dictionary']);
			$ret[] = $dd[$k]["value"];
		}
		else
		$ret[] = $dd[$k];
	}
	return $ret;
}

function dict_get($d, $k, $t) {
	global $lang_takeupload;
	if ($d["type"] != "dictionary")
	bark($lang_takeupload['std_not_a_dictionary']);
	$dd = $d["value"];
	if (!isset($dd[$k]))
	return;
	$v = $dd[$k];
	if ($v["type"] != $t)
	bark($lang_takeupload['std_invalid_dictionary_entry_type']);
	return $v["value"];
}

//list($ann, $info) = dict_check($dict, "announce(string):info");
$info = dict_check($dict, "info");
$info = $info[0];
list($dname, $plen, $pieces) = dict_check($info, "name(string):piece length(integer):pieces(string)");

/*
if (!in_array($ann, $announce_urls, 1))
{
$aok=false;
foreach($announce_urls as $au)
{
if($ann=="$au?passkey=$CURUSER[passkey]")  $aok=true;
}
if(!$aok)
bark("Invalid announce url! Must be: " . $announce_urls[0] . "?passkey=$CURUSER[passkey]");
}
*/


if (strlen($pieces) % 20 != 0)
bark($lang_takeupload['std_invalid_pieces']);

$filelist = array();
$totallen = dict_get($info, "length", "integer");
if (isset($totallen)) {
	$filelist[] = array($dname, $totallen);
	$type = "single";
}
else {
	$flist = dict_get($info, "files", "list");
	if (!isset($flist))
	bark($lang_takeupload['std_missing_length_and_files']);
	if (!count($flist))
	bark("no files");
	$totallen = 0;
	foreach ($flist as $fn) {
		list($ll, $ff) = dict_check($fn, "length(integer):path(list)");
		$totallen += $ll;
		$ffa = array();
		foreach ($ff as $ffe) {
			if ($ffe["type"] != "string")
			bark($lang_takeupload['std_filename_errors']);
			$ffa[] = $ffe["value"];
		}
		if (!count($ffa))
		bark($lang_takeupload['std_filename_errors']);
		$ffe = implode("/", $ffa);
		$filelist[] = array($ffe, $ll);
	}
	$type = "multi";
}

$dict['value']['announce']=bdec(benc_str( get_protocol_prefix() . $announce_urls[0]));  // change announce url to local
$dict['value']['info']['value']['private']=bdec('i1e');  // add private tracker flag
//The following line requires uploader to re-download torrents after uploading
//even the torrent is set as private and with uploader's passkey in it.
$dict['value']['info']['value']['source']=bdec(benc_str( "[$BASEURL] $SITENAME"));
unset($dict['value']['announce-list']); // remove multi-tracker capability
unset($dict['value']['nodes']); // remove cached peers (Bitcomet & Azareus)
$dict=bdec(benc($dict)); // double up on the becoding solves the occassional misgenerated infohash
list($ann, $info) = dict_check($dict, "announce(string):info");

$infohash = pack("H*", sha1($info["string"]));

function hex_esc2($matches) {
	return sprintf("%02x", ord($matches[0]));
}

//die(phpinfo());

//die("magic:" . get_magic_quotes_gpc());

//die("\\' pos:" . strpos($infohash,"\\") . ", after sqlesc:" . (strpos(sqlesc($infohash),"\\") == false ? "gone" : strpos(sqlesc($infohash),"\\")));

//die(preg_replace_callback('/./s', "hex_esc2", $infohash));

// ------------- start: check upload authority ------------------//
$allowtorrents = user_can_upload("torrents");
$allowspecial = user_can_upload("music");

$catmod = get_single_value("categories","mode","WHERE id=".sqlesc($catid));
$offerid = $_POST['offer'];
$is_offer=false;
if ($browsecatmode != $specialcatmode && $catmod == $specialcatmode){//upload to special section
	//if (!$allowspecial)
		//bark($lang_takeupload['std_unauthorized_upload_freely']);
}
elseif($catmod == $browsecatmode){//upload to torrents section
 	if ($offerid){//it is a offer
		$allowed_offer_count = get_row_count("offers","WHERE allowed='allowed' AND userid=".sqlesc($CURUSER["id"]));
		if ($allowed_offer_count && $enableoffer == 'yes'){
				$allowed_offer = get_row_count("offers","WHERE id=".sqlesc($offerid)." AND allowed='allowed' AND userid=".sqlesc($CURUSER["id"]));
				if ($allowed_offer != 1)//user uploaded torrent that is not an allowed offer
					bark($lang_takeupload['std_uploaded_not_offered']);
				else $is_offer = true;
		}
		else bark($lang_takeupload['std_uploaded_not_offered']);
	}
	//elseif (!$allowtorrents)
	//	bark($lang_takeupload['std_unauthorized_upload_freely']);
}
else //upload to unknown section
	die("Upload to unknown section.");
// ------------- end: check upload authority ------------------//

// Replace punctuation characters with spaces

//$torrent = str_replace("_", " ", $torrent);

if ($largesize_torrent && $totallen > ($largesize_torrent * 1073741824)) //Large Torrent Promotion
{
	switch($largepro_torrent)
	{
		case 2: //Free
		{
			$sp_state = 2;
			$endfree='0000-00-00';
			break;
		}
		case 3: //2X
		{
			$sp_state = 3;
			$endfree='0000-00-00';
			break;
		}
		case 4: //2X Free
		{
			$sp_state = 4;
			$endfree='0000-00-00';
			break;
		}
		case 5: //Half Leech
		{
			$sp_state = 5;
			$endfree='0000-00-00';
			break;
		}
		case 6: //2X Half Leech
		{
			$sp_state = 6;
			$endfree='0000-00-00';
			break;
		}
		case 7: //30% Leech
		{
			$sp_state = 7;
			$endfree='0000-00-00';
			break;
		}
		default: //normal
		{
			$sp_state = 1;
			$endfree='0000-00-00';
			break;
		}
	}
}
else{ //ramdom torrent promotion
	$sp_id = mt_rand(1,100);
	if($sp_id <= ($probability = $randomtwoupfree_torrent)) //2X Free
	{
		$sp_state = 4;
		$endfree='0000-00-00';
	}
	elseif($sp_id <= ($probability += $randomtwoup_torrent)) //2X
	{
		$sp_state = 3;
		$endfree='0000-00-00';
	}
	elseif($sp_id <= ($probability += $randomfree_torrent)) //Free
	{
		$sp_state = 2;
		$endfree='0000-00-00';
	}
	elseif($sp_id <= ($probability += $randomhalfleech_torrent)) //Half Leech
	{
		$sp_state = 5;
		$endfree='0000-00-00';
	}
	elseif($sp_id <= ($probability += $randomtwouphalfdown_torrent)) //2X Half Leech
	{
		$sp_state = 6;
		$endfree='0000-00-00';
	}
	elseif($sp_id <= ($probability += $randomthirtypercentdown_torrent)) //30% Leech
	{
		$sp_state = 7;
		$endfree='0000-00-00';
	}
	else
		$sp_state = 1; //normal
}

if ($altname_main == 'yes'){
$cnname_part = unesc(trim($_POST["cnname"]));
$size_part = str_replace(" ", "", mksize($totallen));
$date_part = date("m.d.y");
$category_part = get_single_value("categories","name","WHERE id = ".sqlesc($catid));
$torrent = "【".$date_part."】".($_POST["name"] ? "[".$_POST["name"]."]" : "").($cnname_part ? "[".$cnname_part."]" : "");
}

// some ugly code of automatically promoting torrents based on some rules
if ($prorules_torrent == 'yes'){
foreach ($promotionrules_torrent as $rule)
{
	if (!array_key_exists('catid', $rule) || in_array($catid, $rule['catid']))
		if (!array_key_exists('sourceid', $rule) || in_array($sourceid, $rule['sourceid']))
			if (!array_key_exists('mediumid', $rule) || in_array($mediumid, $rule['mediumid']))
				if (!array_key_exists('codecid', $rule) || in_array($codecid, $rule['codecid']))
					if (!array_key_exists('standardid', $rule) || in_array($standardid, $rule['standardid']))
						if (!array_key_exists('processingid', $rule) || in_array($processingid, $rule['processingid']))
							if (!array_key_exists('teamid', $rule) || in_array($teamid, $rule['teamid']))
								if (!array_key_exists('audiocodecid', $rule) || in_array($audiocodecid, $rule['audiocodecid']))
									if (!array_key_exists('pattern', $rule) || preg_match($rule['pattern'], $torrent))
										if (is_numeric($rule['promotion'])){
											$sp_state = $rule['promotion'];
											break;
										}
}
}

$endsticky='0000-00-00';
$pos_state='normal';
$ismttv = sqlesc(unesc(RemoveXSS($_POST["ismttv"])));
$prohibit_reshipment = sqlesc(unesc(RemoveXSS($_POST["prohibit_reshipment"])));
if( $ismttv=="'yes'"){
	if (get_user_class() >= 11){ //退休及等级以上（暂时把等级硬编码）可发布时选择是否MTTV 2017.09.10 纳兰斯坦
		//MTTV组自动一级置顶24h免费24h
		// $nowtime = date("Y-m-d H:i:s", time())  ;
		$mttvendtime = date("Y-m-d H:i:s", time()+86400)  ;
		$sp_state=2;//免费
		$endfree=$mttvendtime;
		$pos_state='sticky1';//一级置顶
		$endsticky=$mttvendtime;
		//MTTV组自动添加种子描述
		$descr = "[quote][color=Red][font=Times New Roman][size=4]MTTV Exclusive，Don‘t upload it anywhere else!
[font=微软雅黑][b]MTTV组独占资源，禁止转载至其它网站！[/b][/size][/color][/font][/quote]

			" . $descr;
	}else{
		write_log("仿冒MTTV：用户 $anon 试图将 ($torrent) 设置为MTTV组,但他没有成功");
		stdmsg("您没有权限", "您的等级不能设置MTTV项为yes！");
		stdfoot();
		exit;
	}
}
if ($prohibit_reshipment != "'no_restrain'" && get_user_class() < 11){
  write_log("仿冒MTTV: 用户 $anon 试图为 ($torrent) 设置转载限制，被拒绝");
  stdmsg("您没有权限", "您的等级不能设置转载限制。");
  stdfoot();
  exit;
}

function check_prohibit_reshipment($value) {
    $prohibit_reshipments = array("'no_restrain'", "'prohibited'", "'cernet_only'");
    foreach ($prohibit_reshipments as $item)
        if ($value == $item) return true;
    return false;
}
if (get_user_class() >= 11){ //检查转载限制的值是否有效
    if (!check_prohibit_reshipment($prohibit_reshipment)) {
        stdmsg("参数错误", "转载限制值非法");
        stdfoot();
        exit;
	}
}

if ($catid == 411) { //2017.09.10,纳兰斯坦，学习区全部资源2xfree
	$sp_state = 4; //2xfree
	$endfree='0000-00-00';
}

//modified by SamuraiMe,2013.06.27
$status = get_user_class() < UC_POWER_USER ? "candidate" : "normal";
$visible = get_user_class() >= UC_POWER_USER ? "yes" : "no";
$ret = sql_query("INSERT INTO torrents (filename, owner, visible, anonymous, name, size, numfiles, type, dburl, url, small_descr, descr, ori_descr, category, source, medium, codec, audiocodec, standard, processing, team, save_as, sp_state, added, last_action, nfo, info_hash, last_status, status ,ismttv,endfree,endsticky,pos_state,prohibit_reshipment) VALUES (".sqlesc($fname).", ".sqlesc($CURUSER["id"]).", ". sqlesc($visible) .", ".sqlesc($anonymous).", ".sqlesc($torrent).", ".sqlesc($totallen).", ".count($filelist).", ".sqlesc($type).", ".sqlesc($dburl).", ".sqlesc($url).", ".sqlesc($small_descr).", ".sqlesc($descr).", ".sqlesc($descr).", ".sqlesc($catid).", ".sqlesc($sourceid).", ".sqlesc($mediumid).", ".sqlesc($codecid).", ".sqlesc($audiocodecid).", ".sqlesc($standardid).", ".sqlesc($processingid).", ".sqlesc($teamid).", ".sqlesc($dname).", ".sqlesc($sp_state) .", " . sqlesc(date("Y-m-d H:i:s")) . ", " . sqlesc(date("Y-m-d H:i:s")) . ", ".sqlesc($nfo).", " . sqlesc($infohash).", " . sqlesc(date("Y-m-d H:i:s")). ", '".$status."',".$ismttv.",".sqlesc($endfree).",".sqlesc($endsticky).",".sqlesc($pos_state).",".$prohibit_reshipment.")");
if (!$ret) {
	if (mysql_errno() == 1062)
	bark($lang_takeupload['std_torrent_existed']);
	bark("mysql puked: ".mysql_error());
	//bark("mysql puked: ".preg_replace_callback('/./s', "hex_esc2", mysql_error()));
}
$id = mysql_insert_id();
//获取种子文件列表写入files表
@sql_query("DELETE FROM files WHERE torrent = $id");
foreach ($filelist as $file) {
	@sql_query("INSERT INTO files (torrent, filename, size) VALUES ($id, ".sqlesc($file[0]).",".$file[1].")");
}

//move_uploaded_file($tmpname, "$torrent_dir/$id.torrent");
$fp = fopen("$torrent_dir/$id.torrent", "w");
if ($fp)
{
	@fwrite($fp, benc($dict), strlen(benc($dict)));
	fclose($fp);
}

//===add karma
KPS("+",$uploadtorrent_bonus,$CURUSER["id"]);
//===end
$mttvinfo="";
if( $ismttv=="'yes'"){
	if (get_user_class() >= 11){ //退休及等级以上（暂时把等级硬编码）可发布时选择是否MTTV 2017.09.10 纳兰斯坦
		//MTTV组自动置顶24h免费24h并发布到群聊区
		$mttvendtime = date("Y-m-d H:i:s", time()+86400)  ;
		$shoutbox = "设置为置顶+免费，至".$mttvendtime;
		sendshoutbox("系统自动将MTTV组资源 [url=details.php?id=$id] $torrent [/url]" . $shoutbox." ~快去看看吧看看吧o(￣︶￣)o");
		$mttvinfo="MTTV组资源";
	}else{
	}
}
if (($catid == 411) && (get_user_class() >= UC_POWER_USER)) { //2017.09.10,纳兰斯坦，学习区全部资源2xfree
	sendshoutbox("系统自动将学习资源 [url=details.php?id=$id] $torrent [/url] 设置为2xfree ~快去看看吧看看吧o(￣︶￣)o");
}
$candidatelog = get_user_class() < UC_POWER_USER ? ",由于等级不足，种子被列为候选" : "";

write_log("发布：用户 $anon 上传了 种子$candidatelog $id ($torrent) $mttvinfo");


//===notify people who voted on offer thanks CoLdFuSiOn :)
if ($is_offer)
{
	$res = sql_query("SELECT `userid` FROM `offervotes` WHERE `userid` != " . $CURUSER["id"] . " AND `offerid` = ". sqlesc($offerid)." AND `vote` = 'yeah'") or sqlerr(__FILE__, __LINE__);

	while($row = mysql_fetch_assoc($res)) 
	{
		$pn_msg = $lang_takeupload_target[get_user_lang($row["userid"])]['msg_offer_you_voted'].$torrent.$lang_takeupload_target[get_user_lang($row["userid"])]['msg_was_uploaded_by']. $CURUSER["username"] .$lang_takeupload_target[get_user_lang($row["userid"])]['msg_you_can_download'] ."[url=" . get_protocol_prefix() . "$BASEURL/details.php?id=$id&hit=1]".$lang_takeupload_target[get_user_lang($row["userid"])]['msg_here']."[/url]";
		
		//=== use this if you DO have subject in your PMs
		$subject = $lang_takeupload_target[get_user_lang($row["userid"])]['msg_offer'].$torrent.$lang_takeupload_target[get_user_lang($row["userid"])]['msg_was_just_uploaded'];
		//=== use this if you DO NOT have subject in your PMs
		//$some_variable .= "(0, $row[userid], '" . date("Y-m-d H:i:s") . "', " . sqlesc($pn_msg) . ")";

		//=== use this if you DO have subject in your PMs
		sql_query("INSERT INTO messages (sender, subject, receiver, added, msg) VALUES (0, ".sqlesc($subject).", $row[userid], ".sqlesc(date("Y-m-d H:i:s")).", " . sqlesc($pn_msg) . ")") or sqlerr(__FILE__, __LINE__);
		//=== use this if you do NOT have subject in your PMs
		//sql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES ".$some_variable."") or sqlerr(__FILE__, __LINE__);
		//===end
	}
	//=== delete all offer stuff
	sql_query("DELETE FROM offers WHERE id = ". $offerid);
	sql_query("DELETE FROM offervotes WHERE offerid = ". $offerid);
	sql_query("DELETE FROM comments WHERE offer = ". $offerid);
	if($CURUSER['class'] == 1) sql_query("UPDATE users SET class=2 WHERE id = ". $CURUSER['id']);
}
//=== end notify people who voted on offer

/* Email notifs */
if ($emailnotify_smtp=='yes' && $smtptype != 'none')
{
$cat = get_single_value("categories","name","WHERE id=".sqlesc($catid));
$res = sql_query("SELECT id, email, lang FROM users WHERE enabled='yes' AND parked='no' AND status='confirmed' AND notifs LIKE '%[cat$catid]%' AND notifs LIKE '%[email]%' ORDER BY lang ASC") or sqlerr(__FILE__, __LINE__);

$uploader = $anon;

$size = mksize($totallen);

$description = format_comment($descr);

//dirty code, change later

$langfolder_array = array("en", "chs", "cht", "ko", "ja");
$body_arr = array("en" => "", "chs" => "", "cht" => "", "ko" => "", "ja" => "");
$i = 0;
foreach($body_arr as $body)
{
$body_arr[$langfolder_array[$i]] = <<<EOD
{$lang_takeupload_target[$langfolder_array[$i]]['mail_hi']}

{$lang_takeupload_target[$langfolder_array[$i]]['mail_new_torrent']}

{$lang_takeupload_target[$langfolder_array[$i]]['mail_torrent_name']}$torrent
{$lang_takeupload_target[$langfolder_array[$i]]['mail_torrent_size']}$size
{$lang_takeupload_target[$langfolder_array[$i]]['mail_torrent_category']}$cat
{$lang_takeupload_target[$langfolder_array[$i]]['mail_torrent_uppedby']}$uploader

{$lang_takeupload_target[$langfolder_array[$i]]['mail_torrent_description']}
-------------------------------------------------------------------------------------------------------------------------
$description
-------------------------------------------------------------------------------------------------------------------------

{$lang_takeupload_target[$langfolder_array[$i]]['mail_torrent']}<b><a href="javascript:void(null)" onclick="window.open('http://$BASEURL/details.php?id=$id&hit=1')">{$lang_takeupload_target[$langfolder_array[$i]]['mail_here']}</a></b><br />
http://$BASEURL/details.php?id=$id&hit=1

------{$lang_takeupload_target[$langfolder_array[$i]]['mail_yours']}
{$lang_takeupload_target[$langfolder_array[$i]]['mail_team']}
EOD;

$body_arr[$langfolder_array[$i]] = str_replace("<br />","<br />",nl2br($body_arr[$langfolder_array[$i]]));
	$i++;
}

while($arr = mysql_fetch_array($res))
{
		$current_lang = $arr["lang"];
		$to = $arr["email"];

		sent_mail($to,$SITENAME,$SITEEMAIL,change_email_encode(validlang($current_lang),$lang_takeupload_target[validlang($current_lang)]['mail_title'].$torrent),change_email_encode(validlang($current_lang),$body_arr[validlang($current_lang)]),"torrent upload",false,false,'',get_email_encode(validlang($current_lang)), "eYou");
}
}

header("Location: " . get_protocol_prefix() . "$BASEURL/details.php?id=".htmlspecialchars($id)."&uploaded=1");
?>
