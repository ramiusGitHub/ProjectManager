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

use wcf\system\request\LinkHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\data\option\Option;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\system\application\ApplicationHandler;
use wcf\data\package\PackageCache;
use wcf\util\FileUtil;
use wcf\util\ClassUtil;
use wcf\data\project\ProjectEditor;
use wcf\data\project\ProjectAction;
use wcf\system\language\I18nHandler;

/**
 * Abstract form which handles all similarities among all types of options.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectOptionForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * A common template for all types of options.
	 * 
	 * @see \wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'projectOptionAdd';
	
	/**
	 * Field set by extending classes to differentiate between option types.
	 * 
	 * @var string
	 */
	protected static $type = '';
	
	/**
	 * @var string
	 */
	public static $languageVariablePrefix = '';
	
	/**
	 * @var \wcf\system\cache\builder\ICacheBuilder
	 */
	public $cacheBuilder;
	
	/**
	 * @var array<string>
	 */
	public $optionCategories = array();
	
	/**
	 * @var int
	 */
	public $optionCategoryID = 0;
	
	/**
	 * @var array<string>
	 */
	public $optionTypes = array();
	
	/**
	 * @var string
	 */
	public $optionName = '';
	
	/**
	 * @var string
	 */
	public $categoryName = '';
	
	/**
	 * @var string
	 */
	public $optionType = '';
	
	/**
	 * @var string
	 */
	public $defaultValue = '';
	
	/**
	 * @var string
	 */
	public $validationPattern = '';
	
	/**
	 * @var string
	 */
	public $enableOptions = '';
	
	/**
	 * @var string
	 */
	public $permissions = '';
	
	/**
	 * @var string
	 */
	public $options = '';
	
	/**
	 * @var array<string>
	 */
	public $additionalData = array();
	
	/**
	 * @var integer
	 */
	public $showOrder = 0;
	
	/**
	 * @var boolean
	 */
	public $autoShowOrder = false;
	
	/**
	 * @see \wcf\page\AbstractPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Get instance of option type's cache builder
		$this->cacheBuilder = call_user_func(array('\wcf\system\cache\builder\\'.StringUtil::firstCharToUpperCase(static::$type).'CacheBuilder', 'getInstance'));
		
		// Read categories and option types
		$this->readCategories();
		$this->readOptionTypes();
		
		// Register language variables
		I18nHandler::getInstance()->register('displayedName');
		I18nHandler::getInstance()->register('displayedDescription');
		
		if($this->objectID) {
			// Upper case first letter of type
			$this->optionType = ucfirst($this->optionType);
			
			// Get ID of option's category
			$categories = $this->cacheBuilder->getData(array(), 'categories');
			
			if(isset($categories[$this->categoryName])) {
				$this->optionCategoryID = $categories[$this->categoryName]->categoryID;
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Option's displayed name and description
		I18nHandler::getInstance()->readValues();
		$this->additionalFields['languageItems'] = array(
			'name' => array(
				'languageItemValues' => I18nHandler::getInstance()->getValues('displayedName')
			),
			'description' => array(
				'languageItemValues' => I18nHandler::getInstance()->getValues('displayedDescription')
			)
		);
		
		// Category
		if(isset($_POST['optionCategoryID'])) {
			$this->optionCategoryID = intval($_POST['optionCategoryID']);
			$this->readCategoryName();
		}
		
		// Internal option name
		if(isset($_POST['optionName'])) {
			$this->optionName = StringUtil::trim($_POST['optionName']);
		}
		
		// Option type
		if(isset($_POST['optionType'])) {
			$this->optionType = StringUtil::trim($_POST['optionType']);
		}
		
		// The default value upon installation
		if(isset($_POST['defaultValue'])) {
			$this->defaultValue = StringUtil::trim($_POST['defaultValue']);
		}
		
		// Validation pattern to validate option value
		if(isset($_POST['validationPattern'])) {
			$this->validationPattern = StringUtil::trim($_POST['validationPattern']);
		}
		
		// Options enabled by this option
		if(isset($_POST['enableOptions'])) {
			$this->enableOptions = StringUtil::unifyNewlines(StringUtil::trim($_POST['enableOptions']));
		}
		
		// Permissions needed to change this option
		if(isset($_POST['permissions'])) {
			$this->permissions = StringUtil::trim($_POST['permissions']);
		}
		
		// Options which have to be active to enable this option
		if(isset($_POST['options'])) {
			$this->options = StringUtil::trim($_POST['options']);
		}
		
		// Additional data
		if(isset($_POST['additionalData']) && !empty($_POST['additionalData'])) {
			try {
				$this->additionalData = array();
				
				foreach(preg_split("/(\r\n)+|(\n|\r)+/", $_POST['additionalData'], -1,  PREG_SPLIT_NO_EMPTY) as $row) {
					$parts = explode(":", $row);
					$this->additionalData[StringUtil::trim($parts[0])] = StringUtil::trim($parts[1]);
				} 
			} catch (Exception $e) {
				$this->errorType['additionalData'] = 'format';
			}
		}
		
		// Show order
		if(isset($_POST['showOrder'])) {
			$this->showOrder = intval($_POST['showOrder']);
		}
		
		// Automatic show order
		$this->autoShowOrder = isset($_POST['autoShowOrder']);
	}
	
	/**
	 * Reads the category's name based on the given optionCategoryID parameter.
	 */
	public function readCategoryName() {
		foreach($this->optionCategories as $parentCategoryName => $childCategories) {
			if(isset($childCategories[$this->optionCategoryID])) {
				$this->categoryName = $childCategories[$this->optionCategoryID];
				break;
			}
		}
	}

	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Option name
		if(empty($this->optionName)) $this->errorType['optionName'] = 'empty';
	 	elseif(strlen($this->optionName) > PROJECT_MANAGER_NAME_MAX_LENGTH) {
	 		$this->errorType['optionName'] = 'tooLong';
		} elseif(!preg_match(PROJECT_MANAGER_OPTION_NAME_REGEX, $this->optionName)) {
			$this->errorType['optionName'] = 'invalid';
		}
		
		// Category
		if(empty($this->categoryName)) {
			$this->errorType['categoryName'] = 'empty';
		}
		
		// Option type
		$this->validateOptionType();
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		parent::validateDuplicate();
		
		if($this->action == 'add' || $this->optionName != $this->object->optionName) {
			$sql = "SELECT	*
				FROM	".call_user_func(array(call_user_func(array(static::$className, 'getBaseClassName')), 'getDatabaseTableName'))."
				WHERE	optionName COLLATE utf8_bin = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->optionName));
			
			if($statement->getAffectedRows() > 0) {
				$this->errorType['optionName'] = 'duplicate';
			}
		}
	}
	
	/**
	 * Validates the selected optionType.
	 */
	public function validateOptionType() {
		$isValid = false;
		foreach($this->optionTypes as $application => $optionTypes) {
			if(isset($optionTypes[$this->optionType])) {
				$isValid = true;
				break;
			}
		}
		
		if(!$isValid) $this->errorType['optionType'] = 'invalid';
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'optionName' => $this->optionName,
				'categoryName' => $this->categoryName,
				'optionType' => $this->optionType,
				'defaultValue' => $this->defaultValue,
				'validationPattern' => $this->validationPattern,
				'enableOptions' => $this->enableOptions,
				'showOrder' => $this->showOrder,
				'autoShowOrder' => (int) $this->autoShowOrder,
				'permissions' => str_replace(array("\r\n", "\r", "\n"), ",", $this->permissions),
				'options' => $this->options,
				'additionalData' => serialize($this->additionalData),
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
			static::$languageVariablePrefix . '.' . $this->optionName,
			''
		);
		
		I18nHandler::getInstance()->setOptions(
			'displayedDescription',
			$this->packageID,
			static::$languageVariablePrefix.'.'.$this->optionName.'.description',
			''
		);
	}
	
	/**
	 * Reads the option categories.
	 */
	public function readCategories() {
		$this->optionCategories = array();
		$categories = $this->cacheBuilder->getData(array(), 'categories');
		$structure = $this->cacheBuilder->getData(array(), 'categoryStructure');
		
		foreach($structure as $parentCategoryName => $childCategoryNames) {
			if(!empty($parentCategoryName)) {
				if(!isset($this->optionCategories[$parentCategoryName])) {
					$this->optionCategories[$parentCategoryName] = array();
				}
				
				foreach($childCategoryNames as $categoryName) {
					$this->optionCategories[$parentCategoryName][$categories[$categoryName]->categoryID] = $categoryName;
				}
			}
		}
	}
	
	/**
	 * Reads the available option types.
	 */
	abstract public function readOptionTypes();
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// Assign language variables
		I18nHandler::getInstance()->assignVariables(!empty($_POST));

		WCF::getTPL()->assign(array(
			'type' => static::$type,
			'optionID' => $this->objectID,
			'optionCategories' => $this->optionCategories,
			'optionCategoryID' => $this->optionCategoryID,
			'optionTypes' => $this->optionTypes,
			'optionName' => $this->optionName,
			'categoryName' => $this->categoryName,
			'optionType' => $this->optionType,
			'defaultValue' => $this->defaultValue,
			'validationPattern' => $this->validationPattern,
			'enableOptions' => $this->enableOptions,
			'showOrder' => $this->showOrder,
			'autoShowOrder' => $this->autoShowOrder,
			'permissions' => str_replace(",", "\n", $this->permissions),
			'options' => $this->options,
			'additionalData' => $this->outputAdditionalData($this->additionalData),
		));
	}
	
	/**
	 * Returns string representation of an array for a textarea.
	 * 
	 * @param array<mixed> $array
	 * @return string $output
	 */
	protected function outputAdditionalData($array) {
		if(empty($array)) return '';
		
		$output = array();
		foreach($array as $key => $value) {
			$output[] = $key . ":" . $value;
		}
		
		return implode("\n", $output);
	}
}
