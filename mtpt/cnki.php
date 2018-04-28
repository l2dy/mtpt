<?php
/* Cnki文献传递
		要求CRUL库支持。
		author:Cide
 */
class cnkiClient
{
	private $cnkiRefer = '';//当前页面的来源
	private $cookiejar;//当前页面cookie文件名
	private $lifetime = 3600;//资源有效时长。
	private $cookieDir = '';//cookie目录
	private $password = '123456';//密码
	//初始化
	function __construct()
	{
		if($this->password!='')//要求认证
		{
			$this->login();
		}
		if(!is_dir(sys_get_temp_dir().'/cookieDir'))
			mkdir(sys_get_temp_dir().'/cookieDir',0700);
		$this->cookieDir = sys_get_temp_dir().'/cookieDir';//保存远程cookie的具体目录
		if(isset($_COOKIE['cookiejar']))//保存服务器上的远程cookie的位置
			$this->cookiejar = $_COOKIE['cookiejar'];
		else
		{
			$this->cookiejar = tempnam($this->cookieDir, "cke");
			setcookie('cookiejar',$this->cookiejar,time()+$this->lifetime,'/');
		}
		if(isset($_COOKIE['cnkiRefer']))
			$this->cnkiRefer = $_COOKIE['cnkiRefer'];
		else
		{
			$this->cnkiRefer = 'http://epub.cnki.net/kns/brief/';
			setcookie('cnkiRefer',$this->cnkiRefer,time()+$this->lifetime,'/');
		}
		$this->autoclean($this->cookieDir);
	}
	//认证部分
	function login()
	{
		if(!isset($_COOKIE['pwd']) || $_COOKIE['pwd']!= md5($this->password))//静默密码认证不通过
		{
			if(isset($_GET['pwd']) && $_GET['pwd']==$this->password)//GET认证通过,明文验证
			{
				setcookie('pwd',md5($this->password),time()+$this->lifetime,'/');
				header('Location: '.basename(__FILE__));
				exit;
			}
			else if(!isset($_GET['login']))//未认证的，除了login页可见，其他的都转向login页
			{
				header('Location: '.basename(__FILE__).'?login=1');
				exit;
			}
			else
			{
				header('Content-type: text/html;charset=utf-8');
			?>
<!DOCTYPE html>
<html>
<meta charset="utf-8"/>
<head>
	<title>CNKI文献传递</title>
</head>
<body>
<h2>CNKI文献快速传递,只为小伙伴们服务。</h2>
<hr/>
<form action="<?php echo basename(__FILE__).'';?>" method="get">
<!-- 提交表单 -->
<label for="keyword">暗语：</label>
<input type="text" name="pwd" value="小伙伴们都知道(^o^)."/>
<input type="submit" value="我是小伙伴"/>
</form>
<hr/>
</body>
</html>
			<?php
				exit;
			}
		}
	}
	//自动清理临时cookie文件
	function autoclean($dir)
	{
		if(is_dir($dir))
		{
			if ($dh = opendir($dir)) 
			{
				while(($file = readdir($dh)) !== false)
				{
					if($file!="." && $file!=".." && is_dir($dir.'/'.$file) == false)
					{
							if(time() - filemtime($dir.'/'.$file) > $this->lifetime)//一小时内的认为有效
								unlink($dir.'/'.$file);
					}
				}
				closedir($dh);
			}
		}
	}
	//清理cookie
	function clean()
	{
		unlink($this->cookiejar);
		setcookie('SID','', time()-1,'/');
		setcookie('cnkiRefer','',time()-1,'/');
		setcookie('cnkiUserKey','', time()-1,'/');
		setcookie('cookiejar','', time()-1,'/');
		setcookie('pwd','', time()-1,'/');
	}
	//搜索一个关键字文本，返回html文本
	function search($str = '')
	{
		if($str == '')
			return '';
		$quest = 'http://epub.cnki.net/KNS/request/SearchHandler.ashx?action=&NaviCode=*&ua=1.11&PageName=ASP.brief_default_result_aspx&DbPrefix=SCDB&DbCatalog=%e4%b8%ad%e5%9b%bd%e5%ad%a6%e6%9c%af%e6%96%87%e7%8c%ae%e7%bd%91%e7%bb%9c%e5%87%ba%e7%89%88%e6%80%bb%e5%ba%93&ConfigFile=SCDBINDEX.xml&db_opt=CJFQ%2CCJFN%2CCDFD%2CCMFD%2CCPFD%2CIPFD%2CCCND%2CCCJD%2CHBRD&txt_1_sel=FT%24%25%3D%7C&txt_1_value1='.urlencode($str).'&txt_1_special1=%25&his=0&parentdb=SCDB&__='.urlencode($this->gmt(time()+8*3600));
		$quest2 = 'http://epub.cnki.net/kns/brief/brief.aspx?pagename=ASP.brief_default_result_aspx&dbPrefix=SCDB&dbCatalog=%e4%b8%ad%e5%9b%bd%e5%ad%a6%e6%9c%af%e6%96%87%e7%8c%ae%e7%bd%91%e7%bb%9c%e5%87%ba%e7%89%88%e6%80%bb%e5%ba%93&ConfigFile=SCDBINDEX.xml&research=off&t='.(time()*1000).'&keyValue='.urlencode($str).'&S=1&curpage=1&RecordsPerPage=20';
		$this->cnkiRefer = $quest2;
		setcookie('cnkiRefer',$this->cnkiRefer,time()+3600*24,'/');
		$this->curl_get($quest);
		return $this->curl_get($quest2);
	}
	//刷新一个页面，返回html文本
	function reflesh($url = '')
	{
		return $this->curl_get($url);
	}
	//链接转换
	function url2local($htmlText)
	{
		//取最后有效url，该url代表了页面位置，页面内链接以此为据
		$p = "/href=['|\"]([^'|^\"]*)/";
		return preg_replace_callback($p,create_function('$m',"return 'href=\"?reflesh='.urlencode(url_turn(trim(\$m[1]),'{$this->cnkiRefer}')).'\" ';"),str_replace('&#xA;',' ',$htmlText));
	}
	//get操作,成功返回get得到的html，失败返回FALSE
	 function curl_get($url)
	{
		// 创建一个新cURL资源
		$ch = curl_init();
		// 设置URL和相应的选项
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//将html返回
		curl_setopt($ch, CURLOPT_HEADER, false);//不返回头信息
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiejar);//链接断开时保存cookie
		if(isset($_COOKIE['cnkiUserKey']))
			curl_setopt($ch, CURLOPT_COOKIE, 'cnkiUserKey='.$_COOKIE['cnkiUserKey'].';');//链接断开时保存cookie
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiejar);//携带cookie
		curl_setopt($ch, CURLOPT_REFERER, $this->cnkiRefer);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//允许抓取跳转后页面
		curl_setopt($ch, CURLOPT_TIMEOUT,60);//60秒超时
		// 抓取URL并把它传递给浏览器
		$b = curl_exec($ch);
		$z = curl_getinfo($ch);
		if($z['content_type'])
		{
			header('Content-type: '.$z['content_type']);
		}
		if($z['url'])
			$this->cnkiRefer = $z['url'];//最后的有效地址
		//关闭cURL资源，并且释放系统资源
		curl_close($ch);
		return $b;
	}
	//GMT北京时间,unix时间戳转换GMT字符串
	function gmt($time)
	{
		return gmdate('D M d Y H:i:s T', $time).'+0800 (中国标准时间)';
	}
}
//链接转换,在基础链接的基础上转换
function url_turn($url,$pre_url)
{
	if(!$url_part= parse_url($pre_url)) return '';
	$base_url = $url_part['scheme'].'://'.$url_part['host'].(isset($url_part['port'])?':'.$url_part['port']:'').$url_part['path'];
	if(substr($url,0,3) == '../')
		return substr($base_url,0,strrpos($base_url,"/",strrpos($base_url,"/")-strlen($base_url)-1)).substr($url,2);
	else if(substr($url,0,1) == '/')
		return $url_part['scheme'].'://'.$url_part['host'].(isset($url_part['port'])?':'.$url_part['port']:'').$url;
	else if(substr($url,0,1) == '?')
		return $base_url.$url;
	else if(substr($url,0,2) == './')
		return substr($base_url,0,strrpos($base_url,"/")).substr($url,1);
	else 
		return $url;
}
$c = new cnkiClient();
$base_url = 'http://www.cnki.net/KCMS/detail/detail.aspx';
if(isset($_GET['reflesh']))
{
	$_GET['reflesh'] = htmlspecialchars_decode($_GET['reflesh']);//链接中的实体。
	echo $c->url2local($c->reflesh($_GET['reflesh']),$_GET['reflesh']);
	exit;
}
if(isset($_GET['clean']))
{
	$c->clean();
	header('Location: '.basename(__FILE__));
	exit;
}
if(!isset($_GET['keyword']))
{
	header('Content-type: text/html;charset=utf-8');
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8"/>
<head>
	<title>CNKI文献传递</title>
</head>
<body>
<h2>CNKI文献在线快速传递</h2>
<hr/>
<span style="font-size:14px;float:right;color:green;">下载不了的时候，不妨点这里<a href="?clean=1" style="font-weight:bold;color:blue;">清除Cookie</a></span>
<form action="<?php echo basename(__FILE__);?>" method="get" target='searchPage'>
<!-- 提交表单 -->
<label for="keyword">关键字：</label>
<input type="text" name="keyword" value="Cide"/>
<input type="submit" value="检索"/>
</form>
<iframe name="searchPage" src="<?php echo basename(__FILE__);?>?keyword=cide" width="100%"/>
<hr/>
</body>
</html>
<?php
	exit;
}
echo $c->url2local($c->search($_GET['keyword']));
?>