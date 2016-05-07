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

/**
 * Implementation of the project database object cache builder for ACP menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACPMenuItemCacheBuilder extends AbstractProjectDatabaseObjectCacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectDatabaseObjectCacheBuilder::$className
	 */
	protected static $className = '\wcf\data\acp\menu\item\ACPMenuItemList';
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectDatabaseObjectCacheBuilder::getSqlOrderBy()
	 */
	protected function getSqlOrderBy() {
		return "depth.depth ASC, acp_menu_item.parentMenuItem ASC, acp_menu_item.showOrder ASC, acp_menu_item.menuItem ASC";
	}
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectDatabaseObjectCacheBuilder::getSqlJoins()
	 */
	protected function getSqlJoins($tableName = '') {
		if(empty($tableName)) {
			$tableName = call_user_func(array($this->list->className, 'getDatabaseTableName'));
		}
		
		return array(
			'sql' => "	LEFT JOIN	(SELECT		a1.menuItem, COUNT(a2.menuItem)+COUNT(a3.menuItem) as depth
							FROM		" . $tableName . " a1
							LEFT JOIN	" . $tableName . " a2
							ON		a1.parentMenuItem = a2.menuItem
							LEFT JOIN	" . $tableName . " a3
							ON		a2.parentMenuItem = a3.menuItem
							WHERE		a1.packageID = ?
							GROUP BY	a1.menuItem
							) AS depth
					ON		acp_menu_item.menuItem = depth.menuItem",
			'parameters' => array(
				$this->project->packageID
			)
		);
	}
}
