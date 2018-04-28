<?php
require "include/bittorrent.php";
dbconn();
$userid = 0 + $_GET["userid"];
$bgpic = 0 + $_GET["bgpic"];
if (!$userid)
	die;
if (!preg_match("/.*userid=([0-9]+)\.png$/i", $_SERVER['REQUEST_URI']))
	die;
if (!$my_img = $Cache->get_value('userbar_'.$_SERVER['REQUEST_URI'])){
$res = sql_query("SELECT username, uploaded, downloaded, class, privacy FROM users WHERE id=".sqlesc($userid)." LIMIT 1");
$row = mysql_fetch_array($res);
if (!$row)
	die;
elseif($row['privacy'] == 'strong')
	die;
elseif($row['class'] < $userbar_class)
	die;
	
else{
	$username = $row['username'];
	$uploaded = mksize($row['uploaded']);
	$downloaded = mksize($row['downloaded']);
	$sr = floor(($row['uploaded']/ $row['downloaded'] )* 1000) / 1000;//share rate
}
$my_img=imagecreatefrompng("pic/userbar/".$bgpic.".png");
imagealphablending($my_img, false);


	
function rewritedata()
{
	global $username,$uploaded,$downloaded,$sr;
	global $namered,$namegreen,$nameblue,$namesize,$namex,$namey;
	global $upred,$upgreen,$upblue,$upsize,$upx,$upy;
	global $downred,$downgreen,$downblue,$downsize,$downx,$downy;
	global $srred,$srgreen,$srblue,$srsize,$srx,$sry;
	global $name_color,$up_color,$down_color,$sr_color;
	if (isset($_GET['namered']) && $_GET['namered']>=0 && $_GET['namered']<=255)
		$namered = $_GET['namered'];
	if (isset($_GET['namegreen']) && $_GET['namegreen']>=0 && $_GET['namegreen']<=255)
		$namegreen =$_GET['namegreen'];
	if (isset($_GET['nameblue']) && $_GET['nameblue']>=0 && $_GET['nameblue']<=255)
		$nameblue =$_GET['nameblue'];
	if (isset($_GET['namesize']) && $_GET['namesize']>=1 && $_GET['namesize']<=9)
		$namesize = $_GET['namesize'];
	if (isset($_GET['namex']) && $_GET['namex']>=0 && $_GET['namex']<=999)
		$namex = $_GET['namex'];
	if (isset($_GET['namey']) && $_GET['namey']>=0 && $_GET['namey']<=500)
		$namey = $_GET['namey'];
		//name
		
	if (isset($_GET['upred']) && $_GET['upred']>=0 && $_GET['upred']<=255)
		$upred = $_GET['upred'];
	if (isset($_GET['upgreen']) && $_GET['upgreen']>=0 && $_GET['upgreen']<=255)
		$upgreen =$_GET['upgreen'];
	if (isset($_GET['upblue']) && $_GET['upblue']>=0 && $_GET['upblue']<=255)
		$upblue = $_GET['upblue'];
	if (isset($_GET['upsize']) && $_GET['upsize']>=1 && $_GET['upsize']<=5)
		$upsize =  $_GET['upsize'];
	if (isset($_GET['upx']) && $_GET['upx']>=0 && $_GET['upx']<=999)
		$upx = $_GET['upx'];
	if (isset($_GET['upy']) && $_GET['upy']>=0 && $_GET['upy']<=999)
		$upy =  $_GET['upy'];
		//up
		
	if (isset($_GET['downred']) && $_GET['downred']>=0 && $_GET['downred']<=255)
		$downred =  $_GET['downred'];
	if (isset($_GET['downgreen']) && $_GET['downgreen']>=0 && $_GET['downgreen']<=255)
		$downgreen =  $_GET['downgreen'];
	if (isset($_GET['downblue']) && $_GET['downblue']>=0 && $_GET['downblue']<=255)
		$downblue = $_GET['downblue'];
	if (isset($_GET['downsize']) && $_GET['downsize']>=1 && $_GET['downsize']<=5)
		$downsize = $_GET['downsize'];
	if (isset($_GET['downx']) && $_GET['downx']>=0 && $_GET['downx']<=999)
		$downx =  $_GET['downx'];
	if (isset($_GET['downy']) && $_GET['downy']>=0 && $_GET['downy']<=999)
		$downy = $_GET['downy'];
		//down
		
	if (isset($_GET['srred']) && $_GET['srred']>=0 && $_GET['srred']<=255)
		$srred =$_GET['srred'];
	if (isset($_GET['srgreen']) && $_GET['srgreen']>=0 && $_GET['srgreen']<=255)
		$srgreen = $_GET['srgreen'];
	if (isset($_GET['srblue']) && $_GET['srblue']>=0 && $_GET['srblue']<=255)
		$srblue = $_GET['srblue'];
	if (isset($_GET['srsize']) && $_GET['srsize']>=1 && $_GET['srsize']<=5)
		$srsize = $_GET['srsize'];
	if (isset($_GET['srx']) && $_GET['srx']>=0 && $_GET['srx']<=999)
		$srx = $_GET['srx'];
	if (isset($_GET['sry']) && $_GET['sry']>=0 && $_GET['sry']<=999)
		$sry =$_GET['sry'];
	//share rate
}
function creatthem()
{
	global $namered,$namegreen,$nameblue,$namesize,$namex,$namey;
	global $name_color,$up_color,$down_color,$sr_color;
	global $upred,$upgreen,$upblue,$upsize,$upx,$upy;
	global $downred,$downgreen,$downblue,$downsize,$downx,$downy;
	global $srred,$srgreen,$srblue,$srsize,$srx,$sry;
	global $username,$uploaded,$downloaded,$sr,$my_img;
$name_colour = imagecolorallocate($my_img, $namered, $namegreen, $nameblue);
$up_colour = imagecolorallocate($my_img, $upred, $upgreen, $upblue);
$down_colour = imagecolorallocate($my_img, $downred, $downgreen, $downblue);
$sr_colour = imagecolorallocate($my_img, $srred, $srgreen, $srblue);

//imagestring($my_img, $namesize, $namex, $namey, $username, $name_colour);
imagestring($my_img, $upsize, $upx, $upy, $uploaded, $up_colour);
imagestring($my_img, $downsize, $downx, $downy, $downloaded, $down_colour);
imagestring($my_img, $srsize, $srx, $sry, $sr, $sr_colour);
//生成用户名
$pic=imagecreate(566,222); 
	$black=imagecolorallocate($pic,255,255,255);
	$tran=imagecolortransparent($pic,$black);
	$white=imagecolorallocate($pic,$namered,$namegreen,$nameblue); 
	$font="fonts/simhei.ttf";  
	imagettftext($pic,$namesize,0,$namex,$namey,$white,$font,$username);
	imagecopymerge($my_img,$pic,0,0,0,0,566,222,80); 
}


if($bgpic==0)
{
$namered=1;$namegreen=0;$nameblue=0;$namesize=12;$namex=498;$namey=44;
$upred=210;$upgreen=11;$upblue=11;$upsize=4;$upx=68;$upy=17;
$downred=5;$downgreen=0;$downblue=255;$downsize=4;$downx=220;$downy=32;
$srred=255;$srgreen=255;$srblue=2;$srsize=4;$srx=370;$sry=20;

		if (strlen($username)>=10) {$namesize=10;}
	rewritedata();
	creatthem();

}

else if($bgpic==1)
{
$namered=0;$namegreen=1;$nameblue=1;$namesize=12;$namex=10;$namey=31;
$upred=18;$upgreen=40;$upblue=248;$upsize=3;$upx=80;$upy=5;
$downred=255;$downgreen=0;$downblue=2;$downsize=3;$downx=155;$downy=20;
$srred=11;$srgreen=111;$srblue=11;$srsize=5;$srx=440;$sry=16;

if (strlen($username)>=10) {$namesize=11;$namey=33;}
		rewritedata();
$name_colour = imagecolorallocate($my_img, $namered, $namegreen, $nameblue);
$up_colour = imagecolorallocate($my_img, $upred, $upgreen, $upblue);
$down_colour = imagecolorallocate($my_img, $downred, $downgreen, $downblue);
$sr_colour = imagecolorallocate($my_img, $srred, $srgreen, $srblue);

//imagestring($my_img, $namesize, $namex, $namey, $username, $name_colour);
//imagettftext($my_img, $namesize, 0, $namex, $namey, $name_colour, "fonts/simhei.ttf", $username);
imagestring($my_img, $upsize, $upx, $upy, $uploaded, $up_colour);
imagestring($my_img, $downsize, $downx, $downy, $downloaded, $down_colour);
//imagestring($my_img, $srsize, $srx, $sry, $sr, $sr_colour);不显示分享率
//写用户名
$pic=imagecreate(500,222); 
	$black=imagecolorallocate($pic,255,255,255);
	$tran=imagecolortransparent($pic,$black);
	$white=imagecolorallocate($pic,$namered,$namegreen,$nameblue); 
	$font="fonts/simhei.ttf";  
	imagettftext($pic,$namesize,0,$namex,$namey,$white,$font,$username);
	imagecopymerge($my_img,$pic,0,0,0,0,500,222,80); 
}


/*
else if($bgpic==2)
{
$namered=0;$namegreen=0;$nameblue=0;$namesize=5;$namex=585;$namey=30;
$upred=2;$upgreen=11;$upblue=11;$upsize=5;$upx=80;$upy=15;
$downred=5;$downgreen=0;$downblue=2;$downsize=5;$downx=255;$downy=32;
$srred=11;$srgreen=111;$srblue=11;$srsize=5;$srx=440;$sry=16;

		rewritedata();
		creatthem();
}
*/
else{die ("咱家服务器上没有bgpic=".$bgpic).'这个流量条';}

imagesavealpha($my_img, true);
$Cache->cache_value('userbar_'.$_SERVER['REQUEST_URI'], $my_img, 300);
}
header("Content-type: image/png");
imagepng($my_img);
imagedestroy($my_img);
?>

