<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
require_once(get_langfile_path("",true));
loggedinorreturn();

function bark($msg) {
  global $lang_delete;
  stdhead();
  stdmsg($lang_delete['std_delete_failed'], $msg);
  stdfoot();
  exit;
}

/*
if (!mkglobal("id"))
	bark($lang_delete['std_missing_form_date']);
*/

$torrentids = is_array($_REQUEST["id"]) ? $_REQUEST["id"] : array($_REQUEST["id"]);
$recycleMode = $_REQUEST["recycle_mode"];
switch ($recycleMode) {
	case 'release':
		foreach ($torrentids as $id) {
			$res = sql_query("SELECT name, status, owner FROM torrents WHERE id = ".sqlesc($id));
			$row = mysql_fetch_array($res);
			sql_query("UPDATE torrents SET banned = 'no', status = 'normal', added = ".sqlesc(date("Y-m-d H:i:s"))." WHERE id = $id") or sqlerr(__FILE__,__LINE__);
			$beforeStatus = getTorrentStatus($row['status']);	
			write_log("种子：管理员 $CURUSER[username] 发布了 $beforeStatus 种子 $id ($row[name]) ");
			sendMessage(0,$row['owner'],"你的$beforeStatus 种子被管理员发布了","管理员[url=userdetails.php?id=$CURUSER[id]] $CURUSER[username] [/url] 发布了 $beforeStatus 种子 [url=details.php?id=$id] ($row[name])[/url]");
		}
		break;

	case 'delete':
	case 'recycle':
		$rt = 0 + $_POST["reasontype"];

		if (!is_int($rt) || $rt < 1 || $rt > 5)
			bark($lang_delete['std_invalid_reason']."$rt.");

		$r = $_POST["r"];
		$reason = $_POST["reason"];

		if ($rt == 1)
			$reasonstr = "断种: 0 seeders, 0 leechers = 0 peers total";
		elseif ($rt == 2)
			$reasonstr = "重复" . ($reason[0] ? (": " . trim($reason[0])) : "!");
		elseif ($rt == 3)
			$reasonstr = "劣质" . ($reason[1] ? (": " . trim($reason[1])) : "!");
		elseif ($rt == 4)
		{
			if (!$reason[2])
				bark($lang_delete['std_describe_violated_rule']);
		  $reasonstr = $SITENAME." rules broken: " . trim($reason[2]);
		}
		else
		{
			if (!$reason[3])
				bark($lang_delete['std_enter_reason']);
		  $reasonstr = trim($reason[3]);
		}


		if ($recycleMode == "delete") {
			$delMsg = "删除了";
		} else {
			$delMsg = "移入回收站";
			$reasonstr .= "\n 请按照管理员要求修改种子，修改合格之后在15日内联系管理员移出回收站，否则将被系统自动清理。";
		}
		
		foreach ($torrentids as $id) {
			$id = 0 + $id;
			if (!$id)
				die();
			$res = sql_query("SELECT name,owner,seeders,anonymous,status FROM torrents WHERE id = ".sqlesc($id)) or sqlerr();
			$row = mysql_fetch_array($res);
			if (!$row)
				die();

			if ($CURUSER["id"] != $row["owner"] && get_user_class() < $torrentmanage_class)
				bark($lang_delete['std_not_owner']);

			// added by SamuraiMe,2013.05.17
			sendDelMsg($id, $reasonstr, $recycleMode);
			// added by SamuraiMe,2013.05.17

			$timestamp = sqlesc(date("Y-m-d H:i:s"));
			if ($recycleMode == "delete") {
				deletetorrent($id);
			} else if ($recycleMode == "recycle") {
				sql_query("UPDATE torrents SET banned = 'yes', visible = '0' ,status = 'recycle', last_status = $timestamp,added =$timestamp WHERE id = ".sqlesc($id)) or sqlerr(__FILE__,__LINE__);
				sql_query("Insert comments(user,torrent,added,text) values(".$CURUSER['id'].",$id,now(),".sqlesc("你好我把你的种子移入了回收站，有问题联系我[em12]\n 原因是：".$reasonstr).")")or sqlerr(__FILE__, __LINE__);
			}

			$beforeStatus = getTorrentStatus($row['status']);
			if ($CURUSER["id"] == $row["owner"])
			{
				if ($row['anonymous'] == 'yes' ) {
					write_log("种子：发布者 匿名 $delMsg $beforeStatus 种子 $id ($row[name]) 。原因是 ：($reasonstr)",'normal');
				} else {
					write_log("种子：发布者 $CURUSER[username] $delMsg $beforeStatus 种子 $id ($row[name])。原因是： ($reasonstr)",'normal');
				}
			}
			else write_log("种子：管理员 $CURUSER[username] $delMsg $beforeStatus 种子 $id ($row[name])。原因是： ($reasonstr)",'normal');

			//===remove karma
			KPS("-",$uploadtorrent_bonus,$row["owner"]);

		}
		break;

	default:
		# code...
		break;
}

if ($recycleMode == "delete") {
	$title = "删除种子成功";
} else if ($recycleMode == "recycle") {
	$title = "成功移入回收站";
} else if ($recycleMode == "release") {
	$title = "种子发布成功";
}

stdhead($title);

if (isset($_POST["returnto"]))
	$ret = "<a href=\"" . htmlspecialchars($_POST["returnto"]) . "\">".$lang_delete['text_go_back']."</a>";
else
	$ret = "<a href=\"index.php\">如果没有提示删除成功那就是失败了。删除失败请更换浏览器试试，推荐chrome。或者联系管理员使用种子列表的删除功能进行删除。".$lang_delete['text_back_to_index']."</a>";
?>
<h1><?php echo $title ?></h1>
<p><?php echo  $ret ?></p>
<?php
stdfoot();