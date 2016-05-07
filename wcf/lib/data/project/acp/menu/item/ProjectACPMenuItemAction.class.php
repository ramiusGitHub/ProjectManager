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
namespace wcf\data\project\acp\menu\item;

use wcf\data\project\AbstractProjectDatabaseObjectAction;
use wcf\system\cache\builder\ProjectACPMenuItemCacheBuilder;
use wcf\system\cache\builder\ACPMenuCacheBuilder;
use wcf\data\acp\menu\item\ACPMenuItemList;

/**
 * Implementation of the project database object action for ACP menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACPMenuItemAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\acp\menu\item\ACPMenuItemAction';
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::deleteDependentObjects()
	 */
	protected function deleteDependentObjects() {
		$dependentObjectIDs = parent::deleteDependentObjects();
		
		// Delete child menu items
		$childObjectIDs = $this->deleteChildMenuItems();
		
		// Merge object IDs
		$dependentObjectIDs = $this->mergeObjectIDs($dependentObjectIDs, $childObjectIDs); 
		
		return $dependentObjectIDs;
	}
	
	/**
	 * Deletes child menu items of the deleted menu items.
	 * 
	 * @return array
	 */
	protected function deleteChildMenuItems() {
		$objectIDs = array();
		
		foreach($this->getObjects() as $object) {
			$list = new ACPMenuItemList();
			$list->getConditionBuilder()->add("parentMenuItem = ?", array($object->menuItem));
			$list->readObjects();
				
			if($list->count()) {
				$action = new ProjectACPMenuItemAction(
					$list->getObjects(),
					'delete'
				);
				$result = $action->executeAction();
				
				$objectIDs = $this->mergeObjectIDs($objectIDs, $result['returnValues']);
			}
		}
		
		return $objectIDs;
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::updateDependentObjects()
	 */
	protected function updateDependentObjects(array $objects, array $data, array $languageItems) {
		parent::updateDependentObjects($objects, $data, $languageItems);

		// Update items' children
		$this->updateChildMenuItems($objects, $data);
	}
	
	/**
	 * Updates the parentMenuItem values of child menu items.
	 * 
	 * @param array<\wcf\data\acp\menu\item\ACPMenuItemEditor> $object
	 * @param array<mixed> $parameters
	 */
	protected function updateChildMenuItems(array $object, array $parameters) {
		// Update child menu items only if parent menu item name was updated
		if(isset($this->parameters['menuItem'])) {
			// Iterate over parent menu items
			foreach($objects as $object) {
				// Update child menu items only if parent menu item
				// name did really change
				if($object->menuItem == $this->parameters['menuItem']) {
					continue;
				}
				
				// Get list of child menu items
				$list = new ACPMenuItemList();
				$list->getConditionBuilder()->add("parentMenuItem = ?", array($object->menuItem));
				$list->readObjects();
				
				// Update child menu items
				if($list->count()) {
					$action = new ProjectACPMenuItemAction(
						$list->getObjects(),
						'update',
						array(
							'data' => array(
								'parentMenuItem' => $this->parameters['menuItem']
							)
						)
					);
					$action->executeAction();
				}
			}
		}
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getLanguageItemNames()
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		return array(
			'name' => $object->menuItem
		);
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getNewLanguageItemNames()
	 */
	protected function getNewLanguageItemNames(array $parameters, array $types) {
		if(isset($parameters['menuItem'])) {
			return array(
				'name' => $parameters['menuItem']
			);
		}
		
		return array();
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::clearCaches()
	 */
	protected function clearCaches() {
		// Clear project caches
		foreach($this->getPackageIDs() as $packageID) {
			ProjectACPMenuItemCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
		
		// Clear regular caches
		ACPMenuCacheBuilder::getInstance()->reset();
	}
}
