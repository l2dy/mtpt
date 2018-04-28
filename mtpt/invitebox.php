<?php
require "include/bittorrent.php";
dbconn();
parked();
failedloginscheck ();
cur_user_check () ;
$imgtypes = array (null,'gif','jpg','png');
$scaleh = 720; // set our height size desired
$scalew = 1280; // set our width size desired

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$username = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[username]))));
	$email = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[email]))));
	$school = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[school]))));
	$grade = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[grade]))));
	$web = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[web]))));
	$disk = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[disk]))));
	$ip = sqlesc(RemoveXSS($_POST[ip]));
	$self_introduction = sqlesc(RemoveXSS(htmlspecialchars(trim($_POST[self_introduction]))));

	if (!$_POST[username]||!$_POST[email])
	stderr("错误", "必须填写用户名和邮箱");
	$email = safe_email($email);
if (!check_email(htmlspecialchars(trim($_POST[email]))))
	stderr("错误", "邮箱地址无效");

	$file = $_FILES["file"];
	if (!isset($file) || $file["size"] < 1)
		$url='';
	elseif(isset($file)){
		$dir_name = "invite/".date("Ymd");
		if(!file_exists($dir_name)){//检查文件夹是否存在
	        mkdir ("$dir_name");    //没有就创建一个新文件夹
		}
		$rand_prefix=time()+rand(-10000,10000);
		$pp=pathinfo($filename = $file["name"]);
		if($pp['basename'] != $filename)
		stderr('图片上传失败', "文件名错误。");
		$tgtfile = "invite/$filename";
		$newfile = $dir_name."/".md5($rand_prefix.$filename);
		if (file_exists($newfile))
		stderr("上传失败", '你上传的文件 '.htmlspecialchars($filename).' 文件名重复或非法，请检查文件名长度、字符，重命名之后重新上传',false);

		$size = getimagesize($file["tmp_name"]);
		$height = $size[1];
		$width = $size[0];
		$it = $size[2];
		if($imgtypes[$it] == null || $imgtypes[$it] != strtolower($pp['extension']))
		stderr("上传失败", "扩展名无效：<b>只允许gif,jpg或png</b>！",false);
		$newfile = $newfile.".".$imgtypes[$it];

		// Scale image to appropriate avatar dimensions
		$hscale=$height/$scaleh;
		$wscale=$width/$scalew;
		$scale=($hscale < 1 || $wscale < 1) ? 1 : (( $hscale > $wscale) ? $wscale : $hscale);
		$newwidth=floor($width/$scale);
		$newheight=floor($height/$scale);

		if ($it==1)
			$orig=@imagecreatefromgif($file["tmp_name"]);
		elseif ($it == 2)
			$orig=@imagecreatefromjpeg($file["tmp_name"]);
		else
			$orig=@imagecreatefrompng($file["tmp_name"]);
		if(!$orig)
		stderr("图片处理失败","对不起，上传的文件 " ."$imgtypes[$it]"."处理失败。请用图片编辑软件处理后再上传。谢谢！");
		$thumb = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresized($thumb, $orig, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		$ret=($it==1)?imagegif($thumb, $tgtfile): ($it==2)?imagejpeg($thumb, $tgtfile):imagepng($thumb, $tgtfile);
		$ori_name = str_replace(" ", "%20","$filename");
		rename("invite/$filename",$newfile);
	}
	$name = sqlesc($ori_name);
	$added = sqlesc(date("Y-m-d H:i:s"));

	sql_query("INSERT INTO invitebox (username, ip, email, school, grade, web, disk, ori_name, self_introduction, added, pic) VALUES (".$username.", ".$ip.",".$email.", ".$school.", ".$grade.", ".$web.", ".$disk.",".$name.", ".$self_introduction.", $added, ".sqlesc($newfile).")") or sqlerr(__FILE__, __LINE__);
	$subject = "有游客申请邀请码";
	$msg = '请点击[url=/viewinvitebox.php]这里[/url]查看。';
	$added = sqlesc ( date ( "Y-m-d H:i:s" ) );
	sql_query("INSERT INTO staffmessages (sender, subject, msg, added, goto) VALUES (0,'".$subject."','".$msg."',$added,1)") or sqlerr(__FILE__, __LINE__);

	stderr('提交成功', "您的请求已发送，请耐心等待管理员审核，并时常查看注册邮箱。<br/>一切顺利的话，邀请将在2日内发送到您的邮箱。",false);
}

stdhead("邀请申请区");
	if(!ipv6ip(getip())) //不是ipv6地址就提示
	{
		stdmsg("你访问了假麦田", "<h1>您使用IPV4地址访问了假麦田,使用假麦田会对正常麦田用户造成偷跑锐捷流量等各种不好的影响,所以该地址将在近期被禁用,使用该地址你不会得到邀请码！<br/><b>请认准麦田PT官方地址：(pt.nwsuaf6.edu.cn)复制括号中的内容到浏览器地址栏粘贴访问。</b><br/>麦田PT只支持IPV6访问，西农用户请使用锐捷有线认证在宿舍及部分办公区获取IPV6地址。<br/>新手QQ群：145543216 麦田PT新人指导</h1>");
		stdfoot();
		exit;
	}
	$istunnel=!ip_filter(getip(),true,true);//判断是否隧道
	if ($istunnel){
		print("<div align=center style=\"background:black;\" ><a href=\"http://pt.nwsuaf6.edu.cn/faq.php#id16\" target=\"_blank\"><font face=\"verdana\" size=4 color=\"white\">重要提示！您正在使用隧道访问麦田，这会导致您产生巨大的校园网计费流量！<br/>请点击这里解决！</font></a></div><br/>");
	}
	if (substr(sqlesc(getip()),0,15) == "'2001:250:1002:"){ //判断是否校内IP
		stdmsg("校内用户请使用学工号自助注册", "<h1>系统检测到您为校内用户，请使用<a class=\"faqlink\" target=_blank href=signupcard.php>校内用户注册通道</a></h1>");
		stdfoot();
		exit;

	}
?>
<h1>邀请申请区</h1>
<h3></br>西北农林科技大学 的用户请使用 学工号自助注册<a class="faqlink" target=_blank href=signupcard.php>校内用户注册</a>注册帐号。<br/></h3>
<form method="post" action=invitebox.php enctype="multipart/form-data">
<table border=1 cellspacing=0 cellpadding=5>
<?php

if(!is_writable("$bitbucket"))
print("<tr><td align=left colspan=2>"."注意：上传路径不可写。请将该情况报告给管理员！"."</tr></td>");
?>
<tr><td class="rowhead">欲申请用户名</td>
<td class="rowfollow" align="left"><input type="text" id="username" name="username" autocomplete="off" style="width: 200px; border: 1px solid gray"></td><td class="rowfollow" align="left">必须填写，本站支持中文和英文字母做用户名，不支持空格及符号。</td></tr>
<tr><td class="rowhead">邮箱</td>
	<td class="rowfollow" align="left"><input type="text" id="email" name="email" autocomplete="off" style="width: 200px; border: 1px solid gray"></td><td class="rowfollow" align="left">必须填写，邀请将发到此邮箱。部分邮箱可能将邀请信视为垃圾邮件，请注意查收。</td></tr>
<tr><td class="rowhead">所在学校</td>
<td class="rowfollow" align="left"><input type="text" id="school" name="school" autocomplete="off" style="width: 200px; border: 1px solid gray"></td><td class="rowfollow" align="left">西北农林科技大学 的用户请使用 学工号自助注册<a class="faqlink" target=_blank href=signupcard.php>校内用户注册</a>注册帐号。</td></tr>
<tr><td class="rowhead">年级</td>
<td class="rowfollow" align="left"><input type="text" id="grade" name="grade" autocomplete="off" style="width: 200px; border: 1px solid gray"></td><td class="rowfollow" align="left">考研党慎入！（为什么看不起考研党￣へ￣）</td></tr>
<tr><td class="rowhead">网络情况</td>
<td class="rowfollow" align="left"><input type="text" id="web" name="web" autocomplete="off" style="width: 200px; border: 1px solid gray"></td><td class="rowfollow" align="left">请说明网络类型及带宽，如：“教育网，V4+V6或纯v6，100Mbps”。</td></tr>
<tr><td class="rowhead">硬盘情况</td>
<td class="rowfollow" align="left"><input type="text" id="disk" name="disk" autocomplete="off" style="width: 200px; border: 1px solid gray"></td><td class="rowfollow" align="left">如：500G笔记本硬盘+2T移动硬盘</td></tr>
<tr><td class="rowhead">补充说明</td>
<td class="rowfollow" align="left"><textarea id="self_introduction" name="self_introduction" autocomplete="off" style="width: 200px; height: 100px;border: 1px solid gray"></textarea></td><td class="rowfollow" align="left">在此说明你对PT的认识，以及为什么要加入麦田。如：“我想”</td></tr>
<?php
	$ip = sqlesc ( getip () );
print"<input type=hidden name=ip value=$ip>"
?>
<tr><td class="rowhead">其他站点截图<br/></td>
<td class="rowfollow"><input type="file" name="file" size="60"></td><td class="rowfollow" align="left">为了提高成功率，建议尽量上传。选择数据最好的站点截图即可</td></tr>
<tr><td class="rowhead"></td><td class="rowfollow"><input type="submit" value="提交"><input type="reset" value="重置"></td></tr>
</table>
</form>
<?php
stdfoot();
