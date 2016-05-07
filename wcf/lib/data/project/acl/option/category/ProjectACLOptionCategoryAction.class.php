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
namespace wcf\data\project\acl\option\category;

use wcf\data\project\AbstractProjectDatabaseObjectAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\acp\form\ProjectACLOptionCategoryAddForm;
use wcf\system\cache\builder\ProjectACLOptionCategoryCacheBuilder;

/**
 * Implementation of the project database object action for ACL option categories.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACLOptionCategoryAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\acl\option\category\ACLOptionCategoryAction';
	
	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
	public function delete() {
		// TODO deletion of category leads to deletion of options?
		
		// Call parent
		$result = parent::delete();
		
		// Return result
		return $result;
	}

	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		// TODO updated category name leads to updated category name in options?
		
		// Call parent
		parent::update();
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getLanguageItemNames()
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		$objectTypeName = ObjectTypeCache::getInstance()->getObjectType($object->objectTypeID)->objectType;
		
		return array($objectTypeName => ProjectACLOptionCategoryAddForm::$languageVariablePrefix . '.' . $objectTypeName . '.' . $object->categoryName);
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getNewLanguageItemNames()
	 */
	protected function getNewLanguageItemNames($parameters, $types) {
		if(isset($parameters['categoryName'])) {
			$newLanguageItemNames = array();
			
			foreach($types as $type) {
				$newLanguageItemNames[$type] = ProjectACLOptionCategoryAddForm::$languageVariablePrefix . '.' . $type . '.' . $parameters['categoryName'];
			}
			
			return $newLanguageItemNames;
		}
		
		return array();
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::clearCaches()
	 */
	protected function clearCaches() {
		// Clear project caches
		foreach($this->getPackageIDs() as $packageID) {
			ProjectACLOptionCategoryCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
	}
}
