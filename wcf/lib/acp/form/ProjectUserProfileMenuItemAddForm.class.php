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

/**
 * Implementation of the menu item form for user profile menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserProfileMenuItemAddForm extends AbstractProjectMenuItemForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$LANGUAGE_ITEM_PREFIX
	 */
	public static $languageVariablePrefix = USER_PROFILE_MENU_ITEM_LANGUAGE_ITEM_PREFIX;
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\user\profile\menu\item\ProjectUserProfileMenuItemAction';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$type
	 */
	protected static $type = 'userProfileMenuItem';
	
	/**
	 * @var string
	 */
	public $itemClassName = '';
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Class name
		if(isset($_POST['className'])) {
			$this->itemClassName = StringUtil::trim($_POST['className']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		AbstractProjectMenuItemForm::validate();
		
		// Item class name
		if(empty($this->itemClassName)) {
			$this->errorType['className'] = 'empty';
		} elseif(!class_exists($this->itemClassName)) {
			$this->errorType['className'] = 'nonExistent';
		} else {
			$class = new \ReflectionClass($this->itemClassName);
			if(!$class->implementsInterface("wcf\system\menu\user\profile\content\IUserProfileMenuContent")) {
				$this->errorType['className'] = 'interface';
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
				'className' => $this->itemClassName
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'className' => $this->itemClassName
		));
	}
}
