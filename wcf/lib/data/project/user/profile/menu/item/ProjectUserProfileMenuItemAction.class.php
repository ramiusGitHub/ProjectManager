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
namespace wcf\data\project\user\profile\menu\item;

use wcf\data\project\AbstractProjectDatabaseObjectAction;
use wcf\system\cache\builder\ProjectUserProfileMenuItemCacheBuilder;
use wcf\system\cache\builder\UserProfileMenuCacheBuilder;

/**
 * Implementation of the project database object action for user profile menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserProfileMenuItemAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\user\profile\menu\item\UserProfileMenuItemAction';
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getLanguageItemNames()
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		return array(
			'name' => USER_PROFILE_MENU_ITEM_LANGUAGE_ITEM_PREFIX . $object->menuItem
		);
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getNewLanguageItemNames()
	 */
	protected function getNewLanguageItemNames(array $parameters, array $types) {
		if(isset($parameters['menuItem'])) {
			return array(
				'name' => USER_PROFILE_MENU_ITEM_LANGUAGE_ITEM_PREFIX . $parameters['menuItem']
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
			ProjectUserProfileMenuItemCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
	
		// Clear regular caches
		UserProfileMenuCacheBuilder::getInstance()->reset();
	}
}
