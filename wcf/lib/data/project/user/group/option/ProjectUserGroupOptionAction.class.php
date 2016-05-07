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
namespace wcf\data\project\user\group\option;

use wcf\data\project\AbstractProjectOptionAction;
use wcf\data\user\group\UserGroupEditor;
use wcf\acp\form\ProjectUserGroupOptionAddForm;
use wcf\system\cache\builder\ProjectUserGroupOptionCacheBuilder;
use wcf\system\cache\builder\UserGroupOptionCacheBuilder;

/**
 * Implementation of the abstract option action for user group options.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserGroupOptionAction extends AbstractProjectOptionAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\user\group\option\UserGroupOptionAction';
	
	/**
	 * @see \wcf\data\project\AbstractProjectOptionAction::$optionLanguageItemPrefix
	 */
	public static $optionLanguageItemPrefix = USER_GROUP_OPTION_LANGUAGE_ITEM_PREFIX;
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::clearCaches()
	 */
	protected function clearCaches() {
		// Clear project caches
		foreach($this->getPackageIDs() as $packageID) {
			ProjectUserGroupOptionCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
		
		// Clear regular caches
		UserGroupOptionCacheBuilder::getInstance()->reset();
		UserGroupEditor::resetCache();
	}

	
	
}
