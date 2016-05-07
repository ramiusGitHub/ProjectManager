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

use wcf\data\project\ProjectDataTables;
use wcf\system\WCF;

/**
 * Abstract implementation of the project cache builder for database objects.
 * The data has the following indices:
 * active: Database objects of the current project version stored in their regular tables.
 * deleted: Database objects of the current project version stored in the log tables.
 * logged: Database objects not pf the current project version stored in the log tables.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectDatabaseObjectCacheBuilder extends AbstractProjectCacheBuilder {
	/**
	 * The name of the DatabaseObjectList class.
	 * 
	 * @var string
	 */
	protected static $className;
	
	/**
	 * @var \wcf\data\DatabaseObjectList
	 */
	protected $list;
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectCacheBuilder::getProjectData()
	 */
	protected function getProjectData() {
		// Init list object to read data of active version
		$this->initList();
		$activeObjects = $this->getObjects();
		
		// Structure objects in an array
		$data = array(
			'active' => $activeObjects,
			'deleted' => array(),
			'log' => array()
		);
		
		// Iterate over logged objects
		foreach($this->getLoggedObjects() as $loggedObject) {
			// Deleted object of current version
			if($loggedObject->versionID == $this->project->getCurrentVersion()->getVersionID()) {
				$data['deleted'][$loggedObject->getObjectID()] = $loggedObject;
			} else {
				$data['log'][$loggedObject->getObjectID()][$loggedObject->versionID] = $loggedObject;
			}
		}
		
		// Return data
		return $data;
	}
	
	/**
	 * Initializes the list object.
	 */
	protected function initList() {
		$this->list = new static::$className();
		
		// Order by
		$this->list->sqlOrderBy .= $this->getSqlOrderBy();
		
		// SQL joins
		$sqlJoin = $this->getSqlJoins();
		$this->list->sqlJoins .= $sqlJoin['sql'];
		$this->list->getConditionBuilder()->add('', $sqlJoin['parameters']);
		
		// Add packageID condition
		$alias = call_user_func(array($this->list->className, 'getDatabaseTableAlias'));
		$this->list->getConditionBuilder()->add($alias . ".packageID = ?", array($this->project->packageID));
	}
	
	/**
	 * Reads and returns the list of objects.
	 * 
	 * @return array<\wcf\data\DatabaseObject>
	 */
	protected function getObjects() {
		$this->list->readObjects();
		
		return $this->list->getObjects();
	}
	
	/**
	 * Returns the "sqlOrderBy" for the list object.
	 * 
	 * @return string
	 */
	protected function getSqlOrderBy() {
		return "";
	}
	
	/**
	 * Returns the sql joins and parameters.
	 * 
	 * @param string $tableName
	 * @param string $tableAlias
	 * @return array
	 */
	protected function getSqlJoins($tableName = '') {
		return array(
			'sql' => "",
			'parameters' => array()
		);
	}
	
	/**
	 * Returns the data logged in the log table.
	 * 
	 * @return array<\wcf\data\DatabaseObject>
	 */
	protected function getLoggedObjects() {
		// Get name of log table
		$tableName = call_user_func(array($this->list->className, 'getDatabaseTableName'));
		$tableAlias = call_user_func(array($this->list->className, 'getDatabaseTableAlias'));
		$shortTableName = substr($tableName, mb_strpos($tableName, '_') + 1);
		$logTableName = "wcf" . WCF_N . "_" . ProjectDataTables::getInstance()->getLogTableName($shortTableName);
		
		// Get logged data
		$orderBy = $this->getSqlOrderBy();
		$sqlJoins = $this->getSqlJoins($logTableName);
		
		$sql = "SELECT		*
			FROM		" . $logTableName . " " . $tableAlias . "
			" . $sqlJoins['sql'] . "
			WHERE		packageID = ?
			" . (!empty($orderBy) ? "ORDER BY " . $orderBy : "");
		$statement = WCF::getDB()->prepareStatement($sql);
		
		$statement->execute(array_merge(
			array(
				$this->project->packageID
			),
			$sqlJoins['parameters']
		));
		
		// Return logged objects
		return $statement->fetchObjects($this->list->className);
	}
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectCacheBuilder::count()
	 */
	protected function count(array &$data) {
		return count($data['active']);
	}
}
