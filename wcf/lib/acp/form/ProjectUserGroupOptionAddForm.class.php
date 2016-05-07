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

use wcf\data\project\ProjectEditor;
use wcf\util\ProjectUtil;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Implementation of the abstract option form for user group options.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserGroupOptionAddForm extends AbstractProjectOptionForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\user\group\option\ProjectUserGroupOptionAction';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$type
	 */
	protected static $type = 'userGroupOption';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$languageVariablePrefix
	 */
	public static $languageVariablePrefix = USER_GROUP_OPTION_LANGUAGE_ITEM_PREFIX;
	
	/**
	 * @var boolean
	 */
	public $usersOnly = false;
	
	/**
	 * @var string
	 */
	public $userDefaultValue = null;
	
	/**
	 * @var string
	 */
	public $modDefaultValue = null;
	
	/**
	 * @var string
	 */
	public $adminDefaultValue = null;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// The option is not available in the GUEST group
		$this->usersOnly = isset($_POST['usersOnly']);
		
		// The default value upon installation for the USER group
		if(isset($_POST['userDefaultValue']) && isset($_POST['useUserDefaultValue'])) {
			$this->userDefaultValue = StringUtil::trim($_POST['userDefaultValue']);
		}
		
		// The default value upon installation for the MOD group
		if(isset($_POST['modDefaultValue'])) {
			$this->modDefaultValue = StringUtil::trim($_POST['modDefaultValue']);
		}
		
		// The default value upon installation for the ADMIN group
		if(isset($_POST['adminDefaultValue'])) {
			$this->adminDefaultValue = StringUtil::trim($_POST['adminDefaultValue']);
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'usersOnly' => (int) $this->usersOnly,
				'userDefaultValue' => $this->userDefaultValue,
				'modDefaultValue' => $this->modDefaultValue,
				'adminDefaultValue' => $this->adminDefaultValue
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::readOptionTypes()
	 */
	public function readOptionTypes() {
		$this->optionTypes = ProjectUtil::getClassListByNamespace(
			'system\option\user\group\\',
			'UserGroupOptionType',
			false,
			'\wcf\system\option\user\group\IUserGroupOptionType'
		);
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'usersOnly' => $this->usersOnly,
			'userDefaultValue' => $this->userDefaultValue,
			'useUserDefaultValue' => $this->userDefaultValue !== null,
			'modDefaultValue' => $this->modDefaultValue,
			'useModDefaultValue' => $this->modDefaultValue !== null,
			'adminDefaultValue' => $this->adminDefaultValue,
			'useAdminDefaultValue' => $this->adminDefaultValue !== null,
		));
	}
}
