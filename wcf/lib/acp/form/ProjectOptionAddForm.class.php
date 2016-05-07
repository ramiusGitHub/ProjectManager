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
use wcf\data\project\ProjectEditor;
use wcf\util\ProjectUtil;
use wcf\system\language\I18nHandler;

/**
 * Implementation of the abstract option form for options.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectOptionAddForm extends AbstractProjectOptionForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\option\ProjectOptionAction';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$type
	 */
	protected static $type = 'option';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$languageVariablePrefix
	 */
	public static $languageVariablePrefix = OPTION_LANGUAGE_ITEM_PREFIX;
	
	/**
	 * @var string
	 */
	public $optionValue = '';
	
	/**
	 * @var string
	 */
	public $selectOptions = '';
	
	/**
	 * @var boolean
	 */
	public $supportI18n = false;
	
	/**
	 * @var boolean
	 */
	public $requireI18n = false;
	
	/**
	 * @var boolean
	 */
	public $hidden = false;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Internal name of the option (has to be all lowercase)
		$this->optionName = mb_strtolower($this->optionName);
		
		// Option value
		if(isset($_POST['optionValue'])) {
			$this->optionValue = StringUtil::trim($_POST['optionValue']);
		}
		
		// Selectable option values
		if(isset($_POST['selectOptions'])) {
			$this->selectOptions = StringUtil::trim($_POST['selectOptions']);
		}
		
		// Option supports internationalization
		$this->supportI18n = isset($_POST['supportI18n']);
		
		// Option requires internationalization
		$this->requireI18n = isset($_POST['requireI18n']);
		
		// Option is hidden
		if(isset($_POST['hidden'])) {
			$this->showOrder = 0;
			$this->hidden = true;
			
			// Remove languageItems, because hidden options
			// do not have a displayable name or description
			$this->additionalFields['deleteLanguageItems'] = true;
		} else {
			$this->hidden = false;
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// If option is not hidden, a displayable option name is mandatory
		if(!$this->hidden) {
			if(!I18nHandler::getInstance()->validateValue('displayedName', true, false)) {
				$this->errorType['displayedName'] = 'multilingual';
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
				'optionValue' => $this->optionValue,
				'selectOptions' => $this->selectOptions,
				'hidden' => (int) $this->hidden,
				'supportI18n' => (int) $this->supportI18n,
				'requireI18n' => (int) $this->requireI18n
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::readOptionTypes()
	 */
	public function readOptionTypes() {
		$this->optionTypes = ProjectUtil::getClassListByNamespace(
			'system\option\\',
			'OptionType',
			false,
			'\wcf\system\option\IOptionType'
		);
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'optionValue' => $this->optionValue,
			'selectOptions' => $this->selectOptions,
			'supportI18n' => $this->supportI18n,
			'requireI18n' => $this->requireI18n,
			'hidden' => $this->hidden
		));
	}
}
