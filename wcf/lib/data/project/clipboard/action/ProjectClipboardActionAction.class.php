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
namespace wcf\data\project\clipboard\action;

use wcf\data\clipboard\action\ClipboardActionAction;
use wcf\data\project\clipboard\page\ClipboardPageList;
use wcf\util\ProjectUtil;
use wcf\system\cache\builder\ProjectClipboardActionCacheBuilder;
use wcf\system\cache\builder\ClipboardActionCacheBuilder;
use wcf\system\cache\builder\ClipboardPageCacheBuilder;
use wcf\data\project\AbstractProjectDatabaseObjectAction;

/**
 * Implementation of the project database object action for clipboard actions.
 * // TODO check if clipboard has language items
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectClipboardActionAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// Call parent
		$result = parent::create();
	
		// Clear deprecated caches
		$this->clearCaches();
	
		// Return result
		return $result;
	}
	
	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
	public function delete() {
		// Before deleting the clipboard actions, we have to read
		// the clipboard pages which belong to the Action
		$pageList = new ClipboardPageList();
		$pageList->getConditionBuilder()->add("actionID IN (?)", array($this->getObjectIDs()));
		$pageList->readObjects();
	
		// Now we can call the parent delete which will delete the
		// clipboard actions and their clipboard pages (foreign key constraint)
		$result = parent::delete();
	
		// Log deleted clipboard actions
		foreach($this->objects as $object) {
			ProjectUtil::logDeletion($object);
		}
	
		// And log the deleted clipboard pages
		foreach($pageList as $page) {
			ProjectUtil::logDeletion($page);
		}
	
		// Clear deprecated caches
		$this->clearCaches();
	
		// Return result
		return $result;
	}
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		// Call parent
		$result = parent::update();
	
		// Clear deprecated caches
		$this->clearCaches();
	
		// Return result
		return $result;
	}
	
	/**
	 * Clears caches which are deprecated after executing the action.
	 */
	protected function clearCaches() {
		// Clear project caches
		$packageIDs = array();
		foreach($this->objects as $object) {
			$packageIDs[$object->packageID] = true;
		}
	
		foreach($packacheIDs as $packageID => $tmp) {
			ProjectClipboardActionCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
	
		// Clear regular caches
		ClipboardActionCacheBuilder::getInstance()->reset();
		ClipboardPageCacheBuilder::getInstance()->reset();
	}
}
