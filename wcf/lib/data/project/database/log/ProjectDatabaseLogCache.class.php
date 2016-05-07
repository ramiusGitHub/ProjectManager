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
namespace wcf\data\project\database\log;

use wcf\system\SingletonFactory;
use wcf\system\cache\builder\ProjectDatabaseLogCacheBuilder;

/**
 * Manages the database log cache.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseLogCache extends SingletonFactory {
	/**
	 * @var array<mixed>
	 */
	protected $databaseLogCache;
	
	/**
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->databaseLogCache = ProjectDatabaseLogCacheBuilder::getInstance()->getData();
	}
	
	/**
	 * Returns all log entries.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getLogs() {
		return $this->databaseLogCache['active']['list'];
	}
	
	/**
	 * Returns the log entry with the given log ID or null if no entry with this ID exists.
	 * 
	 * @param int $logID
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getLog($logID) {
		if(isset($this->databaseLogCache['active']['list'][$logID])) {
			return $this->databaseLogCache['active']['list'][$logID];
		}
		
		return null;
	}
	
	/**
	 * Returns the logged log entries with the given log ID.
	 * 
	 * @param int $logID
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getLoggedLog($logID) {
		if(isset($this->databaseLogCache['log']['list'][$logID])) {
			return $this->databaseLogCache['log']['list'][$logID];
		}
		
		return array();
	}
	
	/**
	 * Returns the log entry for the table or null if no entry with this name exists.
	 * 
	 * @param string $tableName
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getTableLog($tableName) {
		if(isset($this->databaseLogCache['active']['structure'][$tableName])) {
			return $this->databaseLogCache['active']['structure'][$tableName]['table'];
		}
		
		return null;
	}

	/**
	 * Returns the log entry for the column or null if no entry with this name exists.
	 *
	 * @param string $tableName
	 * @param string $columnName
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getColumnLog($tableName, $columnName) {
		if(isset($this->databaseLogCache['active']['structure'][$tableName]['columns'][$columnName])) {
			return $this->databaseLogCache['active']['structure'][$tableName]['columns'][$columnName];
		}
		
		return null;
	}

	/**
	 * Returns the log entry for the index or null if no entry with this name exists.
	 *
	 * @param string $tableName
	 * @param string $indexName
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getIndexLog($tableName, $indexName) {
		if(isset($this->databaseLogCache['active']['structure'][$tableName]['indices'][$indexName])) {
			return $this->databaseLogCache['active']['structure'][$tableName]['indices'][$indexName];
		}
		
		return null;
	}

	/**
	 * Returns the log entry for the foreign key or null if no entry with this name exists.
	 *
	 * @param string $tableName
	 * @param string $foreignKeyName
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getForeignKeyLog($tableName, $foreignKeyName) {
		if(isset($this->databaseLogCache['active']['structure'][$tableName]['foreignKeys'][$foreignKeyName])) {
			return $this->databaseLogCache['active']['structure'][$tableName]['foreignKeys'][$foreignKeyName];
		}
		
		return null;
	}
		
	/**
	 * Returns all tables logged with the given package ID.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getPackageTableLogs($packageID) {
		if(isset($this->databaseLogCache['active']['packageTables'][$packageID])) {
			return $this->databaseLogCache['active']['packageTables'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all tables deleted in the current version of the package.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getDeletedPackageTableLogs($packageID) {
		if(isset($this->databaseLogCache['deleted']['packageTables'][$packageID])) {
			return $this->databaseLogCache['deleted']['packageTables'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all tables logged in versions different of the current one. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getLoggedPackageTableLogs($packageID) {
		if(isset($this->databaseLogCache['log']['packageTables'][$packageID])) {
			return $this->databaseLogCache['log']['packageTables'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all columns logged with the given package ID. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getPackageColumnLogs($packageID) {
		if(isset($this->databaseLogCache['active']['packageColumns'][$packageID])) {
			return $this->databaseLogCache['active']['packageColumns'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all columns deleted in the current version of the package.
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getDeletedPackageColumnLogs($packageID) {
		if(isset($this->databaseLogCache['deleted']['packageColumns'][$packageID])) {
			return $this->databaseLogCache['deleted']['packageColumns'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all columns logged in versions different of the current one. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getLoggedPackageColumnLogs($packageID) {
		if(isset($this->databaseLogCache['log']['packageColumns'][$packageID])) {
			return $this->databaseLogCache['log']['packageColumns'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all indices logged with the given package ID. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getPackageIndexLogs($packageID) {
		if(isset($this->databaseLogCache['active']['packageIndices'][$packageID])) {
			return $this->databaseLogCache['active']['packageIndices'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all indices deleted in the current version of the package.
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getDeletedPackageIndexLogs($packageID) {
		if(isset($this->databaseLogCache['deleted']['packageIndices'][$packageID])) {
			return $this->databaseLogCache['deleted']['packageIndices'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all indices logged in versions different of the current one. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getLoggedPackageIndexLogs($packageID) {
		if(isset($this->databaseLogCache['log']['packageIndices'][$packageID])) {
			return $this->databaseLogCache['log']['packageIndices'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all foreign keys logged with the given package ID. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getPackageForeignKeyLogs($packageID) {
		if(isset($this->databaseLogCache['active']['packageForeignKeys'][$packageID])) {
			return $this->databaseLogCache['active']['packageForeignKeys'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all foreign keys deleted in the current version of the package.
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getDeletedPackageForeignKeyLogs($packageID) {
		if(isset($this->databaseLogCache['deleted']['packageForeignKeys'][$packageID])) {
			return $this->databaseLogCache['deleted']['packageForeignKeys'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all foreign keys logged in versions different of the current one. 
	 * 
	 * @param int $packageID
	 * @return array<mixed>
	 */
	public function getLoggedPackageForeignKeyLogs($packageID) {
		if(isset($this->databaseLogCache['log']['packageForeignKeys'][$packageID])) {
			return $this->databaseLogCache['log']['packageForeignKeys'][$packageID];
		}
		
		return array();
	}
}
