<?php

if ( ! defined('AUTHORS_STATS_ADDON_NAME'))
{
	define('AUTHORS_STATS_ADDON_NAME',         'Authors Stats');
	define('AUTHORS_STATS_ADDON_VERSION',      '0.1');
}

$config['name']=AUTHORS_STATS_ADDON_NAME;
$config['version']=AUTHORS_STATS_ADDON_VERSION;

$config['nsm_addon_updater']['versions_xml']='https://raw.github.com/intoeetive/authors_stats/master/README';