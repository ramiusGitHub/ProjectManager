&#x003C;?php
//initialize package array
$packageDirs = array();

//include config
require_once(dirname(__FILE__).'/config.inc.php');

//include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if(!count($packageDirs)) $packageDirs[] = {@$prefix}_DIR;
$packageDirs[] = WCF_DIR;

//starting application
require_once({@$prefix}_DIR.'lib/system/{@$prefix}Core.class.php');
new {@$prefix}Core();