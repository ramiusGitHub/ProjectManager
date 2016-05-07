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

use wcf\data\user\option\UserOptionAction;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\data\project\ProjectEditor;
use wcf\util\ProjectUtil;

/**
 * Implementation of the abstract option form for user options.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserOptionAddForm extends AbstractProjectOptionForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\user\option\ProjectUserOptionAction';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$type
	 */
	protected static $type = 'userOption';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionForm::$languageVariablePrefix
	 */
	public static $languageVariablePrefix = USER_OPTION_LANGUAGE_ITEM_PREFIX;
	
	/**
	 * @var array<string>
	 */
	public $outputClasses = array();
	
	/**
	 * @var string
	 */
	public $outputClass = '';
	
	/**
	 * @var string
	 */
	public $selectOptions = '';
	
	/**
	 * @var integer
	 */
	public $editable = 1;
	
	/**
	 * @var integer
	 */
	public $visible = 1;
	
	/**
	 * @var boolean
	 */
	public $required = false;
	
	/**
	 * @var boolean
	 */
	public $askDuringRegistration = false;
	
	/**
	 * @var boolean
	 */
	public $searchable = false;
	
	/**
	 * @var boolean
	 */
	public $defaultIsDisabled = false;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// varchar parameters
		if(isset($_POST['outputClass']) && is_string($_POST['outputClass'])) $this->outputClass = StringUtil::trim($_POST['outputClass']);
		
		// integer parameters
		if(isset($_POST['editable'])) $this->editable = intval($_POST['editable']);
		if(isset($_POST['visible'])) $this->visible = intval($_POST['visible']);
		
		// boolean parameters		
		if(isset($_POST['required'])) $this->required = true;
		else $this->required = false;
		if(isset($_POST['askDuringRegistration'])) $this->askDuringRegistration = true;
		else $this->askDuringRegistration = false;
		if(isset($_POST['searchable'])) $this->searchable = true;
		else $this->searchable = false;
		if(isset($_POST['defaultIsDisabled'])) $this->defaultIsDisabled = true;
		else $this->defaultIsDisabled = false;
		
		// text parameters
		if(isset($_POST['selectOptions'])) $this->selectOptions = StringUtil::trim($_POST['selectOptions']);
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Editable
		if($this->editable < 0 || $this->editable > 3) {
			$this->errorType['editable'] = 'outOfBounds';
		}
		
		// Visible
		if($this->visible < 0 || $this->visible > 15) {
			$this->errorType['visible'] = 'outOfBounds';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'outputClass' => $this->outputClass,
				'selectOptions' => $this->selectOptions,
				'editable' => $this->editable,
				'visible' => $this->visible,
				'required' => intval($this->required),
				'askDuringRegistration' => intval($this->askDuringRegistration),
				'searchable' => intval($this->searchable),
				'defaultIsDisabled' => intval($this->defaultIsDisabled)
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// Read option outputs before calling parent
		// in order to have the outputs available in
		// the validation methods
		$this->outputClasses = array_merge(
			array(
				'' => WCF::getLanguage()->get('wcf.project.userOption.noOutputClass')
			),
			ProjectUtil::getClassListByNamespace(
				'system\option\user\\',
				'UserOptionOutput',
				false,
				'\wcf\system\option\user\IUserOptionOutput'
			)
		);
		
		// Call parent
		parent::readData();
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
	 * @see AbstractProjectOptionForm::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'outputClasses' => $this->outputClasses,
			'outputClass' => $this->outputClass,
			'selectOptions' => $this->selectOptions,
			'editable' => $this->editable,
			'visible' => $this->visible,
			'required' => $this->required,
			'askDuringRegistration' => $this->askDuringRegistration,
			'searchable' => $this->searchable,
			'defaultIsDisabled' => $this->defaultIsDisabled
		));
	}
}
