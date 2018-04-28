<?php
require "include/bittorrent.php";
dbconn();
// require_once(get_langfile_path());
loggedinorreturn();
?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>麦田PT成绩查询功能</title>
<link rel="stylesheet" href="<?php echo get_font_css_uri()?>" type="text/css">
<link rel="stylesheet" href="<?php echo get_css_uri()."theme.css"?>" type="text/css">
<style>
body {
TEXT-ALIGN: center;
font-size:20px;
}
</style>
</head>
<?php
if(isset($_POST['CookieFile'])){

    $CookieFile = $_POST['CookieFile'];

}

else if(isset($_GET['CookieFile'])){

    $CookieFile = $_GET['CookieFile'];

}

else{

 //   $i = 0;

 //   while(file_exists(dirname(__FILE__).'/cookie/cookie.tmp'.$i))

  //      $i = $i + 1;

  //  $CookieFile = dirname(__FILE__).'/cookie/cookie.tmp'.$i;
$CookieFile= tempnam('./chengji_cookie/','cookie');
}

//$CookieFile= tempnam('./cookie/','cookie');

//$CookieFile = dirname(__FILE__).'/cookie.tmp';

if(isset($_GET["img"])){

	$url = 'http://jwgl.nwsuaf.edu.cn/academic/getCaptcha.do';//验证码code

	$ch = curl_init($url);

	curl_setopt($ch,CURLOPT_COOKIEJAR, $CookieFile);//把返回来的cookie信息保存在文件中

	//curl_setopt($ch, CURLOPT_REFERER, "http://jwgl.nwsuaf.edu.cn/");

	//设置请求的来源(referrer)

	curl_exec($ch);

	curl_close($ch);

	exit();

}

if(isset($_POST['captcha'])){

	$username=$_POST['username'];

	$password=$_POST['password'];

	$captcha=$_POST['captcha'];

	$select=$_POST['select'];



	$post_fields = '&j_username='.$username.'&j_password='.$password.'&j_captcha='.$captcha;

	$ch = curl_init();

	// 2. 设置选项，包括URL

	curl_setopt($ch,CURLOPT_URL, "http://jwgl.nwsuaf.edu.cn/academic/j_acegi_security_check");

	curl_setopt($ch,CURLOPT_COOKIEFILE, $CookieFile);//同时发送Cookie

	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch,CURLOPT_POST, 1);

	curl_setopt($ch,CURLOPT_POSTFIELDS, $post_fields);//提交查询信息

	$s = curl_exec($ch);

	echo "$s";//输出结果
	// $my_str='/<table.*class="datalist"([\s\S]*)<\/table/iU';
	// if(preg_match("$my_str", "$s", $matches)){
		// print "A match was found:". $matches[0];
	// } else {
		// print "A match was not found.";
	// }



	if($select=="ben")

		{curl_setopt($ch,CURLOPT_URL, "http://jwgl.nwsuaf.edu.cn/academic/manager/score/studentOwnScore.do?moduleId=2020&groupId=");}

	else{

	curl_setopt($ch,CURLOPT_URL, "http://jwgl.nwsuaf.edu.cn/academic/manager/score/studentOwnScore.do?groupId=&moduleId=2021&year=35&term=1&para=0&sortColumn=&Submit=%E6%9F%A5%E8%AF%A2");}



	curl_setopt($ch,CURLOPT_COOKIEFILE, $CookieFile);//同时发送Cookie

	curl_setopt($ch,CURLOPT_HEADER, 0);

	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 0);

//	curl_setopt($ch,CURLOPT_POST, 1);

//	curl_setopt($ch,CURLOPT_POSTFIELDS, $p);//提交查询信息

	curl_exec($ch);

//	echo $bs;//输出结果

	curl_close($ch);

	}else{

?>


<!--<body class='inframe'>-->
<body>
<div>
<form id="form1" name="form1" method="post" action="">

<h2>西北农林科技大学麦田PT成绩查询功能</h2>
<br>
提示：成绩查询结果页面那些按钮不可点！点击会有奇怪的效果！<br>
若查出来为空白的情况，请确保账号密码正确，并评教后重试!
<br>
不要问我为啥页面这么丑,我也想知道为啥……
<br>
<br>



   学&nbsp;&nbsp;&nbsp;号：<input type="text" name="username"><br>

   密&nbsp;&nbsp;&nbsp;码：<input type="password" name="password"><br>

   验证码：<input type="text" name="captcha"><br/><img src="chengji_post.php?img=true&CookieFile=<?php echo$CookieFile;?>" /><!--由服务器端取图片内容并输出-->

           <input type="text" style="display:none;" name="CookieFile" value="<?php echo $CookieFile;?>">

    <br>选择查询类别:

	<select name="select">

	<option value="ben" selected>本学期

	<option value="shang">上学期

	</select>

	<br>

</p><input type="submit" name="button" id="button" value="提交" /> <input type="reset" value="重置">
</p>

</form>
</div>
</body>

</html>
<?php }

?>
