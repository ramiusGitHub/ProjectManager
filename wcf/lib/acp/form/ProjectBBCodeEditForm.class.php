<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) <2016> <Haas Webdesign>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace wcf\acp\form;

use wcf\util\ProjectUtil;
use wcf\system\language\I18nHandler;
use wcf\data\bbcode\attribute\BBCodeAttributeAction;
use wcf\system\WCF;

/**
 * Edit form for BBCodes.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 * @see \wcf\acp\form\BBCodeAddForm
 */
class ProjectBBCodeEditForm extends ProjectBBCodeAddForm {
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'edit';
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		$this->bbcodeClassName = $this->object->className;
	}

	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		AbstractProjectDatabaseObjectForm::save();
		
		if($this->showButton) {
			$this->buttonLabel = 'wcf.bbcode.buttonLabel'.$this->objectID;
			if(I18nHandler::getInstance()->isPlainValue('buttonLabel')) {
				I18nHandler::getInstance()->remove($this->buttonLabel);
				$this->buttonLabel = I18nHandler::getInstance()->getValue('buttonLabel');
			} else {
				I18nHandler::getInstance()->compareAndSave('buttonLabel', $this->buttonLabel, 'wcf.bbcode', $this->packageID);
			}
		}
		
		// clear existing attributes
		$sql = "DELETE FROM	wcf".WCF_N."_bbcode_attribute
			WHERE		bbcodeID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->bbcodeID));
		
		foreach($this->attributes as $attribute) {
			$attributeAction = new BBCodeAttributeAction(array(), 'create', array('data' => array(
				'bbcodeID' => $this->bbcodeID,
				'attributeNo' => $attribute->attributeNo,
				'attributeHtml' => $attribute->attributeHtml,
				'validationPattern' => $attribute->validationPattern,
				'required' => $attribute->required,
				'useText' => $attribute->useText,
			)));
			$attributeAction->executeAction();
		}
	}
}