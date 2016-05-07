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

use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\system\language\I18nHandler;

/**
 * Abstract form which handles all similarities among all types of menu items.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectMenuItemForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * A prefix of the name of the menu item's language items.
	 * 
	 * @var string
	 */
	public static $languageVariablePrefix = "";
	
	/**
	 * A common template for all types of menu items.
	 * 
	 * @see \wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'projectMenuItemAdd';
	
	/**
	 * Field set by extending classes to differentiate between menus.
	 * 
	 * @var string
	 */
	protected static $type = '';
	
	/**
	 * @var string
	 */
	public $menuItem = '';
	
	/**
	 * @var string
	 */
	public $permissions = '';
	
	/**
	 * @var string
	 */
	public $options = '';
	
	/**
	 * @var integer
	 */
	public $showOrder = 0;
	
	/**
	 * @var boolean
	 */
	public $autoShowOrder = false;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Register language variable
		I18nHandler::getInstance()->register('displayedName');
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Item's displayed name
		I18nHandler::getInstance()->readValues();
		$this->additionalFields['languageItems'] = array(
			'name' => array(
				'languageItemValues' => I18nHandler::getInstance()->getValues('displayedName')
			)
		);
		
		// Varchar parameters
		if(isset($_POST['menuItem'])) {
			$this->menuItem = StringUtil::trim($_POST['menuItem']);
		}
		
		// Text parameters
		if(isset($_POST['permissions'])) {
			$this->permissions = StringUtil::trim($_POST['permissions']);
		}
		
		if(isset($_POST['options'])) {
			$this->options = StringUtil::trim($_POST['options']);
		}
		
		// Integer parameters
		if(isset($_POST['showOrder'])) {
			$this->showOrder = intval($_POST['showOrder']);
		}
		
		// Boolean parameters
		$this->autoShowOrder = intval(isset($_POST['autoShowOrder']));
	}

	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Menu item
		if(empty($this->menuItem)) {
			$this->errorType['menuItem'] = 'empty';
		}

		// Displayed item name
		if(!I18nHandler::getInstance()->validateValue('displayedName', true, false)) {
			$this->errorType['displayedName'] = 'multilingual';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		parent::validateDuplicate();
		
		if($this->action == 'add' || $this->menuItem != $this->object->menuItem) {
			$sql = "SELECT	menuItem
				FROM	".call_user_func(array(call_user_func(array(static::$className, 'getBaseClassName')), 'getDatabaseTableName'))."
				WHERE	menuItem COLLATE utf8_bin = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->menuItem));
			
			if($statement->getAffectedRows() > 0) {
				$this->errorType['menuItem'] = 'duplicate';
			}
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'menuItem' => $this->menuItem,
				'showOrder' => $this->showOrder,
				'autoShowOrder' => (int) $this->autoShowOrder,
				'permissions' => str_replace(array("\r\n", "\r", "\n"), ",", $this->permissions),
				'options' => $this->options
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		// Init values of I18nHandler
		I18nHandler::getInstance()->setOptions(
			'displayedName',
			$this->packageID,
			static::$languageVariablePrefix . $this->menuItem,
			''
		);
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// Assign language variables
		I18nHandler::getInstance()->assignVariables(!empty($_POST));

		WCF::getTPL()->assign(array(
			'type' => static::$type,
			'itemID' => $this->objectID,
			'menuItem' => $this->menuItem,
			'showOrder' => $this->showOrder,
			'autoShowOrder' => $this->autoShowOrder,
			'permissions' => str_replace(",", "\n", $this->permissions),
			'options' => $this->options
		));
	}
}
