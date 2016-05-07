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

use wcf\system\WCF;
use wcf\system\SingletonFactory;
use wcf\data\template\TemplateList;
use wcf\system\database\editor\MySQLDatabaseEditor;
use wcf\system\database\editor\PostgreSQLDatabaseEditor;
use wcf\system\exception\SystemException;
use wcf\system\exception\UnsupportedDatabaseException;
use wcf\system\database\MySQLDatabase;
use wcf\system\database\PostgreSQLDatabase;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\util\StringUtil;
use wcf\data\project\database\log\DatabaseLogList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\data\project\Project;

/**
 * Cache builder for database logs.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseLogCacheBuilder extends AbstractCacheBuilder {	
	/**
	 * @see \wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		// Get all log entries
		$databaseLogs = $this->getDatabaseLogs();
		
		// Structure data
		$databaseLogStructure = $this->buildDatabaseLogStructure($databaseLogs);
		$packageTables = $this->groupTableLogsByPackageID($databaseLogStructure);
		$packageColumns = $this->groupColumnLogsByPackageID($databaseLogStructure);
		$packageIndices = $this->groupIndexLogsByPackageID($databaseLogStructure);
		$packageForeignKeys = $this->groupForeignKeyLogsByPackageID($databaseLogStructure);
		
		// Get deleted and logged data
		$logs = $this->getDeletedAndLoggedDatabaseLogs();
		
		// Prepare arrays
		$deletedPackageTables = array();
		$deletedPackageColumns = array();
		$deletedPackageIndices = array();
		$deletedPackageForeignKeys = array();
		
		$loggedDatabaseLogs = array();
		$loggedPackageTables = array();
		$loggedPackageColumns = array();
		$loggedPackageIndices = array();
		$loggedPackageForeignKeys = array();
		
		// Assign logs to arrays
		foreach($logs as $log) {
			// Check if log is deleted l of current version
			if($log->versionID == Project::getProject($log->packageID)->getCurrentVersion()->getVersionID()) {
				if($log->isTableLog()) {
					$deletedPackageTables[$log->packageID][$log->sqlLogID] = $log;
				} elseif($log->isColumnLog()) {
					$deletedPackageColumns[$log->packageID][$log->sqlTable][$log->sqlLogID] = $log;
				} elseif($log->isIndexLog()) {
					$deletedPackageIndices[$log->packageID][$log->sqlTable][$log->sqlLogID] = $log;
				} else {
					$deletedPackageForeignKeys[$log->packageID][$log->sqlTable][$log->sqlLogID] = $log;
				}
			}
			// Or log is entry of a different version
			else {
				$loggedDatabaseLogs[$log->sqlLogID][$log->versionID] = $log;
				
				if($log->isTableLog()) {
					$loggedPackageTables[$log->packageID][$log->sqlLogID][$log->versionID] = $log;
				} elseif($log->isColumnLog()) {
					$loggedPackageColumns[$log->packageID][$log->sqlLogID][$log->versionID] = $log;
				} elseif($log->isIndexLog()) {
					$loggedPackageIndices[$log->packageID][$log->sqlLogID][$log->versionID] = $log;
				} else {
					$loggedPackageForeignKeys[$log->packageID][$log->sqlLogID][$log->versionID] = $log;
				}
			}
		}
		
		// Return structured data
		return array(
			'active' => array(
				'list' => $databaseLogs,
				'structure' => $databaseLogStructure,
				'packageTables' => $packageTables,
				'packageColumns' => $packageColumns,
				'packageIndices' => $packageIndices,
				'packageForeignKeys' => $packageForeignKeys
			),
			'deleted' => array(
				'packageTables' => $deletedPackageTables,
				'packageColumns' => $deletedPackageColumns,
				'packageIndices' => $deletedPackageIndices,
				'packageForeignKeys' => $deletedPackageForeignKeys
			),
			'log' => array(
				'list' => $loggedDatabaseLogs,
				'packageTables' => $loggedPackageTables,
				'packageColumns' => $loggedPackageColumns,
				'packageIndices' => $loggedPackageIndices,
				'packageForeignKeys' => $loggedPackageForeignKeys
			)
		);
	}
	
	/**
	 * Returns the complete list of database logs.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	protected function getDatabaseLogs() {
		$list = new DatabaseLogList();
		$list->sqlOrderBy = "sqlTable ASC, sqlColumn ASC, sqlIndex ASC";
		$list->readObjects();
		
		return $list->getObjects();
	}
	
	/**
	 * Returns the complete list of deleted and logged database logs.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	protected function getDeletedAndLoggedDatabaseLogs() {
		$sql = "SELECT	*
			FROM	wcf" . WCF_N . "_project_sql_log";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		
		return $statement->fetchObjects('\wcf\data\project\database\log\DatabaseLog');
	}
	
	/**
	 * Returns a structured format of the database logs.
	 * 
	 * @param array<\wcf\data\project\database\log\DatabaseLog> $databaseLogs
	 * @return array<mixed>
	 */
	protected function buildDatabaseLogStructure(array $databaseLogs) {
		$databaseLogStructure = array();
		
		foreach($databaseLogs as $entry) {
			if($entry->isTableLog()) {
				$databaseLogStructure[$entry->sqlTable]['table'] = $entry;
			} elseif($entry->isColumnLog()) {
				$databaseLogStructure[$entry->sqlTable]['columns'][$entry->sqlColumn] = $entry;
			} elseif($entry->isIndexLog()) {
				$databaseLogStructure[$entry->sqlTable]['indices'][$entry->sqlIndex] = $entry;
			} else {
				$databaseLogStructure[$entry->sqlTable]['foreignKeys'][$entry->sqlIndex] = $entry;
			}
		}
		
		foreach($databaseLogStructure as $tableName => $logs) {
			if(!isset($logs['columns'])) $databaseLogStructure[$tableName]['columns'] = array();
			if(!isset($logs['indices'])) $databaseLogStructure[$tableName]['indices'] = array();
			if(!isset($logs['foreignKeys'])) $databaseLogStructure[$tableName]['foreignKeys'] = array();
		}
		
		return $databaseLogStructure;
	}
		
	/**
	 * Returns all table logs grouped by package ID.
	 * 
	 * @param array<mixed> $databaseLogStructure
	 * @return array<mixed>
	 */
	protected function groupTableLogsByPackageID(array $databaseLogStructure) {
		$packageDatabaseTableLogs = array();
		
		foreach($databaseLogStructure as $table) {
			$packageDatabaseTableLogs[$table['table']->packageID][$table['table']->sqlTable] = $table['table'];
		}
		
		return $packageDatabaseTableLogs;
	}
	
	/**
	 * Returns all columns logs grouped by package ID. 
	 * 
	 * @param array<mixed> $databaseLogStructure
	 * @return array<mixed>
	 */
	public function groupColumnLogsByPackageID(array $databaseLogStructure) {
		$packageDatabaseColumnLogs = array();
			
		foreach($databaseLogStructure as $table) {
			foreach($table['columns'] as $column) {
				if(!isset($packageDatabaseColumnLogs[$column->packageID][$column->sqlTable])) {
					$packageDatabaseColumnLogs[$column->packageID][$column->sqlTable] = array();
				}
				
				$packageDatabaseColumnLogs[$column->packageID][$column->sqlTable][] = $column;
			}
		}
		
		return $packageDatabaseColumnLogs;
	}
	
	/**
	 * Returns all index logs grouped by packageID. 
	 * 
	 * @param array<mixed> $databaseLogStructure
	 * @return array<mixed>
	 */
	public function groupIndexLogsByPackageID(array $databaseLogStructure) {
		$packageDatabaseIndexLogs = array();
		
		foreach($databaseLogStructure as $table) {
			foreach($table['indices'] as $index) {
				if(!isset($packageDatabaseIndexLogs[$index->packageID][$index->sqlTable])) {
					$packageDatabaseIndexLogs[$index->packageID][$index->sqlTable] = array();
				}
				
				$packageDatabaseIndexLogs[$index->packageID][$index->sqlTable][] = $index;
			}
		}
		
		return $packageDatabaseIndexLogs;
	}
	
	/**
	 * Returns all foreign key logs grouped by packageID.
	 * 
	 * @param array<mixed> $databaseLogStructure
	 * @return array<mixed>
	 */
	public function groupForeignKeyLogsByPackageID(array $databaseLogStructure) {
		$packageDatabaseForeignKeyLogs = array();
		
		foreach($databaseLogStructure as $table) {
			foreach($table['foreignKeys'] as $foreignKey) {
				if(!isset($packageDatabaseForeignKeyLogs[$foreignKey->packageID][$foreignKey->sqlTable])) {
					$packageDatabaseForeignKeyLogs[$foreignKey->packageID][$foreignKey->sqlTable] = array();
				}
				
				$packageDatabaseForeignKeyLogs[$foreignKey->packageID][$foreignKey->sqlTable][] = $foreignKey;
			}
		}
		
		return $packageDatabaseForeignKeyLogs;
	}
}
