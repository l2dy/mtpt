<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();

function bark($msg) {
	global $lang_takeedit;
	genbark($msg, $lang_takeedit['std_edit_failed']);
}

if (!mkglobal("id:name:descr:type")){
	global $lang_takeedit;
	bark($lang_takeedit['std_missing_form_data']);
}

$id = 0 + $id;
if (!$id)
	die("没有这个id的种子");
$sourceid = (int)$_POST['source_sel'];
if ($sourceid == 0)
bark($lang_takeedit['std_missing_lid']);

$res = sql_query("SELECT ismttv,status, category, owner, filename, save_as, anonymous, picktype, picktime, sp_state, pos_state, added FROM torrents WHERE id = ".mysql_real_escape_string($id)) or sqlerr(__FILE__,__LINE__);
$row = mysql_fetch_array($res);
$torrentAddedTimeString = $row['added'];
if (!$row)
	die("没有这个id的种子");

if ($CURUSER["id"] != $row["owner"] && get_user_class() < $torrentmanage_class)
	bark($lang_takeedit['std_not_owner']);
$oldcatmode = get_single_value("categories","mode","WHERE id=".sqlesc($row['category']));

$updateset = array();

//$fname = $row["filename"];
//preg_match('/^(.+)\.torrent$/si', $fname, $matches);
//$shortfname = $matches[1];
//$dname = $row["save_as"];

$url = parse_imdb_id($_POST['imdburl']);
$dburl = parse_douban_id($_POST['dburl']);

if ($enablenfo_main=='yes'){
$nfoaction = $_POST['nfoaction'];
if ($nfoaction == "update")
{
	$nfofile = $_FILES['nfo'];
	if (!$nfofile) die("No data " . var_dump($_FILES));
	if ($nfofile['size'] > 65535)
		bark($lang_takeedit['std_nfo_too_big']);
	$nfofilename = $nfofile['tmp_name'];
	if (@is_uploaded_file($nfofilename) && @filesize($nfofilename) > 0)
		$updateset[] = "nfo = " . sqlesc(str_replace("\x0d\x0d\x0a", "\x0d\x0a", file_get_contents($nfofilename)));
	$Cache->delete_value('nfo_block_torrent_id_'.$id);
}
elseif ($nfoaction == "remove"){
	$updateset[] = "nfo = ''";
	$Cache->delete_value('nfo_block_torrent_id_'.$id);
}
}

$catid = (0 + $type);
if (!is_valid_id($catid))
bark($lang_takeedit['std_missing_form_data']);
if (!$name || !$descr)
bark($lang_takeedit['std_missing_form_data']);
$newcatmode = get_single_value("categories","mode","WHERE id=".sqlesc($catid));
if ($enablespecial == 'yes' && get_user_class() >= $movetorrent_class)
	$allowmove = true; //enable moving torrent to other section
else $allowmove = false;
if ($oldcatmode != $newcatmode && !$allowmove)
	bark($lang_takeedit['std_cannot_move_torrent']);
$updateset[] = "anonymous = '" . ($_POST["anonymous"] ? "yes" : "no") . "'";
$updateset[] = "name = " . sqlesc($name);
$updateset[] = "descr = " . sqlesc($descr);
$updateset[] = "url = " . sqlesc($url);
$updateset[] = "dburl = " . sqlesc($dburl);
$updateset[] = "small_descr = " . sqlesc($_POST["small_descr"]);
//$updateset[] = "ori_descr = " . sqlesc($descr);
$updateset[] = "category = " . sqlesc($catid);
$updateset[] = "source = " . sqlesc(0 + $_POST["source_sel"]);
$updateset[] = "medium = " . sqlesc(0 + $_POST["medium_sel"]);
$updateset[] = "codec = " . sqlesc(0 + $_POST["codec_sel"]);
$updateset[] = "standard = " . sqlesc(0 + $_POST["standard_sel"]);
$updateset[] = "processing = " . sqlesc(0 + $_POST["processing_sel"]);
$updateset[] = "team = " . sqlesc(0 + $_POST["team_sel"]);
$updateset[] = "audiocodec = " . sqlesc(0 + $_POST["audiocodec_sel"]);

$pick_info = "";



if (get_user_class() >= $torrentmanage_class) {
	//这就是编辑并发布的功能...
	//added by SamuraiMe,2013.06.12
	if ($_REQUEST["release"] == "yes") {
		$updateset[] = "banned = 'no'";
		$updateset[] = "status = 'normal'";
		$updateset[] = "visible= 'yes'";
		$updateset[] = "added = ".sqlesc(date("Y-m-d H:i:s"));
		$edited = 2;
	} else	if ($_POST["banned"]) {
		$updateset[] = "banned = 'yes'";
		$_POST["visible"] = 0;
		$pick_info .= "，设置为禁止。";
		$notshout =1;
	}
	else
		$updateset[] = "banned = 'no'";
}
$updateset[] = "visible = '" . ($_POST["visible"] ? "yes" : "no") . "'";
//设置促销
$place_info = "";
if(get_user_class()>=$torrentonpromotion_class && $row['sp_state'] != $_POST['sel_spstate'])
{
	if(!isset($_POST["sel_spstate"]) ){
		$updateset[] = "sp_state = 1";}
	if($_POST["sel_spstate"] == 1){
		$updateset[] = "sp_state = 1";
		$place_info .= "取消促销。";$notshout =1;}
	elseif((0 + $_POST["sel_spstate"]) == 2){
		$updateset[] = "sp_state = 2";
		$place_info .= "设置为免费";}
	elseif((0 + $_POST["sel_spstate"]) == 3){
		$updateset[] = "sp_state = 3";
		$place_info .= "设置为2X";}
	elseif((0 + $_POST["sel_spstate"]) == 4){
		$updateset[] = "sp_state = 4";
		$place_info .= "设置为2X免费";}
	elseif((0 + $_POST["sel_spstate"]) == 5){
		$updateset[] = "sp_state = 5";
		$place_info .= "设置为50%";}
	elseif((0 + $_POST["sel_spstate"]) == 6){
		$updateset[] = "sp_state = 6";
		$place_info .= "设置为2X50%";}
	elseif((0 + $_POST["sel_spstate"]) == 7){
		$updateset[] = "sp_state = 7";
		$place_info .= "设置为30%";}
}
//设置免费时长
	if(isset($_POST['freetime']) && (0 + $_POST['freetime']) < 0)
	{
		$updateset[] = "endfree = '0000-00-00'";
	}elseif(isset($_POST['freetime']) && $_POST['freetime'] <> ""){
		$freetime = 0 + $_POST['freetime'];
		$freetimeh = 0 + $_POST['freetimeh'];
		$updateset[] = "endfree = '".date("Y-m-d H:i:s",time() + 86400 * $freetime + 3600 * $freetimeh)."'";
		$place_info .= "， 至".date("Y-m-d H:i:s",time() + 86400 * $freetime + 3600 * $freetimeh)."。";
	}
	
//设置置顶
if(get_user_class()>=$torrentsticky_class)
{	
	if((0 + $_POST["sel_posstate"]) == 0 && $row['pos_state'] != 'normal'){
		$updateset[] = "pos_state = 'normal'";
		$place_info .= "取消置顶";
		$notshout =1;}
	elseif((0 + $_POST["sel_posstate"]) == 1 && $row['pos_state'] != 'sticky1'){
		$updateset[] = "pos_state = 'sticky1'";
		$place_info .= "设置为一级置顶";
    }elseif((0 + $_POST["sel_posstate"]) == 2 && $row['pos_state'] != 'sticky'){
        $updateset[] = "pos_state = 'sticky'";
        $place_info .= "设置为二级置顶";
    }
}
	
	//设置置顶时长
	if(!isset($_POST['stickytime']) && get_user_class()>=$torrentsticky_class && (((0 + $_POST["sel_posstate"]) == 1 && $row['pos_state'] != 'sticky1') || ((0 + $_POST["sel_posstate"]) == 2 && $row['pos_state'] != 'sticky')))
	{
	$place_info .= "，没有填写置顶时间23333。";
	}
	elseif(isset($_POST['stickytime']) && (0 + $_POST['stickytime']) < 0)
	{
		$updateset[] = "endsticky = '0000-00-00'";
		$place_info .= " 至 天荒地老";

	}
	elseif(isset($_POST['stickytime']) && $_POST['stickytime'] <> ""){
		$stickytime = 0 + $_POST['stickytime'];
		$stickytimeh = 0 + $_POST['stickytimeh'];
		if ( $stickytime >7)$stickytime=5;
		$updateset[] = "endsticky = '".date("Y-m-d H:i:s",time() + 86400 * $stickytime + 3600 * $stickytimeh)."'";
		$place_info .= "， 至".date("Y-m-d H:i:s",time() + 86400 * $stickytime + 3600 * $stickytimeh)."。";
		// if ($CURUSER['id']==35187) {header("Refresh: 0; url=torrents.php");die;}//历史问题，2017.09.10发现并注释
	}
	//设置是否MTTV组,2017.09.10 纳兰斯坦
	if(get_user_class()>=$torrentsticky_class && $row['ismttv'] != $_POST['ismttv']){
		$ismttv = sqlesc(unesc(RemoveXSS($_POST["ismttv"])));
		if ($ismttv == "'yes'"){
			$updateset[] = "ismttv = 'yes'";
			$pick_info .= ", 设置为MTTV组。";$notshout =1;
		}elseif ($ismttv == "'no'"){
			$updateset[] = "ismttv = 'no'";
			$pick_info .= ", 取消MTTV组。";$notshout =1;
		}
    }
    if (get_user_class()>=$torrentsticky_class && $row['prohibit_reshipment'] != $_POST['prohibit_reshipment']){
      $prohibit_reshipment = sqlesc(unesc(RemoveXSS($_POST['prohibit_reshipment'])));
      $matched = true;
      switch ($prohibit_reshipment) {
      case "'prohibited'":
        $pick_info .= ", 设置为禁止转载。";
        break;
        case "'cernet_only'":
          $pick_info .= ", 设置为仅限教育网转载。";
          break;
        case "'no_restrain'":
          $pick_info .= ", 取消转载限制。";
          break;
        default:
          $matched = false;
          break;
      }
      if ($matched){
        $updateset[] = "prohibit_reshipment = $prohibit_reshipment";
        $notshout = 1;
      }
    }

	//promotion expiration type
	if(!isset($_POST["promotion_time_type"]) || $_POST["promotion_time_type"] < 0) {
		$updateset[] = "promotion_time_type = 0";
		$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
	} elseif ($_POST["promotion_time_type"] == 1) {
		$updateset[] = "promotion_time_type = 1";
		$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
	} elseif ($_POST["promotion_time_type"] == 2) {
		if ($_POST["promotionuntil"] && strtotime($torrentAddedTimeString) <= strtotime($_POST["promotionuntil"])) {
			$updateset[] = "promotion_time_type = 2";
			$updateset[] = "promotion_until = ".sqlesc($_POST["promotionuntil"]);
		} else {
			$updateset[] = "promotion_time_type = 0";
			$updateset[] = "promotion_until = '0000-00-00 00:00:00'";
		}
	}

if((get_user_class()>=$torrentmanage_class && $CURUSER['picker'] == 'yes')||get_user_class()>=15)
{
	if((0 + $_POST["sel_recmovie"]) == 0 && $row["picktype"] != 'normal')
	{
		$pick_info = ", 取消推荐。";
		$updateset[] = "picktype = 'normal'";
		$updateset[] = "picktime = '0000-00-00 00:00:00'";
		$notshout =1;
	}
	elseif((0 + $_POST["sel_recmovie"]) == 1 && $row["picktype"] != 'hot')
	{
		$pick_info = ", 推荐为  热门。";
		$updateset[] = "picktype = 'hot'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	elseif((0 + $_POST["sel_recmovie"]) == 2 && $row["picktype"] != 'classic')
	{
		$pick_info = ", 推荐为  经典。";
		$updateset[] = "picktype = 'classic'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
	elseif((0 + $_POST["sel_recmovie"]) == 3 && $row["picktype"] != 'recommended')
	{
		$pick_info = ", 推荐为  推荐。";
		$updateset[] = "picktype = 'recommended'";
		$updateset[] = "picktime = ". sqlesc(date("Y-m-d H:i:s"));
	}
}
sql_query("UPDATE torrents SET " . join(",", $updateset) . " WHERE id = $id") or sqlerr(__FILE__, __LINE__);

$beforeStatus = getTorrentStatus($row['status']);
if ($_REQUEST["release"] == "yes") 
{
	write_log("种子：管理员 $CURUSER[username] 编辑并发布了 $beforeStatus 种子 $id ($name) " . $pick_info . $place_info);
	sendMessage(0,$row['owner'],"你的$beforeStatus 种子被管理员编辑了","管理员[url=userdetails.php?id=$CURUSER[id]] $CURUSER[username] [/url] 编辑并发布了 $beforeStatus 种子 [url=details.php?id=$id] ($name)[/url]" . $pick_info . $place_info."");
} 
else if($CURUSER["id"] == $row["owner"]) 
{
	if ($row["anonymous"]=='yes')
	{
		write_log("种子：发布者 匿名用户 编辑了 $beforeStatus 种子 $id ($name) " . $pick_info . $place_info);
	}
	else
	{
		write_log("种子：发布者 $CURUSER[username] 编辑了 $beforeStatus 种子 $id ($name) " . $pick_info . $place_info);
	}
}
else
{
	write_log("种子：管理员 $CURUSER[username] 编辑了 $beforeStatus 种子 $id ($name) " . $pick_info . $place_info);
	sendMessage(0,$row['owner'],"你发布的$beforeStatus 种子被管理员编辑了","管理员[url=userdetails.php?id=$CURUSER[id]] $CURUSER[username] [/url] 编辑了种子 [url=details.php?id=$id] ($name)[/url]" . $pick_info . $place_info."");
}
$shoutbox = $pick_info . $place_info;
if ($shoutbox && !$notshout)
sendshoutbox("管理员将 [url=details.php?id=$id] $name [/url]" . $shoutbox." ~快去看看吧看看吧o(￣︶￣)o");
$edited = $edited ? 2 : 1;
$returl = "details.php?id=$id&edited=$edited";
if (isset($_POST["returnto"]))
	$returl = $_POST["returnto"];
header("Refresh: 0; url=$returl");
