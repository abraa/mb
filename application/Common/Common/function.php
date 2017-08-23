<?php
/**
 * ====================================
 * 公共方法库
 * ====================================
 * Author: 9004396
 * Date: 2016-06-25 11:38
 * ====================================
 * File: verify.php
 * ====================================
 */

/**
 * 生成excel
 * @param array $data 数组参数 格式($data = array('title'=>array(),'data'=>array()))
 * @param $fileName 文件名称，不带后缀
 * @return bool
 * @throws Exception
 */
function export_data($data = array(), $fileName = '') {
    if (empty($data)) return false;

    $date = date("Y_m_d", time());
    $fileName = (!empty($fileName) ? $fileName : $date) . ".xlsx";


    foreach ($data['data'] as $key => $value) { //比较字符串的长度,生成最大长度数组
        static $newData = array();
        foreach ($value as $k => $v) {
            if (empty($newData)) {
                $newData = $value;
            } else {
                if (mb_strlen($newData[$k], 'utf8') < mb_strlen($v, 'utf8')) {
                    $newData[$k] = $v;
                }
            }
        }
    }
    $newArr = array();
    foreach ($newData as $v) {  //替换键值，与标题做对比
        $newArr[] = $v;
    }

    vendor("PHPExcel.PHPExcel");
    $objPHPExcel = new \PHPExcel();
    $objPHPExcel->getProperties();

    $key = ord("A");
    $key_s = ord("A");

    $str = array();
    foreach ($data['title'] as $k => $v) {
        $colum = chr($key);
        if($key>90  && $key_s<=90){
            $colum = 'A'.chr($key_s);
            $key_s++;
        }

        if (mb_strlen($newArr[$k], 'utf8') < mb_strlen($v, 'utf8')) {  //与标题比较的长度,获取生成excel每一列的最大长度
            $str[$colum] = $v;
        } else {
            $str[$colum] = $newArr[$k];
        }


        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
        $key += 1;
    }


    foreach ($str as $k => $v) {  //判断是否存在中文，产生最适合长度
        if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $v, $match)) {
            $width = mb_strlen($v, 'utf8');
            $strArr[$k] = $width >= 4 ? ($width >= 10 ? $width * 2.2 : $width * 2) : 12;
        } else {
            $strArr[$k] = mb_strlen($v, 'utf8');
        }

    }

    $column = 2;
    $objActSheet = $objPHPExcel->getActiveSheet();

    foreach ($data['data'] as $key => $rows) {
        $span = ord("A");
        $key_s = ord("A");
        foreach ($rows as $keyName => $value) {
            $j = chr($span);
            if($span>90 && $key_s<=90){
                $j = 'A'.chr($key_s);
                $key_s++;
            }
            $strArr[$j] = $strArr[$j] < 80 ? $strArr[$j]: 80;
            $objActSheet->getColumnDimension($j)->setWidth(($strArr[$j] + 1));  //设置每列的长度
            $objActSheet->setCellValue($j . $column, $value);  //赋值
            $span++;
        }
        $column++;
    }

    $fileName = iconv("utf-8", "gb2312", $fileName); //转码
    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output'); //文件通过浏览器下载
    return true;
}

/**
 * 获取当前完整路径
 * @return string
 */
function locationHref()
{
    $url = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != '80') {
        $url .= ":" . $_SERVER['SERVER_PORT'];
    }
    $url .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);
    return $url;
}



/*
*	取得当前的域名
*	@Author 9009123 (Lemonice)
*	@return string 当前的域名  如：http://www.baidu.com/
*/
function siteUrl()
{
    /* 协议 */
    $protocol = (is_ssl() ? 'https://' : 'http://');
    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = ':' . $_SERVER['SERVER_PORT'];
            if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                $port = '';
            }
        } else {
            $port = '';
        }

        if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'] . $port;
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            $host = $_SERVER['SERVER_ADDR'] . $port;
        }
    }

    return $protocol . (isset($host) && $host ? $host : '') . '/';
}

/*
*	检查目标文件夹是否存在，如果不存在则自动创建该目录
*	@Author 9009123 (Lemonice)
*	@param  string  $folder 目录路径。不能使用相对于网站根目录的URL
*	@return true or false
*/
function makeDir($folder)
{
    $reval = false;
    if (!file_exists($folder)) {
        /* 如果目录不存在则尝试创建该目录 */
        @umask(0);
        /* 将目录路径拆分成数组 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
        /* 如果第一个字符为/则当作物理路径处理 */
        $base = ($atmp[0][0] == '/') ? '/' : '';

        /* 遍历包含路径信息的数组 */
        foreach ($atmp[1] as $val) {
            if ('' != $val) {
                $base .= $val;
                if ('..' == $val || '.' == $val) {
                    /* 如果目录为.或者..则直接补/继续下一个循环 */
                    $base .= '/';
                    continue;
                }
            } else {
                continue;
            }

            $base .= '/';

            if (!file_exists($base)) {
                /* 尝试创建目录，如果创建失败则继续循环 */
                if (@mkdir(rtrim($base, '/'), 0755)) {
                    @chmod($base, 0755);
                    $reval = true;
                }
            }
        }
    } else {
        /* 路径已经存在。返回该路径是不是一个目录 */
        $reval = is_dir($folder);
    }
    clearstatcache();
    return $reval;
}

/**
 *    资源链接替换方法
 * @param $content    string 替换的内容
 * @param $suffix    mix  string/array 资源的后缀
 * @param $doamin    string 替换的资源域名
 */
function replaceHtml($content, $domain = '')
{
    preg_match_all('/<img.*?src=\"(.*?.*?)\".*?>/isu', $content, $src);  //抓取链接;
    $src = isset($src[1]) ? $src[1] : array();//print_r($src);
    if (!empty($src)) {
        $src = array_unique($src);  //去除重复
        foreach ($src as $path) {
            if (!strstr($path, 'http://') && !strstr($path, 'https://') && strstr($path, '/')) {
                $new_path = substr($path, 0, 1) == '/' ? substr($path, 1) : $path;
                $content = str_replace($path, $domain . $new_path, $content);
            }
        }
    }
    return $content;
}

/*
*	二维数组排序
*	@Author 9009123 (Lemonice)
*	@param array $array  二维数组
*	@param string $field 数组里面的字段，排序的字段
*	@param string $direction 倒序或者升序
*	@return array
*/
function arraySort($array = array(), $field = '', $direction = 'DESC')
{
    if (empty($array) || $field == '') {
        return $array;
    }
    $sort = array(
        'direction' => 'SORT_' . strtoupper($direction), //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
        'field' => $field,       //排序字段
    );
    $arrSort = array();
    foreach ($array AS $uniqid => $row) {
        foreach ($row AS $key => $value) {
            $arrSort[$key][$uniqid] = $value;
        }
    }
    if ($sort['direction']) {
        array_multisort($arrSort[$sort['field']], constant($sort['direction']), $array);
    }
    return $array;
}


/**
 * 统计在线人数
 * @param bool $isLogOut
 * @return array
 */
function totalOnline($isLogOut = false)
{
    $num = 0;
    $refTime = ini_get('session.gc_maxlifetime');
    $onLineFilePath = RUNTIME_PATH . 'online';
    $onlineNumber = 0;
    if (is_dir($onLineFilePath) && $dir = opendir($onLineFilePath)) {
        while (($file = readdir($dir)) !== false) {
            if (strcmp($file, "..") == 0 || strcmp($file, ".") == 0) {
                continue;
            }
            $time = date("Y-m-d H:i:s", filemtime($onLineFilePath . "/" . $file));
            $D_[$time] = $file;
            $num++;
            unset($num);
        }
        closedir($dir);
        $filename = session_id();
        $filePath = $onLineFilePath . "/" . $filename;
        if($isLogOut){
            unlink($filePath);
        }else{
            $fp = fopen($filePath, "w");
            fputs($fp, "");
            fclose($fp);
        }
        $nTime = date("Y-m-d H:i:s", mktime(date("H"), date("i") - $refTime, 0, date("m"), date("d"), date("Y")));
        $D_[$nTime] = "-";
        krsort($D_);
        $onlineAccount = array();
        while (1) {
            $vKey = key($D_);
            $onlineNumber++;
            if($D_[$vKey] != session_id()){
                $onlineAccount[] = $D_[$vKey];
            }
            if (strcmp($nTime, $vKey) == 0) {
                break;
            } else {
                array_shift($D_);
            }
        }
        array_shift($D_);
        reset($D_);
        while (count($D_) > 0) {
            $cKey = key($D_);
            unlink($onLineFilePath . "/" . $D_[$cKey]);
            if (!next($D_)) {
                break;
            }
        }
    } else {
        @chmod("..", 0777);
        @mkdir($onLineFilePath,0777);
    }
    $online = $onlineNumber-1;
    krsort($onlineAccount);
    array_shift($onlineAccount);
    $ret = array(
        'account'   => $onlineAccount,
        'total'     => $online
    );

    return $ret;
}

/**
 * 单设备登陆
 * @return bool
 */
function isLoginOnly(){
    $user_id = login('user_id');
    $session_key = F('session_key_'.$user_id);
    if(($session_key != session_id()) || (get_client_ip(0,true) != login('now_login_ip'))){
        return false;
    }
    return true;
}

/**
 * 生成随机名字
 * @return string
 */
function makeRandName(){
    return time() . mt_rand(100000000, 999999999);
}

