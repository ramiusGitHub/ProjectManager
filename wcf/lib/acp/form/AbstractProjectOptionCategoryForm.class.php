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

use wcf\util\StringUtil;
use wcf\data\project\ProjectEditor;
use wcf\system\WCF;
use wcf\data\project\ProjectAction;
use wcf\system\language\I18nHandler;

/**
 * Abstract form which handles all similarities among all types of option categories.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectOptionCategoryForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * A common template for all types of option categories.
	 * 
	 * @see \wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'projectOptionCategoryAdd';
	
	/**
	 * Field set by extending classes to differentiate between option category types.
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
	public $cacheBuilder = null;
	
	/**
	 * @var int
	 */
	public $categoryID = 0;
	
	/**
	 * @var string
	 */
	public $oldCategoryName = '';
	
	/**
	 * @var array<string>
	 */
	public $parentCategories = array();
	
	/**
	 * @var int
	 */
	public $parentCategoryID = 0;
	
	/**
	 * @var string
	 */
	public $categoryName = '';
	
	/**
	 * @var string
	 */
	public $parentCategoryName = '';
	
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
		
		// Get instance of option category type's cache builder
		$this->cacheBuilder = call_user_func(array('\wcf\system\cache\builder\\'.StringUtil::firstCharToUpperCase(static::$type).'CacheBuilder', 'getInstance'));
		
		// Register language variable
		I18nHandler::getInstance()->register('displayedName');
		
		if($this->objectID) {
			// Get ID of parent category
			if(!empty($this->parentCategoryName)) {
				$categories = $this->cacheBuilder->getData(array(), 'categories');
				
				if(isset($categories[$this->parentCategoryName])) {
					$this->parentCategoryID = $categories[$this->parentCategoryName]->categoryID;
				}
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Option category's displayed name
		I18nHandler::getInstance()->readValues();
		$this->additionalFields['languageItems'] = array(
			'name' => array(
				'languageItemValues' => I18nHandler::getInstance()->getValues('displayedName')
			)
		);
		
		// Parent category ID
		if(isset($_POST['parentCategoryID'])) {
			$this->parentCategoryID = intval($_POST['parentCategoryID']);
			
			if(isset($this->parentCategories[$this->parentCategoryID])) {
				$this->parentCategoryName = $this->parentCategories[$this->parentCategoryID];
			}
		}
		
		// Internal option category name
		if(isset($_POST['categoryName'])) {
			$this->categoryName = StringUtil::trim($_POST['categoryName']);
		}
		
		// Permissions needed to access options in this category
		if(isset($_POST['permissions'])) {
			$this->permissions = StringUtil::trim($_POST['permissions']);
		}
		
		// Options which have to be active to enable this category
		if(isset($_POST['options'])) {
			$this->options = StringUtil::trim($_POST['options']);
		}
		
		// Show order
		if(isset($_POST['showOrder'])) {
			$this->showOrder = intval($_POST['showOrder']);
		}
		
		// Automatic show order
		$this->autoShowOrder = isset($_POST['autoShowOrder']);
	}

	/**
	 * @see AbstractForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Category name
		if(empty($this->categoryName)) {
			$this->errorType['categoryName'] = 'empty';
		}
	}
	
	/**
	 * @see AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		parent::validateDuplicate();
		
		if($this->action == 'add' || $this->categoryName != $this->object->categoryName) {			
			$sql = "SELECT	*
				FROM	".call_user_func(array(call_user_func(array(static::$className, 'getBaseClassName')), 'getDatabaseTableName'))."
				WHERE	categoryName = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->categoryName));
			
			if($statement->getAffectedRows() > 0) {
				$this->errorType['categoryName'] = 'duplicate';
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
				'categoryName' => $this->categoryName,
				'parentCategoryName' => $this->parentCategoryName,
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
		// Read categories before calling parent in order to have
		// the categories and types available in the validation methods
		$this->readCategories();
		
		// Call parent
		parent::readData();

		// Init values of I18nHandler
		$className = static::$className;
		I18nHandler::getInstance()->setOptions(
			'displayedName',
			$this->packageID,
			$className::$categoryLanguageItemPrefix . '.' . $this->categoryName,
			''
		);
	}
	
	/**
	 * Reads the parent categories.
	 */
	public function readCategories() {
		$this->parentCategories = array(0 => '');
		$categories = $this->cacheBuilder->getData(array(), 'categories');
		$structure = $this->cacheBuilder->getData(array(), 'categoryStructure');
		
		$roots = array();
		foreach($categories as $category) {
			if($category->parentCategoryName == '') $roots[] = $category;
		}
		
		foreach($roots as $root) {
			$this->parentCategories[$root->categoryID] = $root->categoryName;
			
			if(isset($structure[$root->categoryName])) {
				foreach($structure[$root->categoryName] as $categoryName) {
					$this->parentCategories[$categories[$categoryName]->categoryID] = $categoryName;
				}
			}
		}
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
			'categoryID' => $this->categoryID,
			'parentCategories' => $this->parentCategories,
			'parentCategoryID' => $this->parentCategoryID,
			'oldCategoryName' => $this->oldCategoryName,
			'categoryName' => $this->categoryName,
			'parentCategoryName' => $this->parentCategoryName,
			'showOrder' => $this->showOrder,
			'autoShowOrder' => $this->autoShowOrder,
			'permissions' => str_replace(",", "\n", $this->permissions),
			'options' => $this->options
		));
	}
}
