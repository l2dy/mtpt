<?php
/**
 * 晨光播种机、转发机接口文件  for nexusphp
 * @copyright http://cgbt.org
 *  
 */
$debug = false;
if ($debug)
{
	ini_set('display_errors', '1');
	error_reporting(E_ALL ^ E_NOTICE | E_STRICT);
}
date_default_timezone_set('Asia/Shanghai');

$cg_api = new cg_api();
class cg_api
{
	private $db_config = array(
		'host' => 'localhost',
		'user' => 'root',
		'password' => '',
		'name' => 'nexusphp'
	);
	private $config = array(
		'torrent_save_path' => '/torrents/',
		'tracker' => 'http://pt.nwsuaf6.edu.cn/announce.php',
		'torrent_source' => '[MT] MT'
	);
	private $params = array();
	private $user = array();
	private $dict = array();
	private $torrent_id = 0;

	public function __construct()
	{
		$action = "receive_torrent";
		$this->$action();
	}

	private function init_db()
	{
		$db_config = $this->db_config;
		$link = mysql_connect($db_config['host'], $db_config['user'], $db_config['password']);
		mysql_select_db($db_config['name'], $link);
		mysql_query("set names utf8");
	}

	private function receive_torrent()
	{
		$this->get_params();
		$this->init_db();
		$this->get_user();
		$torrent_info = $this->decode_torrent();
		$this->check_exists($torrent_info['info_hash']);
		$this->insert_torrents($torrent_info);
		$this->save_torrent();
		$this->response();
	}

	private function response()
	{
		echo $this->torrent_id;
		die();
	}

	private function insert_torrents($torrent_info)
	{
		$arr_fields = array(
			'filename' => $_FILES['torrent_file']['name'],
			'owner' => $this->user['id'],
			'visible' => 'yes',
			'name' => $this->params['name'],
			'size' => $torrent_info['total_length'],
			'numfiles' => count($torrent_info['files']),
			'type' => $torrent_info['type'],
			'category' => $this->params['category'],
			'save_as' => $torrent_info['name'],
			'added' => date("Y-m-d H:i:s"),
			'last_action' => date("Y-m-d H:i:s"),
			'info_hash' => $torrent_info['info_hash'],
			'descr' => $this->get_descr()
		);
		if (isset($_POST['other_upload_params']))
		{
			parse_str($_POST['other_upload_params'], $d);
			$arr_fields = array_merge($arr_fields, $d);
		}
		$sql = "insert into torrents (";
		$sql .= implode(",", array_keys($arr_fields));
		$sql .= ") values('";
		foreach ($arr_fields as $value)
		{
			$sql .= mysql_escape_string($value) . "','";
		}
		$sql = substr($sql, 0, -2);
		$sql .= ")";
		$res = mysql_query($sql);
		if (!$res)
		{
			$this->show_message('error: sql error 2');
		}
		$id = mysql_insert_id();
		
		foreach ($torrent_info['files'] as $file)
		{
			$filename = mysql_escape_string($file['filename']);
			$sql = "insert into files (torrent, filename, size)values('$id', '$filename', '{$file['length']}')";
			$res = mysql_query($sql);
			if (!$res)
			{
				echo $sql;
				$this->show_message('error: sql error 3');
			}
		}
		$this->torrent_id = $id;
	}

	private function save_torrent()
	{
		$torrent_file = $this->config['torrent_save_path'] . '/' . $this->torrent_id . '.torrent';
		file_put_contents($torrent_file, cg_bcode::bencode($this->dict));
	}

	private function check_exists($info_hash)
	{
		$sql = "select id from torrents where info_hash = '" . mysql_escape_string($info_hash) . "'";
		$res = mysql_query($sql);
		if (!$res)
		{
			$this->show_message('error: sql error 1');
		}
		$row = mysql_fetch_assoc($res);
		if ($row['id'] > 0)
		{
			echo $row['id'];
			die();
		}
	}

	private function show_message($msg)
	{
		echo $msg;
		die();
	}

	private function get_params()
	{
		$this->params['passkey'] = isset($_POST['passkey']) ? $_POST['passkey'] : '';
		if (!preg_match('/^[0-9a-f]{32}$/i', $this->params['passkey']))
		{
			$this->show_message('error: passkey error');
		}
		if (!isset($_FILES['torrent_file']))
		{
			$this->show_message('error: no torrent_file');
		}
		if (isset($_POST['name']))
		{
			$this->params['name'] = $_POST['name'];
		}
		if (isset($_POST['category']))
		{
			$this->params['category'] = $_POST['category'];
		}
		    $this->params['category'] = '401';
		if ($_FILES['torrent_file']['size'] == 0)
		{
			$this->show_message('error: torrent_file size 0');
		}
	}

	private function decode_torrent()
	{
		$torrent_file = $_FILES['torrent_file']['tmp_name'];
		$dict = cg_bcode::bdecode(file_get_contents($torrent_file));
		if (!is_array($dict) || !isset($dict['info']))
		{
			$this->show_message('error: not torrent file');
		}
		unset($dict['announce-list']);
		unset($dict['comment']);
		unset($dict['nodes']);
		$data = array();
		$dict['announce'] = $this->config['tracker'];
		$dict['info']['private'] = 1;
		$dict['info']['source'] = $this->config['torrent_source'];
		$data['info_hash'] = pack("H*", sha1(cg_bcode::bencode($dict['info'])));
		$data['total_length'] = 0;
		if (isset($dict['info']['length']))
		{
			$data['type'] = 'single';
			$data['total_length'] = $dict['info']['length'];
			$data['files'][0]['filename'] = $dict['info']['name'];
			$data['files'][0]['length'] = $dict['info']['length'];
		}
		else
		{
			$data['type'] = 'multi';
			$i = 0;
			foreach ($dict['info']['files'] as $file)
			{
				$data['total_length'] += $file['length'];
				$data['files'][$i]['filename'] = $dict['info']['name'] . '/' . implode('/', $dict['info']['files'][$i]['path']);
				$data['files'][$i]['length'] = $dict['info']['files'][$i]['length'];
				$i++;
			}
		}
		$data['name'] = $dict['info']['name'];
		if (empty($this->params['name']))
		{
			$this->params['name'] = $data['name'];
		}
		$this->dict = $dict;
		return $data;
	}

	private function get_user()
	{
		$sql = "select * from users where passkey='{$this->params['passkey']}' limit 1";
		$res = mysql_query($sql);
		if (!$res)
		{
			$this->show_message('error: sql error 0');
		}
		$this->user = mysql_fetch_assoc($res);
		if (empty($this->user))
		{
			$this->show_message('error: wrong passkey');
		}
	}

	private function get_descr()
	{
		$descr = <<<HTML



等待添加 海报图、电影简介




HTML;
	return $descr;
	
	}
}
class cg_bcode
{

	/**
     * bittorrent bdecode
     * @param  string  $s     string to be decoded
     * @param  integer $pos   start position
     * @return array
     */
	public static function bdecode($s)
	{
		$pos = 0;
		return self::bdecode_internal($s, $pos);
	}

	private static function bdecode_internal($s, &$pos = 0)
	{
		switch ($s[$pos])
		{
			case 'd':
				$ret = array();
				$pos++;
				while($s[$pos] != 'e')
				{
					$key = self::bdecode_internal($s, $pos);
					if ($key !== false)
					{
						$val = self::bdecode_internal($s, $pos);
						if ($val !== false)
						{
							$ret[$key] = $val;
						}
						else
						{
							$ret[$key] = 0;
						}
					}
					else
					{
						return false;
					}
				}
				$pos++;
				return $ret;
			case 'l':
				$ret = array();
				$pos++;
				while($s[$pos] != 'e')
				{
					$val = self::bdecode_internal($s, $pos);
					if ($val === false)
					{
						$val == 0;
					}
					$ret[] = $val;
				}
				$pos++;
				return $ret;
			case 'i':
				$pos++;
				$i = '';
				while($s[$pos] != 'e')
				{
					$i .= $s[$pos];
					$pos++;
				}
				$pos++;
				if (floatval($i) > pow(2, 64))
				{
					return false;
				}
				if (floatval($i) > pow(2, 31) - 1)
				{
					return floatval($i);
				}
				else
				{
					return intval($i);
				}
			case '0':
			case '1':
			case '2':
			case '3':
			case '4':
			case '5':
			case '6':
			case '7':
			case '8':
			case '9':
			case '.':
			case '-':
				$length_int = strpos($s, ':', $pos) - $pos;
				$str_lenth = intval(substr($s, $pos, $length_int));
				$pos += $length_int + 1;
				$str = substr($s, $pos, $str_lenth);
				$pos += $str_lenth;
				return $str;
			default:
				return false; //非种子文件长时间执行会造成服务器负载问题
				$pos++;
				return false;
		}
		
		return false;
	}

	/**
     * bittorrent bencode
     *
     * @param  mixed   $var        the variable to be encoded
     * @param  bool    $is_array   the variable is array or not
     * @return string
     */
	public static function bencode($var, $is_array = false)
	{
		if ($is_array || is_array($var))
		{
			$is_dict = false;
			$keys = array_keys($var);
			foreach ($keys as $k => $v)
			{
				if ($k !== $v)
				{
					$is_dict = true;
				}
			}
			
			$s = $is_dict ? 'd' : 'l';
			if ($is_dict)
			{
				ksort($var, SORT_STRING);
			}
			else
			{
				//ksort($var, SORT_NUMERIC);
			}
			foreach ($var as $k => $v)
			{
				if ($is_dict)
				{
					$s .= strlen(strval($k)) . ':' . $k;
				}
				if (is_string($v))
				{
					$s .= strlen($v) . ':' . $v;
				}
				elseif (is_integer($v))
				{
					$s .= 'i' . $v . 'e';
				}
				elseif (is_array($v))
				{
					$s .= self::bencode($v, true);
				}
				elseif (is_float($v))
				{
					$s .= 'i' . sprintf('%.0f', round($v)) . 'e';
				}
				elseif (is_bool($v))
				{
					$s .= $v ? 'i1e' : 'i0e';
				}
			}
			return $s . 'e';
		}
		elseif (is_float($var))
		{
			return 'i' . sprintf('%.0f', round($var)) . 'e';
		}
		elseif (is_integer($var))
		{
			return 'i' . $var . 'e';
		}
		elseif (is_string($var))
		{
			return strlen($var) . ':' . $var;
		}
		elseif (is_bool($var))
		{
			return $var ? 'i1e' : 'i0e';
		}
		return false;
	}

	/**
     * bdecode file
     * @param string $file
     */
	public static function bdecode_file($file)
	{
		if (file_exists($file))
		{
			return self::bdecode(file_get_contents($file));
		}
		return false;
	}
}
?>