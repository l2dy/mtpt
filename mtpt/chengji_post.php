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

	$post_fields = '&j_username='.$username.'&j_password='.$password.'&j_captcha='.$captcha;

	$ch = curl_init();

	// 2. 设置选项，包括URL

	curl_setopt($ch,CURLOPT_URL, "http://jwgl.nwsuaf.edu.cn/academic/j_acegi_security_check");

	curl_setopt($ch,CURLOPT_COOKIEFILE, $CookieFile);//同时发送Cookie

	curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch,CURLOPT_POST, 1);

	curl_setopt($ch,CURLOPT_POSTFIELDS, $post_fields);//提交查询信息

	$s = curl_exec($ch);

	echo $s;//输出结果

	curl_setopt($ch,CURLOPT_URL, "http://jwgl.nwsuaf.edu.cn/academic/manager/score/studentOwnScore.do?moduleId=2020&groupId=");

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

<meta name="viewport"content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>麦田PT成绩查询功能</title>

<h1>请到<a href="chengji_show.php">成绩查询</a>页面查询成绩！<h1>
<?php }

?>
