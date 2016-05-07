&#x003C;?php
require_once({@$prefix}_DIR.'lib/system/session/{@$prefix}Session.class.php');
require_once({@$prefix}_DIR.'lib/data/user/{@$prefix}UserSession.class.php');
require_once({@$prefix}_DIR.'lib/data/user/{@$prefix}GuestSession.class.php');
require_once(WCF_DIR.'lib/system/session/CookieSessionFactory.class.php');

{include file='file_class_comment'}
class {@$prefix}SessionFactory extends CookieSessionFactory {
	protected $guestClassName = '{@$prefix}GuestSession';
	protected $userClassName = '{@$prefix}UserSession';
	protected $sessionClassName = '{@$prefix}Session';
}