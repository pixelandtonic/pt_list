<?php

if (! defined('PT_LIST_NAME'))
{
	define('PT_LIST_NAME', 'P&amp;T List');
	define('PT_LIST_VER',  '1.0.3');
}

$config['name']    = PT_LIST_NAME;
$config['version'] = PT_LIST_VER;
$config['nsm_addon_updater']['versions_xml'] = 'http://pixelandtonic.com/ee/releasenotes.rss/pt_list';
