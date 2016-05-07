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
use wcf\data\language\category\LanguageCategoryAction;
use wcf\system\language\LanguageFactory;
use wcf\data\project\acp\menu\item\ProjectACPMenuItemAction;

/**
 * Wrapper for all database object actions. Considers side effects of actions, e.g.
 * on associated language items, parent or child objects and clearing of deprecated
 * caches. Logs deleted objects and can restore them. The execution of all actions
 * is permitted with the right "admin.system.projects.canManageProjects".
 * 
 * In contrast to the regular AbstractDatabaseObjectAction, this wrapper can handle
 * updates for multiple objects with different parameters. The parameters have to
 * contain the boolean value "dataPerObject => true" to update each object with
 * its respective parameters. The "data" in the parameters array have to be divided
 * into arrays with the object IDs as keys:
 * 
 * 	$parameters = array(
 * 		'dataPerObject' => true,
 * 		'data' => array(
 * 			objectID-1 => array(data-1),
 * 			objectID-2 => array(data-2)
 * 		)
 * 	);
 * 
 * If an object has associated language items, the action can update the language
 * items' values. Therefore the parameters have to contain the language item values.
 * For example \wcf\data\option\Option has the associated language items name and
 * description. To update the values of the language items, the parameters look like
 * this:
 * 
 * 	$parameters = array(
 * 		'data' => array(...),
 * 		'languageItems' => array(
 * 			'name' => array(
 * 				'languageItemValues' => array(
 * 					languageID-1 => value-1,
 * 					languageID-2 => value-2
 * 				)
 * 			),
 * 			'description' => array(
 * 				'languageItemValues' => array(
 * 					languageID-1 => value-1,
 * 					languageID-2 => value-2
 * 				)
 * 			)
 * 		)
 * 	);
 * 
 * Language item values can be updated per object like "data". If parameters contains
 * the boolean value "dataPerObject => true" than "languageItems" has to contain
 * language item values divided into arrays with the object IDs as keys. 
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectDatabaseObjectAction extends AbstractDatabaseObjectAction {
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$permissionsCreate
	 */
	protected $permissionsCreate = array('admin.system.projects.canManageProjects');

	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.system.projects.canManageProjects');
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.system.projects.canManageProjects');
	
	/**
	 * @var string
	 */
	protected static $actionClassName = '';
	
	/**
	 * Initialize a new ProjectManager-triggered action.
	 * 
	 * @param array<mixed> $objects
	 * @param string $action
	 * @param array $parameters
	 */
	public function __construct(array $objects, $action, array $parameters = array()) {
		// Set editor class name
		$this->className = static::getBaseClassName() . 'Editor';
		
		// Call parent constructor
		parent::__construct($objects, $action, $parameters);
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// Create object
		$action = new static::$actionClassName(array(), 'create', $this->getParameters());
		$result = $action->executeAction();
		$object = $result['returnValues'];
		
		// Set objects
		$this->objects = array(new $this->className($object));
		
		// Create associated language items
		$this->createLanguageItems($object, $this->getLanguageItemNames($object));
		
		// Clear caches
		$this->clearCaches();
		
		// Return object
		return $object;
	}
	
	/**
	 * Creates the language items associated with the database object.
	 * 
	 * @param \wcf\data\DatabaseObject $object
	 * @param array<string> $languageItemNames
	 */
	protected function createLanguageItems(\wcf\data\DatabaseObject $object, array $languageItemNames) {
		foreach($languageItemNames as $type => $name) {
			if(isset($this->parameters['languageItems'][$type]['languageItemValues'])) {
				$languageVariable = $this->parameters['languageItems'][$type];
				$languageCategoryID = (isset($languageVariable['languageCategoryID']) ? $languageVariable['languageCategoryID'] : $this->getLanguageCategoryID($name));
				
				foreach($languageVariable['languageItemValues'] as $languageID => $languageItemValue) {
					if(LanguageFactory::getInstance()->getLanguage($languageID)->get($name) != $name) {
						continue;
					}
					
					$action = new ProjectLanguageItemAction(
						array(),
						'create',
						array(
							'data' => array(
								'languageID' => $languageID,
								'languageItem' => $name,
								'languageItemValue' => $languageItemValue,
								'languageItemOriginIsSystem' => 1,
								'languageCategoryID' => $languageCategoryID,
								'packageID' => $object->packageID
							)
						)
					);
					$action->executeAction();
				}
			}
		}
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::delete()
	 */
	public function delete() {
		// Delete objects
		$action = new static::$actionClassName($this->getObjects(), 'delete', $this->getParameters());
		$action->executeAction();
		
		// Log deleted objects
		foreach($this->getObjects() as $object) {
			ProjectUtil::logDeletion($object);
		}
		
		// Init object IDs
		$objectIDs[static::getPIPName()] = $this->getObjectIDs();
		
		// Delete dependent objects
		$dependentObjectIDs = $this->deleteDependentObjects();
		
		// Add IDs of deleted objects to result
		$objectIDs = $this->mergeObjectIDs($objectIDs, $dependentObjectIDs);
		
		// Clear caches
		$this->clearCaches();
		
		// Return result
		return $objectIDs;
	}
	
	/**
	 * Deletes objects which depent on the deleted objects.
	 * 
	 * @return array
	 */
	protected function deleteDependentObjects() {
		$dependentObjectIDs['LanguageItem'] = $this->deleteLanguageItems($this->getObjects());
		
		return $dependentObjectIDs;
	}
	
	/**
	 * Deletes language items associated with the deleted objects.
	 * 
	 * @param array<\wcf\data\DatabaseObject> $objects
	 * @return array<int>
	 */
	protected function deleteLanguageItems(array $objects) {
		// Gather names of language items
		$languageItemNames = array();
		foreach($objects as $object) {
			foreach($this->getLanguageItemNames($object) as $type => $name) {
				$languageItemNames[] = $name;
			}
		}
		
		if(!empty($languageItemNames)) {
			// Get language item objects
			$languageItemList = new LanguageItemList();
			$languageItemList->getConditionBuilder()->add("languageItem IN (?)", array($languageItemNames));
			$languageItemList->readObjects();
			
			// Delete language items
			$action = new ProjectLanguageItemAction($languageItemList->getObjects(), 'delete');
			$action->executeAction();
			
			// Returns IDs of deleted objects
			return $languageItemList->getObjectIDs();
		}
		
		return array();
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		if(isset($this->parameters['dataPerObject']) && $this->parameters['dataPerObject']) {
			// Update objects one by one
			foreach($this->getObjects() as $object) {
				if(isset($this->parameters['data'][$object->getObjectID()])) {
					// Update object
					$action = new static::$actionClassName(
						array($object),
						'update',
						array(
							'data' => $this->parameters['data'][$object->getObjectID()]
						)
					);
					$action->executeAction();
					
					// Update dependent objects
					$this->updateDependentObjects(
						array($object),
						$this->parameters['data'][$object->getObjectID()],
						(isset($this->parameters['languageItems'][$object->getObjectID()]) ? $this->parameters['languageItems'][$object->getObjectID()] : array())
					);
				}
			}
		} else {
			// Update all objects
			$action = new static::$actionClassName(
				$this->getObjects(),
				'update',
				array(
					'data' => $this->parameters['data']
				)
			);
			$action->executeAction();
			
			// Update all dependent objects
			$this->updateDependentObjects(
				$this->getObjects(),
				$this->parameters['data'],
				(isset($this->parameters['languageItems']) ? $this->parameters['languageItems'] : array())
			);
		}
		
		// Clear deprecated caches
		$this->clearCaches();
	}
	
	/**
	 * Updates objects which depent on the updated objects.
	 * 
	 * @param array<\wcf\data\DatabaseObject> $objects
	 * @param array<mixed> $data
	 * @param array<mixed> $languageItems
	 */
	protected function updateDependentObjects(array $objects, array $data, array $languageItems) {		
		// Update object's associated language items
		if(!isset($this->parameters['deleteLanguageItems']) || !$this->parameters['deleteLanguageItems']) {
			$this->updateLanguageItemNames(
				$objects,
				$data,
				$languageItems
			);
		}
		// Delete associated language items
		else {
			$this->deleteLanguageItems($objects);
		}
	}
	
	/**
	 * Updates language items associated with the updated objects.
	 * 
	 * @param array<\wcf\data\DatabaseObject> $objects
	 * @param array<mixed> $data
	 * @param array<mixed> $languageVariables
	 */
	protected function updateLanguageItemNames(array $objects, array $data, array $languageVariables) {
		// Gather names of language items
		$languageItemNames = array();
		foreach($objects as $object) {
			foreach($this->getLanguageItemNames($object) as $type => $name) {
				if(!isset($languageItemNames[$type])) {
					$languageItemNames[$type] = array();
				}
				
				$languageItemNames[$type][] = $name;
			}
		}
		
		$newLanguageItemNames = $this->getNewLanguageItemNames($data, array_keys($languageItemNames));
		
		// Update language item names per type
		foreach($languageItemNames as $type => $names) {
			// Get language item objects
			$languageItemList = new LanguageItemList();
			$languageItemList->getConditionBuilder()->add("languageItem IN (?)", array($names));
			$languageItemList->readObjects();
			
			// Get language category ID
			if(isset($languageVariable['languageCategoryID'])) {
				$languageCategoryID = $languageVariable['languageCategoryID'];
			} elseif(isset($newLanguageItemNames[$type])) {
				$languageCategoryID = $this->getLanguageCategoryID($newLanguageItemNames[$type]);
			} else {
				$languageCategoryID = $this->getLanguageCategoryID(reset($names));
			}
			
			// Differentiate between an create/update per language
			if(isset($languageVariables[$type]['languageItemValues'])) {
				$languageVariable = $languageVariables[$type];
				
				// Sort language items by languageID
				$languageItems = array();
				foreach($languageItemList->getObjects() as $languageItem) {
					$languageItems[$languageItem->languageID] = $languageItem;
				}
				
				// Iterate over language item values per language
				foreach($languageVariable['languageItemValues'] as $languageID => $languageItemValue) {
					if(!isset($languageItems[$languageID])) {
						// Create non existent language item
						$action = new ProjectLanguageItemAction(
							array(),
							'create',
							array(
								'data' => array(
									'languageID' => $languageID,
									'languageItem' => (isset($newLanguageItemNames[$type]) ? $newLanguageItemNames[$type] : reset($names)),
									'languageItemValue' => $languageItemValue,
									'languageItemOriginIsSystem' => 1,
									'languageCategoryID' => $languageCategoryID,
									'packageID' => reset($objects)->packageID
								)
							)
						);
						$action->executeAction();
					} else {
						// Update existing language item
						$action = new ProjectLanguageItemAction(
							array($languageItems[$languageID]),
							'update',
							array(
								'data' => array(
									'languageItem' => (isset($newLanguageItemNames[$type]) ? $newLanguageItemNames[$type] : reset($names)),
									'languageItemValue' => $languageItemValue,
									'languageCategoryID' => (isset($languageCategoryID) ? $languageCategoryID : $languageItems[$languageID]->languageCategoryID)
								)
							)
						);
						$action->executeAction();
					}
				}
			}
			// Or create/update all languages at once
			else {
				// Update existing language item
				if($languageItemList->count()) {
					$data = array();
					
					if(isset($newLanguageItemNames[$type])) {
						$data['languageItem'] = $newLanguageItemNames[$type];
					}
					
					if(isset($languageCategoryID)) {
						$data['languageCategoryID'] = $languageCategoryID;
					}
					
					if(!empty($data)) {
						$action = new ProjectLanguageItemAction(
							$languageItemList->getObjects(),
							'update',
							array(
								'data' => $data
							)
						);
						$action->executeAction();
					}
				}
			}
		}
	}
	
	/**
	 * Returns the longest matching language category for the given language item
	 * or creates a language category if no matching category exists.
	 * 
	 * @param string $languageItem
	 * @return integer
	 */
	protected function getLanguageCategoryID($languageItem) {
		// Get all categories
		$categories = LanguageCacheBuilder::getInstance()->getData(array(), 'categories');
		$category = null;
		
		// Split language item into parts
		$parts = explode('.', $languageItem);
		
		// Remove last part, because it cannot be part of the categories name
		array_pop($parts);
		
		// Validate number of parts
		if(count($parts) <= 1) {
			return 0;
		}
		
		// Search for language category with three parts
		if(count($parts >= 4)) {
			// Reduce to three parts
			$parts = array_slice($parts, 0, 3);
			
			// Build category name
			$categoryName = implode('.', $parts);
			
			// Check if category exists
			if(isset($categories[$categoryName])) {
				$category = $categories[$categoryName];
			}
		}
		
		// Search for language category with two parts
		if(!isset($category)) {
			// Build category name
			$categoryName = implode('.', array_slice($parts, 0, 2));
				
			// Check if category exists
			if(isset($categories[$categoryName])) {
				$category = $categories[$categoryName];
			}
		}
		
		// No category found
		// Create new language category
		if(!isset($category)) {
			// Build category name
			$categoryName = implode('.', $parts);
			
			// Create language category
			$action = new LanguageCategoryAction(
				array(),
				'create',
				array(
					'data' => array(
						'languageCategory' => $categoryName
					)
				)
			);
			$result = $action->executeAction();
			$category = $result['returnValues'];
		}
		
		// Return categoryID
		return $category->getObjectID();
	}
	
	/**
	 * Returns the names of language items associated with the database object.
	 * 
	 * @param \wcf\data\DatabaseObject $object
	 * @return array<string>
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		return array();
	}
	
	/**
	 * Returns the new language item names based on the given parameters.
	 * 
	 * @param array<mixed> $parameters
	 * @param array<string> $types
	 * @return array<string>
	 */
	protected function getNewLanguageItemNames(array $parameters, array $types) {
		return array();
	}
	
	/**
	 * Returns the IDs of affected packages.
	 * 
	 * @return array<integer>
	 */
	protected function getPackageIDs() {
		$packageIDs = array();
		
		// Package IDs of affected objects
		foreach($this->getObjects() as $object) {
			$packageIDs[$object->packageID] = true;
		}
		
		// Package ID of parameters
		if(isset($this->parameters['data']['packageID'])) {
			$packageIDs[$this->parameters['data']['packageID']] = true;
		}
		
		return array_keys($packageIDs);
	}
	
	/**
	 * Clears caches which are deprecated after executing the action.
	 */
	abstract protected function clearCaches();
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::getObjects()
	 */
	public function getObjects() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		return parent::getObjects();
	}
	
	/**
	 * Merges two two-dimensional arrays.
	 * 
	 * @param array $o1
	 * @param array $o2
	 * @return array
	 */
	protected function mergeObjectIDs(array $o1, array $o2) {
		// Merge common pips
		foreach($o1 as $pip => $objectIDs) {
			if(isset($o2[$pip])) {
				$o1[$pip] = array_merge($o1[$pip], $o2[$pip]);
			}
		}
		
		// Add missing pips of o2 to o1
		foreach($o2 as $pip => $objectIDs) {
			if(!isset($o1[$pip])) {
				$o1[$pip] = $objectIDs;
			}
		}
		
		// Return o1
		return $o1;
	}
	
	/**
	 * Returns the name of the database object class affected by this action.
	 * 
	 * @return string
	 */
	public static function getBaseClassName() {
		return mb_substr(static::$actionClassName, 0, -6);
	}
	
	/**
	 * Returns the name of the PIP affected by this action.
	 * 
	 * @return string
	 */
	public static function getPIPName() {
		$baseClassName = static::getBaseClassName();
		
		return mb_substr($baseClassName, mb_strrpos($baseClassName, '\\') + 1);
	}
}
