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
namespace wcf\data\project;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\util\ClassUtil;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\data\language\item\LanguageItemAction;
use wcf\acp\form\ProjectOptionAddForm;
use wcf\data\object\type\ObjectTypeCache;
use wcf\util\ProjectUtil;
use wcf\util\StringUtil;
use wcf\data\package\Package;
use wcf\data\package\PackageCache;
use wcf\system\cache\builder\ProjectLanguageItemCacheBuilder;
use wcf\data\language\item\LanguageItemList;
use wcf\data\project\language\item\ProjectLanguageItemAction;
use wcf\system\cache\builder\LanguageCacheBuilder;

/**
 * Abstract implementation of the database object action for option categories.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectOptionCategoryAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @var string
	 */
	public static $optionActionClassName;
	
	/**
	 * @var string
	 */
	public static $optionListClassName;
	
	/**
	 * @var string
	 */
	public static $categoryListClassName;
	
	/**
	 * @var string
	 */
	public static $categoryLanguageItemPrefix;
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::deleteDependentObjects()
	 */
	protected function deleteDependentObjects() {
		$dependentObjectIDs = parent::deleteDependentObjects();
		
		// Get category names
		$categoryNames = $this->getCategoryNames($this->getObjects());
		
		// Delete options in deleted categories
		$optionList = new static::$optionListClassName();
		$optionList->getConditionBuilder()->add("categoryName IN (?)", array($categoryNames));
		$optionList->readObjects();
		
		if($optionList->count()) {
			$optionAction = new static::$optionActionClassName(
				$optionList->getObjects(),
				'delete'
			);
			$optionAction->executeAction();
			
			$pip = call_user_func(array(static::$optionActionClassName, 'getPIPName'));
			$dependentObjectIDs[$pip] = $optionList->getObjectIDs();
		}
		
		// Delete child categories
		$categoryList = new static::$categoryListClassName();
		$categoryList->getConditionBuilder()->add("parentCategoryName IN (?)", array($categoryNames));
		$categoryList->readObjects();
		
		if($categoryList->count()) {
			$categoryAction = new static(
				$categoryList->getObjects(),
				'delete'
			);
			$categoryAction->executeAction();
			
			$dependentObjectIDs[static::getPIPName()] = $categoryList->getObjectIDs();
		}
		
		return $dependentObjectIDs;
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::updateDependentObjects()
	 */
	protected function updateDependentObjects(array $objects, array $data, array $languageItems) {
		parent::updateDependentObjects($objects, $data, $languageItems);
		
		// Update references to the categoryNames if the
		// categoryName was updated
		if(isset($data['categoryName'])) {
			$categoryNames = $this->getCategoryNames($objects);
			
			// Update options
			$this->updateOptionCategoryName(
				$categoryNames,
				$data['categoryName']
			);
			
			// Update categories
			$this->updateParentCategoryName(
				$categoryNames,
				$data['categoryName']
			);
		}
	}
	
	/**
	 * Returns the names of all given categories.
	 * 
	 * @param array<\wcf\data\DatabaseObject> $objects
	 * @return array<string>
	 */
	protected function getCategoryNames($objects) {
		$categoryNames = array();
		foreach($objects as $object) {
			$categoryNames[] = $object->categoryName;
		}
		
		return $categoryNames;
	}
	
	/**
	 * Updates the categoryName of options.
	 * 
	 * @param array<string> $oldCategoryNames
	 * @param string $newCategoryName
	 * @return \wcf\data\DatabaseObjectList
	 */
	protected function updateOptionCategoryName(array $oldCategoryNames, $newCategoryName) {
		$optionList = new static::$optionListClassName();
		$optionList->getConditionBuilder()->add("categoryName IN (?)", array($oldCategoryNames));
		$optionList->readObjects();
		
		if($optionList->count()) {
			$optionAction = new static::$optionActionClassName(
				$optionList->getObjects(),
				'update',
				array(
					'data' => array(
						'categoryName' => $newCategoryName
					)
				)
			);
			$optionAction->executeAction();
		}
		
		return $optionList;
	}
	
	/**
	 * Updates the parentCategoryName of categories.
	 * 
	 * @param array<string> $oldCategoryNames
	 * @param string $newCategoryName
	 */
	protected function updateParentCategoryName(array $oldCategoryNames, $newCategoryName) {
		$categoryList = new static::$categoryListClassName();
		$categoryList->getConditionBuilder()->add("parentCategoryName IN (?)", array($oldCategoryNames));
		$categoryList->readObjects();
		
		if($categoryList->count()) {
			$categoryAction = new static(
				$categoryList->getObjects(),
				'update',
				array(
					'data' => array(
						'categoryName' => $newCategoryName
					)
				)
			);
			$categoryAction->executeAction();
		}
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getLanguageItemNames()
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		return array(
			'name' => static::$categoryLanguageItemPrefix . '.' . $object->categoryName
		);
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getNewLanguageItemNames()
	 */
	protected function getNewLanguageItemNames(array $parameters, array $types) {
		if(isset($parameters['categoryName'])) {
			return array(
				'name' => static::$categoryLanguageItemPrefix . '.' . $parameters['categoryName']
			);
		}
		
		return array();
	}
}
