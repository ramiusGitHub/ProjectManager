&#x003C;?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');

{include file='file_class_comment'} 
class {@$className} extends AbstractPage {
	public $templateName = '';
	
	/**
	 * @see AbstractPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
	}

	/**
	 * @see AbstractPage::readData()
	 */
	public function readData() {
		parent::readData();
		
	}
	
	/**
	 * @see AbstractPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			
		));
	}
	
	/**
	 * @see AbstractPage::show()
	 */
	public function show() {
		parent::show();
		
	}
}