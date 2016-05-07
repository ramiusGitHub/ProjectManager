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
namespace wcf\system\cache\builder;

use wcf\acp\action\ClipboardAction;
use wcf\data\project\clipboard\page\ClipboardPageList;

/**
 * Implementation of the project database object cache builder for clipboard actions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectClipboardActionCacheBuilder extends AbstractProjectDatabaseObjectCacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectDatabaseObjectCacheBuilder::$className
	 */
	protected static $className = '\wcf\data\clipboard\action\ClipboardActionList';
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectDatabaseObjectCacheBuilder::getSqlOrderBy()
	 */
	protected function getSqlOrderBy() {
		return "actionClassName ASC, actionName ASC";
	}
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectDatabaseObjectCacheBuilder::getObjects()
	 */
	protected function getObjects() {
		// Get clipboard actions
		$objects = parent::getObjects();
		
		// Get clipboard pages
		$pageList = new ClipboardPageList();
		$pageList->getConditionBuilder()->add("actionID IN (?)", array($this->list->getObjectIDs()));
		$pageList->readObjects();
		
		// Add clipboard pages to clipboard actions
		$rows = array();
		foreach($pageList->getObjects() as $page) {
			if(isset($rows[$page->actionID])) {
				$rows[$page->actionID]['pages'][] = $page;
			}
			else {
				$rows[$page->actionID] = $objects[$page->actionID]->getData();
				$rows[$page->actionID]['pages'] = array($page);
			}
		}
		
		// Create clipboard action objects with their pages
		$data = array();
		foreach($rows as $actionID => $row) {
			$data[] = new ClipboardAction(0, $row);
		}
		
		// Return cache data
		return $data;
	}
}
