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

use wcf\system\cache\builder\LanguageCacheBuilder;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\data\language\item\LanguageItemAction;
use wcf\data\language\category\LanguageCategoryAction;
use wcf\data\project\ProjectEditor;
use wcf\system\language\LanguageFactory;
use wcf\form\AbstractForm;
use wcf\system\Regex;
use wcf\data\package\PackageEditor;
use wcf\data\project\language\item\ProjectLanguageItemAction;

/**
 * Implementation of the data form for language variables.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectLanguageVariableAddForm extends AbstractProjectDataForm {
	/**
	 * @var array<\wcf\data\language\Language>
	 */
	public $languages = array();
	
	/**
	 * @var array<string>
	 */
	public $languageCategories = array();
	
	/**
	 * @var integer
	 */
	public $languageCategoryID = 0;
	
	/**
	 * @var string
	 */
	public $newLanguageCategory = '';
	
	/**
	 * @var string
	 */
	public $languageItemName = '';
	
	/**
	 * @var array<string>
	 */
	public $languageItemValues = array();

	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// languages
		$this->languages = LanguageFactory::getInstance()->getLanguages();
		
		// language categories
		$this->languageCategories = $this->languageCategories = LanguageCacheBuilder::getInstance()->getData(array(), 'categoryIDs');
		
		// parameters
		if(isset($_GET['languageItemName'])) $this->languageItemName = StringUtil::trim($_GET['languageItemName']);
		if(isset($_GET['languageCategory'])) {
			$category = StringUtil::trim($_GET['languageCategory']);
			if(in_array($category, $this->languageCategories)) $this->languageCategoryID = array_search($category, $this->languageCategories);
			else $this->newLanguageCategory = $category;
		}
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Language item name
		if(isset($_POST['languageItemName'])) {
			$this->languageItemName = StringUtil::trim($_POST['languageItemName']);
		}
		
		// Language item values
		if(isset($_POST['languageItemValues']) && is_array($_POST['languageItemValues'])) {
			$this->languageItemValues = $_POST['languageItemValues'];
		}
		
		// Escaping backslashes
		// @see https://github.com/WoltLab/WCF/pull/1893
		// TODO remove; also in assignVariables()
		foreach($this->languageItemValues as $languageID => $value) {
			$this->languageItemValues[$languageID] = str_replace('\\', '\\\\', $value);
		}
		
		// Language category
		$categoryName = '';
		if(isset($_POST['languageCategoryID'])) $this->languageCategoryID = intval($_POST['languageCategoryID']);
		if(isset($_POST['newLanguageCategory'])) {
			$this->newLanguageCategory = StringUtil::trim($_POST['newLanguageCategory']);
			$categoryName = $this->newLanguageCategory;
		}
			
		if(empty($categoryName)) {
			$category = LanguageFactory::getInstance()->getCategoryByID($this->languageCategoryID);
			
			if($category !== null) {
				$categoryName = $category->languageCategory;
			}
		}
		
		
		if(!empty($categoryName) && StringUtil::indexOfIgnoreCase($this->languageItemName, $categoryName) === false) {
			$this->languageItemName = $categoryName . ($this->languageItemName ? '.'.$this->languageItemName : '');
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Language category
		if(empty($this->newLanguageCategory)) {
			$category = LanguageFactory::getInstance()->getCategoryByID($this->languageCategoryID);
			
			if($category === null) {
				$this->errorType['languageCategoryID'] = 'empty';
			}
		} else {
			// Language category syntax
			if(!preg_match('/[a-z0-9_]+(?:\.[a-z0-9_]+){1,2}/i', $this->newLanguageCategory)) {
				$this->errorType['newLanguageCategory'] = 'invalid';
			}
		}
		
		// Language item
		if(empty($this->languageItemName)) {
			$this->errorType['languageItemName'] = 'empty';
		}
		
		// Language item syntax
		if(!preg_match('/[a-z0-9_]+(?:\.[a-z0-9_]+){2,}/i', $this->languageItemName)) {
			$this->errorType['languageItemName'] = 'invalid';
		}
		
		// Language item values
		$regex = Regex::compile("\]\]\>");
		foreach($this->languageItemValues as $languageID => $value) {
			if($regex->match($value)) $this->errorType['languageItemValues'][$languageID] = 'closingCdata';
		}
	}

	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_language_item
			WHERE	languageItem COLLATE utf8_bin = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->languageItemName));
		
		if($statement->getAffectedRows() > 0) {
			$this->errorType['languageItemName'] = 'duplicate';
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// Save category
		$this->saveCategory();

		// Save language items
		foreach($this->languageItemValues as $languageID => $value) {
			$action = new ProjectLanguageItemAction(
				array(),
				'create',
				array(
					'data' => array_merge(
						$this->additionalFields,
						array(
							'languageID' => $languageID,
							'languageItem' => $this->languageItemName,
							'languageItemValue' => $value,
							'languageItemOriginIsSystem' => 1,
							'languageCategoryID' => $this->languageCategoryID,
							'packageID' => $this->packageID,
						)
					)
				)
			);
			$action->executeAction();
		}
	}
	
	/**
	 * Checks whether the entered new category already
	 * exists and creates a new category if necessary.
	 */
	public function saveCategory() {
		if(!empty($this->newLanguageCategory)) {
			// Get category
			$category = LanguageFactory::getInstance()->getCategory($this->newLanguageCategory);
			
			if($category === null) {
				// Category does not exist
				// Create new category
				$action = new LanguageCategoryAction(
					array(),
					'create',
					array(
						'data' => array(
							'languageCategory' => $this->newLanguageCategory
						)
					)
				);
				$action->executeAction();
				$category = $action->getReturnValues()['returnValues'];
				
				$this->languageCategoryID = $category->languageCategoryID;
				
				// Add new language category to array of categories
				if(!empty($this->newLanguageCategory)) {
					$this->languageCategories[$this->languageCategoryID] = $this->newLanguageCategory;
					asort($this->languageCategories);
				}
			}
			
			// Get ID of language category
			$this->languageCategoryID = $category->languageCategoryID;
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::addForwardParameters()
	 */
	protected function addForwardParameters() {
		parent::addForwardParameters();
		
		$this->forwardParameters[] = 'languageCategory='.$this->languageCategories[$this->languageCategoryID];
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$values = array();
		// Escaping backslashes
		// @see https://github.com/WoltLab/WCF/pull/1893
		// TODO remove; also in readFormParamters()
		foreach($this->languageItemValues as $languageID => $value) {
			$values[$languageID] = str_replace('\\\\', '\\', $value);
		}
		
		WCF::getTPL()->assign(array(
			'languageCategoryID' => $this->languageCategoryID,
			'newLanguageCategory' => $this->newLanguageCategory,
			'languageItemName' => $this->languageItemName,
			'languageItemValues' => $values,
			'languages' => $this->languages,
			'languageCategories' => $this->languageCategories
		));
	}
}
