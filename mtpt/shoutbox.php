<?php
require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
require "./memcache.php";
if (isset($_GET['checknew']))
{
	//file_put_contents("testshout/".$CURUSER["username"],date("H:i:s",time()));
	echo file_get_contents("shoutbox_new.html");
	die;
}
if (isset($_GET['del']))
{
	if (is_valid_id($_GET['del']))
	{
		if((get_user_class() >= $sbmanage_class))
		{
			sql_query("DELETE FROM shoutbox WHERE id=".mysql_real_escape_string($_GET['del']));
			write_log($CURUSER['username'] ."删掉了一条群聊发言",'normal');
		}
	}
}
$where=$_GET["type"];
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="<?php echo get_font_css_uri()?>" type="text/css">
<link rel="stylesheet" href="<?php echo get_css_uri()."theme.css"?>" type="text/css">
<link rel="stylesheet" href="styles/curtain_imageresizer.css" type="text/css">
<script src="curtain_imageresizer.js" type="text/javascript"></script><style type="text/css">body {overflow-y:scroll; overflow-x: hidden}</style>
<?php
print(get_style_addicode());
?>
<script type="text/javascript">
//<![CDATA[
var t;
function countdown(time)
{
	if (time <= 0){
	parent.document.getElementById("hbtext").disabled=false;
	parent.document.getElementById("hbsubmit").disabled=false;
	parent.document.getElementById("hbsubmit").value=parent.document.getElementById("sbword").innerHTML;
	}
	else {
	parent.document.getElementById("hbsubmit").value=time;
	time=time-1;
	setTimeout("countdown("+time+")", 1000);
	}
}
function hbquota(){
parent.document.getElementById("hbtext").disabled=true;
parent.document.getElementById("hbsubmit").disabled=true;
var time=10;
countdown(time);
//]]>
}

</script>
</head>
<body class='inframe' <?php if ($_GET["type"] != "helpbox"){?> onload="<?php echo $startcountdown?>" <?php } else {?> onload="hbquota()" <?php } ?>>
<?php
if($_GET["sent"]=="yes"){
//php内再次判断发言间隔
$date = date("H:i:s");
$limittime = 4;
if($memcache->get('app_shoutbox_'.$CURUSER['id'])!='') {
$lasttime = $memcache->get('app_shoutbox_'.$CURUSER['id']);
echo("<script type=\"text/javascript\"> alert(\"你在$limittime 秒内($lasttime)刚发过言，休息一会吧.此提示框会出现到时间到了为止，然后大概会把你刚才的发言发出去，抱歉╮(╯▽╰)╭\");location.reload(); </script>");die;}
$memcache->set('app_shoutbox_'.$CURUSER['id'],$date,false,$limittime) or die ("请向管理员报告此错误");

if(!$_GET["shbox_text"])
{
	$userid=0+$CURUSER["id"];
}
else
{
	$text=trim($_GET["shbox_text"]);
	if($_GET["type"]=="helpbox")
	{
		if ($showhelpbox_main != 'yes'){
			write_log("Someone is hacking shoutbox. - IP : ".getip(),'mod');
			die($lang_shoutbox['text_helpbox_disabled']);
		}
		$userid=0;
		$type='hb';
	}
	elseif ($_GET["type"] == 'shoutbox')
	{
		$userid=0+$CURUSER["id"];
		if (!$userid){
			write_log("Someone is hacking shoutbox. - IP : ".getip(),'mod');
			die($lang_shoutbox['text_no_permission_to_shoutbox']);
		}
		if ($_GET["toguest"]){
			$type ='hb';
		}else{
			if(strpos($text,"@游客") > 0)
			$type = 'hb';
			else
			$type = 'sb';
		}
	}
	$date=sqlesc(time());

	sql_query("INSERT INTO shoutbox (userid, date, text, type, ip) VALUES (" . sqlesc($userid) . ", $date, " . sqlesc(RemoveXSS($text)) . ", ".sqlesc($type).", ".sqlesc(getip()).")") or sqlerr(__FILE__, __LINE__);

	file_put_contents("shoutbox_new.html",mysql_insert_id());
	if ($memcache->get('robotname') == ''){
	$robotname = sql_query("SELECT username from users where id=11") or sqlerr(__FILE__,__LINE__);
	$robotname = mysql_fetch_array($robotname);
	$memcache->set('robotname',$robotname[0],false,3600*24*7);
	}
	else
	$robot = $memcache->get('robotname');
	if (!$memcache->get('app_shoutbox_shoutup'))
	{
		if(preg_match( "/\[\@$robot\](.*?)(开奖|中奖|彩票)/",$text))
		sendshoutbox("[@$CURUSER[username]]：最近一期的彩票是第".($memcache->get('drawid'))."期，中奖号码。。忘记了。。。[url=/lottery.php?action=drawlog]here，here~[/url]","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(不|别|没|无|非)/",$text))
		//sendshoutbox("[@$CURUSER[username]]：我不认识否定词哎，不明白你说的啥意思，不过我的意思是你说的话的意思可能不是本来的意思。要是一直没人喂我的话我就要自己去偷麦粒了(。·`ω´·)[url=steal.php]你偷过麦粒么[/url] ","","",$date+5);
		;

		elseif(preg_match( "/\[\@$robot\](.*?)(请问|？|\?|吗|怎么|什么)/",$text))
		sendshoutbox("[@$CURUSER[username]]：不要在群聊问我问题啦ㄟ( ▔。 ▔ )ㄏ我在[url=autofaq/]这里[/url]负责回答各种问题，目前还在调教中＾（·w·）＾ ","","",$date+5);

		elseif(preg_match( "/\[\@$robot\](.*?)(闭嘴|烦人|讨厌|安静)/",$text)){
		sendshoutbox("[@$CURUSER[username]]：好吧惹人厌了 ( >﹏<。)那我就闭嘴五分钟（/TДT)/我还会回来的 ","","",$date+5);
		$memcache->set('app_shoutbox_shoutup','1',false,300) or die ("请向管理员报告此错误");
		}
		elseif(preg_match( "/\[\@$robot\](.*?)((我(爱|喜欢|想)你)|么么哒)/",$text,$matches))
		sendshoutbox("[@$CURUSER[username]]：我爱你o(*￣︶￣*)o么么哒 ","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(你好|hello|HELLO|HI|hi)/",$text))
		sendshoutbox("[@$CURUSER[username]]：大家好才是真的好o(￣ε￣*)多逛论坛多开μt保种哦亲(￣︶￣)↗ ","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(早上好|早安|晚安|睡|困)/",$text))
		sendshoutbox("[@$CURUSER[username]]：早睡早起，熬夜对身体不好、我要努力升级~我可是要当站长的机器人<(￣ˇ￣)/  ","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(等级|机器人|麦粒|快长大)/",$text))
		sendshoutbox("[@$CURUSER[username]]：送麦粒给我吧，我的麦粒全被吃了。我吃饱了就会慢慢长大自动升级的哦(￣ˇ￣)v我可是要当站长的机器人~~~要是一直没人喂我的话我就要自己去偷麦粒了(。·`ω´·)[url=steal.php]你偷过麦粒么[/url]  ","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(哟哟|切克闹|炫|唱(.*?)歌|跳(.*?)舞|玩)/",$text))
		sendshoutbox("[@$CURUSER[username]]：哟哟切克闹，煎饼果子来一套╰(*°▽°*)╯ 南校的煎饼果子没有北校的好吃ヽ(。ゝω·。)丿我说yes你说no~~~~m(￢0￢)m~~~~yes~~	 ","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(no)/",$text))
		sendshoutbox("[@$CURUSER[username]]：yes","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(yes)/",$text))
		sendshoutbox("[@$CURUSER[username]]：你说no你说no（￣ c￣）","","",$date+5);


		elseif(preg_match( "/\[\@$robot\](.*?)(滚|坏|坑|贱|sb|傻|狗|猪|bad|fuck|操|艹|靠|我日|痴|2b|2B|垃圾)/",$text))
		sendshoutbox("[@$CURUSER[username]]：不要这样说好不好，我看不懂啦(′д｀ )  ","","",$date+5);
		elseif(preg_match( "/\[\@$robot\](.*?)(好|棒|牛|厉害|爱|帅|美|nb|nice|good|开心|喜|欢|mua)/",$text))
		sendshoutbox("[@$CURUSER[username]]：你说的话我还看不懂啦，不过好像是在夸我呢︿(*￣︶￣*)︿ 你不嫌烦开心就好啦","","",$date+5);


		elseif(preg_match( "/\[\@$robot\](.*?)(教|学|智商|笨)/",$text))
		sendshoutbox("[@$CURUSER[username]]：我还小着呢，根本就没有智商，全靠搜索关键词回复的说(¬､¬) 不要试图教我啦(´·ω·`) 送点麦粒给我吃，长大了说不定就能看懂了(ˉ▽ˉ；) ","","",$date+5);

		//elseif(preg_match( "/\[\@$robot\](.*?)(名字|叫什么)/",$text))
		//sendshoutbox("[@$CURUSER[username]]：大家给我起了好多名字啊，我叫哪个好呢╰(￣ω￣ｏ) 到下面投个票吧~ ","","",$date+5);



		//elseif(preg_match( "/\[\@$robot\](.*?)/",$text))
		//sendshoutbox("[@$CURUSER[username]]：对不起我还不能看懂你说话~\(≧▽≦)/~啦啦啦~送点麦粒给我吃，长大了说不定就能看懂了(ˉ▽ˉ；)","","",$date+5);

		else{
		at_user_message($text,'','shoutbox');
		if(preg_match( "/.*(滚|贱|sb|傻b|傻逼|猪|fuck|我操|艹|2b|靠|我日|痴|2b|2B|操你|tmd)/",$text))
		sendshoutbox("[@$CURUSER[username]]：要注意文明用语啊亲(*￣︿￣)   ","","",$date+5);
		elseif(preg_match( "/.*(不能下载|跑流量|在哪下载)/",$text))
		sendshoutbox("[@$CURUSER[username]]：不会下载或者跑流量到论坛搜一下相关帖子吧，页面底部有麦田专用μt下载哦亲o(*￣▽￣*)o   ","","",$date+5);
		elseif(preg_match( "/.*(求麦粒|求种)/",$text))
		sendshoutbox("[@$CURUSER[username]]：(ｏ ‵-′)ノ禁止刷屏，禁止求麦粒，禁止求外站邀请码，禁止求种~~求邀请码请到[url=forums.php?action=viewforum&forumid=8]论坛邀请交流区[/url]，求种请到[url=viewrequest.php]求种区[/url] 看一眼公告嘛，慢走不送(ˉ▽￣～) ","","",$date+5);
		}
	}
	else
	{
		at_user_message($text,'','shoutbox');
		if(preg_match( "/.*(滚|贱|sb|傻b|傻逼|猪|fuck|我操|艹|2b|靠|我日|痴|2b|2B|操你|tmd)/",$text))
		sendshoutbox("[@$CURUSER[username]]：要注意文明用语啊亲(*￣︿￣)   ","","",$date+5);
		elseif(preg_match( "/.*(不(能|会)下载|UT|Utorrent|下载器|跑流量|在哪下载)/",$text))
		sendshoutbox("[@$CURUSER[username]]：不会下载或者跑流量到论坛搜一下相关帖子吧，页面底部有麦田专用[url=http://pt.nwsuaf6.edu.cn/forums.php?action=viewtopic&forumid=13&topicid=4740]μtorrent[/url]下载哦亲o(*￣▽￣*)o   ","","",$date+5);
		elseif(preg_match( "/.*(求麦粒|求种|送点麦粒)/",$text))
		sendshoutbox("[@$CURUSER[username]]：(ｏ ‵-′)ノ禁止刷屏，禁止求麦粒，禁止求外站邀请码，禁止求种~~求邀请码请到[url=forums.php?action=viewforum&forumid=8]论坛邀请交流区[/url]，求种请到[url=viewrequest.php]求种区[/url] 看一眼公告嘛，慢走不送(ˉ▽￣～) ","","",$date+5);
	}
	if (!$memcache->get('app_shoutbox_cleanup') or preg_match( "/\[\@$robot\](.*?)清屏/",$text))
	{
		$memcache->set('app_shoutbox_cleanup','1',false,60*60*24) or die ("请向管理员报告此错误");
		$cleanres = sql_query("SELECT * FROM shoutbox WHERE userid = 11");
		sendshoutbox("清屏完成，出来冒个泡~我才不会卖萌呢～[url=steal.php]你偷过麦粒么[/url]~~~[url=getcardnum.php]你绑定学号了么[/url]～～[url=autofaq/]你有什么问题要问么[/url]","","",$date+5);
		while ($cleanrow = mysql_fetch_assoc($cleanres))
		{
			//if(preg_match( "/(.*?)我还小着呢/",$cleanrow[text]) or preg_match( "/(.*?)不要问我问题啦/",$cleanrow[text])or preg_match( "/(.*?)我不认识否定词哎/",$cleanrow[text]))

			sql_query("DELETE FROM shoutbox WHERE id=$cleanrow[id]");

		}
	}
	print "<script type=\"text/javascript\">parent.document.forms['shbox'].shbox_text.value='';</script>";
}
}

$limit = ($CURUSER['sbnum'] ? $CURUSER['sbnum'] : 70);
if ($where == "helpbox")
{
$sql = "SELECT * FROM shoutbox WHERE type='hb' ORDER BY date DESC LIMIT ".$limit;
}
elseif ($CURUSER['hidehb'] == 'yes' || $showhelpbox_main != 'yes'){
$sql = "SELECT * FROM shoutbox WHERE type='sb' ORDER BY date DESC LIMIT ".$limit;
}
elseif ($CURUSER){
$sql = "SELECT * FROM shoutbox ORDER BY date DESC LIMIT ".$limit;
}
else {
die("<h1>".$lang_shoutbox['std_access_denied']."</h1>"."<p>".$lang_shoutbox['std_access_denied_note']."</p></body></html>");
}
$res = sql_query($sql) or sqlerr(__FILE__, __LINE__);
if (mysql_num_rows($res) == 0)
print("\n");
else
{
?>
<script type="text/javascript" src="jquerylib/jquery-1.5.2.min.js"></script>
<script type="text/javascript">
	function retuser(value){
		var c = $("#shbox_text", window.parent.document);
		c.val(c.val() + "[@"+value+"]  ");
		c.focus();
		c = $("#hbtext", window.parent.document);
		c.val(c.val() + "[@"+value+"]  ");
		c.focus();
	}
</script>
<?
	print("<table border='0' cellspacing='0' cellpadding='2' width='100%' align='left' style='word-break: break-all; word-wrap:break-word;table-layout: fixed;'>\n");
	$i = 1;
	while ($arr = mysql_fetch_assoc($res))
	{
		if (get_user_class() >= $sbmanage_class) {
//			$del="[<a href=\"shoutbox.php?del=".$arr[id]."\">".$lang_shoutbox['text_del']."</a>]";
$del = <<<EOD
[<a onclick="if(confirm('确定要删除吗？')){window.location.href='shoutbox.php?del={$arr['id']}';}" style="cursor:pointer">{$lang_shoutbox['text_del']}</a>]
EOD;
		}
		if ($arr["userid"]) {
			$username = get_username($arr["userid"],false,true,true,true,false,false,"",true);
			$arr2 = get_user_row($arr["userid"]);
			if ($_GET["type"] != 'helpbox' && $arr["type"] == 'hb')
				$username .= $lang_shoutbox['text_to_guest'];
			}
		else{
			$school = strpos($arr["ip"],':')?school_ip_location($arr["ip"],false):'';
			$userip = str_replace(':','',$arr['ip']);
			$guestid = substr($userip,strlen($userip) - 8);
			$username = "<b title='".$school."'>游客".$guestid."</b>";
			$arr2["username"] = "游客".$guestid;
		}
		if ($CURUSER['timetype'] != 'timealive')
			$time = strftime("%m.%d %H:%M",$arr["date"]);
		else $time = get_elapsed_time($arr["date"]).$lang_shoutbox['text_ago'];
		$messtext = $arr["text"];

		$messtext = str_replace("[@".$CURUSER['username']."]","[color=Red][b]@".$CURUSER['username']."[/b][/color]",$messtext);  //将回复给自己的名字染红
		print("<tr><td class=\"shoutrow\"><span class='date'>[".$time."]</span> ".
$del ." <span onclick=\"retuser('".$arr2["username"]."');\" style=\"cursor:pointer;\">[@]</span> ". $username." " . format_comment($messtext,1,true,true,600,true,false)."
</td></tr>\n");
		$i++;
	}
	print("</table>");
}
?>
</body>
</html>
