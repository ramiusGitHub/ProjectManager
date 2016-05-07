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
namespace wcf\data\project\bbcode;

use wcf\data\project\AbstractProjectDatabaseObjectAction;
use wcf\system\cache\builder\ProjectBBCodeCacheBuilder;
use wcf\system\cache\builder\BBCodeCacheBuilder;
use wcf\util\ProjectUtil;
use wcf\data\bbcode\attribute\BBCodeAttributeList;

/**
 * Implementation of the project database object action for BBCodes.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectBBCodeAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\bbcode\BBCodeAction';
	
	/**
	 * @see	\wcf\data\IDeleteAction::delete()
	 */
	public function delete() {
		// Before deleting the BBCodes, we have to read
		// the attributes which belong to the BBCodes
		$attributeList = new BBCodeAttributeList();
		$attributeList->getConditionBuilder()->add("bbcodeID IN (?)", array($this->getObjectIDs()));
		$attributeList->readObjects();
		
		// Now we can call the parent delete which will delete the
		// BBCodes and their attributes (foreign key constraint)
		$result = parent::delete();
		
		// Log the deleted attributes
		foreach($attributeList as $attribute) {
			ProjectUtil::logDeletion($attribute, array(
				'packageID' => $this->objects[$attribute->bbcodeID]->packageID
			));
		}
	
		// Return result
		return $result;
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::clearCaches()
	 */
	protected function clearCaches() {
		// Clear project caches
		foreach($this->getPackageIDs() as $packageID) {
			ProjectBBCodeCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
	
		// Clear regular caches
		BBCodeCacheBuilder::getInstance()->reset();
	}
}
