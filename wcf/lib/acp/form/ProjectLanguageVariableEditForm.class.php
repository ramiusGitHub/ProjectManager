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

use wcf\data\language\item\LanguageItemList;
use wcf\system\exception\IllegalLinkException;
use wcf\data\language\item\LanguageItemAction;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\data\project\ProjectEditor;
use wcf\form\AbstractForm;
use wcf\data\project\language\item\ProjectLanguageItemAction;

/**
 * Edit form for language variables.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectLanguageVariableEditForm extends ProjectLanguageVariableAddForm {
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'edit';
	
	/**
	 * @var \wcf\data\language\item\LanguageItemList
	 */
	public $languageItems;
	
	/**
	 * Reference values for update query
	 * 
	 * @var string
	 */ 
	public $refLanguageItemName;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Get languageItemName
		if(isset($_REQUEST['refLanguageItemName'])) {
			$this->refLanguageItemName = StringUtil::trim($_REQUEST['refLanguageItemName']);
		}
		
		// Get language items
		$this->languageItems = new LanguageItemList();
		$this->languageItems->getConditionBuilder()->add("languageItem = ?", array($this->refLanguageItemName));
		$this->languageItems->readObjects();
		
		// Validate refLanguageItemName
		if(!$this->languageItems->count()) {
			throw new IllegalLinkException();
		}
		
		// Set data
		foreach($this->languageItems as $languageItem) {
			foreach($languageItem->getData() as $col => $value) {
				if(property_exists($this, $col)) {
					$this->$col = $value;
				} elseif($col == 'languageItem') {
					$this->languageItemName = $value;
				} elseif($col == 'languageItemValue') {
					$this->languageItemValues[$languageItem->languageID] = $value;
				}
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();

		if(!isset($this->refLanguageItemName)) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		if($this->languageItemName != $this->refLanguageItemName) {
			parent::validateDuplicate();
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		// Do not call parent's save, because it creates
		// instead of updates language variables
		AbstractProjectDataForm::save();

		// Update category
		$this->saveCategory();
		
		// Update language items
		foreach($this->languageItems as $languageItem) {	
			$action = new ProjectLanguageItemAction(
				array($languageItem),
				'update',
				array(
					'data' => array(
						'languageItem' => $this->languageItemName,
						'languageItemValue' => $this->languageItemValues[$languageItem->languageID],
						'languageCategoryID' => $this->languageCategoryID,
						'packageID' => $this->packageID,
					)
				)
			);
			$action->executeAction();
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::addForwardParameters()
	 */
	protected function addForwardParameters() {
		parent::addForwardParameters();
		
		$this->forwardParameters[] = 'refLanguageItemName='.$this->languageItemName;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'refLanguageItemName' => $this->refLanguageItemName,
		));
	}
}
