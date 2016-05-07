&#x003C;?php
// wcf imports
require_once(WCF_DIR.'lib/form/AbstractForm.class.php');

{include file='file_class_comment'} 
class {@$className} extends AbstractForm {
	public $templateName = '';
	
	/**
	 * @see AbstractPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
	}
		
	/**
	 * @see AbstractForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
	}
		
	/**
	 * @see AbstractForm::validate()
	 */
	public function validate() {
		parent::validate();
		
	}
		
	/**
	 * @see AbstractForm::save()
	 */
	public function save() {
		parent::save();
		
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