<?php
session_start();
define('IN_TRACKER', true);
define("PROJECTNAME","MTPT");
define("NEXUSPHPURL","https://pt.nwsuaf6.edu.cn");
define("NEXUSWIKIURL","https://pt.nwsuaf6.edu.cn");
define("VERSION","Powered by <a href=\"aboutmtpt.php\">".PROJECTNAME."</a>");
define("THISTRACKER","General");
$showversion = " - Powered by ".PROJECTNAME;
$rootpath=realpath(dirname(__FILE__) . '/..');
set_include_path(get_include_path() . PATH_SEPARATOR . $rootpath);
$rootpath .= "/";
/*默认网站模板*/
define('MTPTTEMPLATES',"default");
/*默认函数页模版路径*/
define('FUNCTIONSMARTY',"default/function_smarty");
/*配置smarty模版*/
require_once($rootpath."/include/smarty/libs/Smarty.class.php");
$smarty=new Smarty();
/*指定模板文件的路径*/
$smarty->template_dir=$rootpath."/templates";
/*指定编译的文件路径*/
$smarty->compile_dir=$rootpath."/data/templates_compile";
/*不使用缓存*/
$smarty->caching=false;
/*指定smarty配置文件路径*/
$smarty->config_dir=$rootpath."/include/smarty/config";
/*指定左定界符，避免和JS冲突*/
$smarty->left_delimiter="{% ";
/*指定右定界符，避免和JS冲突*/
$smarty->right_delimiter=" %}";
/*设置编译号id*/
$smarty->compile_id="1.0.1";
/*强制编译，仅在网站开发时方便实时更新使用*/
$smarty->force_compile=true;
include($rootpath . 'include/core.php');
include_once($rootpath . 'include/functions.php');
//include_once($rootpath . 'include/function_output.php');
//error_reporting(E_ALL);

