<?php

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
(function () {
	/**
	 * @Description：反腾讯网址安全检测系统
	 * @Author：易航
	 * @Link：http://blog.bri6.cn
	 */
	if (Helper::options()->JTencentProtect == 'off')
		return;
	$spider = [
		'Baiduspider', // 百度搜索爬虫
		'360Spider', // 360搜索爬虫
		'YisouSpider',
		'Sogou web spider',
		'Sogou inst spider',
		'Googlebot/', // 谷歌搜索爬虫
		'bingbot/', // 必应搜索爬虫
		'Bytespider'
	];
	foreach ($spider as $value) {
		if (strpos($_SERVER['HTTP_USER_AGENT'], $value) !== false)
			return;
	}
	if (!function_exists('real_ip')) {
		function real_ip()
		{
			$ip = $_SERVER['REMOTE_ADDR'];
			if (isset ($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
				$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
			} elseif (isset ($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (isset ($_SERVER['HTTP_X_FORWARDED_FOR']) and preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
				foreach ($matches[0] as $xip) {
					if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
						$ip = $xip;
						break;
					}
				}
			}
			return $ip;
		}
	}
	if (!empty ($_SERVER['HTTP_REFERER'])) {
		$refere = [
			'.tr.com',
			'.wsd.com',
			'.oa.com',
			'.cm.com',
			'/membercomprehensive/',
			'www.internalrequests.org'
		];
		foreach ($refere as $value) {
			if (strpos($_SERVER['HTTP_REFERER'], $value) !== false)
				$_SESSION['txprotectblock'] = true;
		}
	}
	//IP屏蔽
	$iptables = '977012992~977013247|977084416~977084927|1743654912~1743655935|1949957632~1949958143|2006126336~2006127359|2111446272~2111446527|3418570752~3418578943|3419242496~3419250687|3419250688~3419275263|3682941952~3682942207|3682942464~3682942719|3682986660~3682986663|1707474944~1707606015|1709318400~1709318655|1884967642|1884967620|1893733510|1709332858|1709325774|1709342057|1709341968|1709330358|1709335492|1709327575|1709327041|1709327557|1709327573|1975065457|1902908741|1902908705|3029946827|236000818';
	$remoteIpLong = ip2long(real_ip());
	foreach (explode('|', $iptables) as $ipRows) {
		$ipBanRange = explode('~', $ipRows);
		if(count($ipBanRange)==1){
			if ($remoteIpLong == $ipRows) {
				exit ('Click to continue!' . date('Y-m-d'));
			}
		}else{
			if ($remoteIpLong >= $ipBanRange[0] && $remoteIpLong <= $ipBanRange[1])	{
				exit ('Click to continue!' . date('Y-m-d'));
			}
		}		
	}
	//HEADER特征屏蔽
	if (!isset ($_SERVER['HTTP_ACCEPT']) || empty ($_SERVER['HTTP_USER_AGENT']) || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "manager") !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'ozilla') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'Mozilla') === false || strpos($_SERVER['HTTP_USER_AGENT'], "Windows NT 6.1") !== false && $_SERVER['HTTP_ACCEPT'] == '*/*' || strpos($_SERVER['HTTP_USER_AGENT'], "Windows NT 5.1") !== false && $_SERVER['HTTP_ACCEPT'] == '*/*' || strpos($_SERVER['HTTP_ACCEPT'], "vnd.wap.wml") !== false && strpos($_SERVER['HTTP_USER_AGENT'], "Windows NT 5.1") !== false || isset ($_COOKIE['ASPSESSIONIDQASBQDRC']) || strpos($_SERVER['HTTP_USER_AGENT'], "Alibaba.Security.Heimdall") !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'libcurl/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'python') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Go-http-client') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'HeadlessChrome') !== false || @$_SESSION['txprotectblock'] == true) {
		exit ('Click to continue!' . date('Y-m-d'));
	}
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'Coolpad Y82-520') !== false && $_SERVER['HTTP_ACCEPT'] == '*/*' || strpos($_SERVER['HTTP_USER_AGENT'], 'Mac OS X 10_12_4') !== false && $_SERVER['HTTP_ACCEPT'] == '*/*' || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'baiduboxapp/') === false && $_SERVER['HTTP_ACCEPT'] == '*/*' || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false && $_SERVER['HTTP_ACCEPT'] == '*/*' || strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'en') !== false && strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'zh') === false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'en-') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'zh') === false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone OS 9_1') !== false && $_SERVER['HTTP_CONNECTION'] == 'close') {
		exit ('您当前浏览器不支持或操作系统语言设置非中文，无法访问本站！');
	}
})();