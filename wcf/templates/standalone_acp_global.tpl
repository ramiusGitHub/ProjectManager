&#x003C;?php
// define paths
define('RELATIVE_{@$prefix}_DIR', '../');
//initialize package array
$packageDirs = array();

//include config
require_once(dirname(dirname(__FILE__)).'/config.inc.php');

//include WCF
require_once(RELATIVE_WCF_DIR.'global.php');
if(!count($packageDirs)) $packageDirs[] = {@$prefix}_DIR;
$packageDirs[] = WCF_DIR;

// starting acp
require_once({@$prefix}_DIR.'lib/system/{@$prefix}ACP.class.php');
new {@$prefix}ACP();