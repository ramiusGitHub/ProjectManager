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

use wcf\system\WCF;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\system\exception\UserInputException;
use wcf\util\ProjectUtil;
use wcf\data\project\Project;
use wcf\system\cache\builder\ProjectDatabaseCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseLogCacheBuilder;
use wcf\data\project\database\ProjectDatabaseTable;
use wcf\system\cache\builder\ProjectDatabaseTableCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseColumnCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseIndexCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseForeignKeyCacheBuilder;
use wcf\data\project\AbstractProjectDatabaseObjectAction;

/**
 * Implementation of the project database object action for database logs.
 * // TODO move functionality for database modification for create and update from forms to action?
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseLogAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\project\database\log\DatabaseLogAction';
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::validateDelete()
	 */
	public function validateDelete() {
		$log = $this->getSingleObject();
		
		if($log->isTableLog()) {
			$this->validateDeleteTable();
		} elseif($log->isColumnLog()) {
			$this->validateDeleteColumn();
		} elseif($log->isIndexLog()) {
			$this->validateDeleteIndex();
		}
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::validateDelete()
	 */
	public function delete() {
		$log = $this->getSingleObject();
		
		// Delete database modification and get IDs of associated log entries
		if($log->isTableLog()) {
			$this->objectIDs = $this->deleteTable();
		} elseif($log->isColumnLog()) {
			$this->objectIDs = $this->deleteColumn();
		} elseif($log->isIndexLog()) {
			$this->objectIDs = $this->deleteIndex();
		} else {
			$this->objectIDs = $this->deleteForeignKey();
		}
		
		// Get log entries
		$this->readObjects();
		
		// Delete log entries
		parent::delete();
		
		return array(
			'Database' => $this->objectIDs
		);
	}
	
	/**
	 * Validates the deletion of a table.
	 */
	protected function validateDeleteTable() {
		$log = $this->getSingleObject();
		$errors = array();
		
		// Check whether the table is referenced by at least one foreign key
		$table = ProjectDatabaseCache::getInstance()->getTable($log->sqlTable);
		if($table->isReferencedByForeignKeys()) {
			// Only gather foreign keys from other tables
			$foreignKeys = array();
			foreach($table->getReferencedByForeignKeys() as $foreignKey) {
				if($foreignKey->getTable()->getName() != $table->getName()) {
					$foreignKeys[] = $foreignKey->toArray();
				}
			}
			
			// If at least one foreign key from another table
			// references this table, throw an exception
			if(!empty($foreignKeys)) {
				$errors['referencedBy'] = $foreignKeys;
			}
		}
		
		// Check if any other package has columns in the table
		$columns = array();
		foreach($table->getColumns() as $column) {
			if($column->getPackageID() != $table->getPackageID()) {
				$columns[] = $column->toArray();
			}
		}
		if(!empty($columns)) {
			$errors['columns'] = $columns;
		}
		
		// Check if any other package has indices on the table
		$indices = array();
		foreach($table->getIndices() as $index) {
			if($index->getPackageID() != $table->getPackageID()) {
				$indices[] = $index->toArray();
			}
		}
		if(!empty($indices)) {
			$errors['indices'] = $indices;
		}
		
		// Check if any other package has foreign keys on the table
		$foreignKeys = array();
		foreach($table->getForeignKeys() as $foreignKey) {
			if($foreignKey->getPackageID() != $table->getPackageID()) {
				$foreignKeys[] = $foreignKey->toArray();
			}
		}
		if(!empty($foreignKeys)) {
			$errors['foreignKeys'] = $foreignKeys;
		}
		
		// Throw exception if any errors occured
		if(!empty($errors)) {
			$message = WCF::getLanguage()->getDynamicVariable('wcf.project.error.database.table.notDeletable', $errors);
			
			throw new UserInputException('sqlTable', $message);
		}
	}
	
	/**
	 * Validates the deletion of a column.
	 */
	protected function validateDeleteColumn() {
		$log = $this->getSingleObject();
		$errors = array();
		
		// Check whether the column is referenced by at least one foreign key
		$column = ProjectDatabaseCache::getInstance()->getTable($log->sqlTable)->getColumn($log->sqlColumn);
		if($column->isReferencedByForeignKeys()) {
			// Only gather foreign keys from other tables
			$foreignKeys = array();
			foreach($column->getReferencedByForeignKeys() as $foreignKey) {
				if($foreignKey->getTable()->getName() != $column->getTable()->getName()) {
					$foreignKeys[] = $foreignKey->toArray();
				}
			}
			
			// If at least one foreign key from another table
			// references this column, throw an exception
			if(!empty($foreignKeys)) {
				$errors['referencedBy'] = $foreignKeys;
			}
		}
		
		// Check if any other package has indices on the table
		$indices = array();
		foreach($column->getIndices() as $index) {
			if($index->getPackageID() != $column->getPackageID()) {
				$indices[] = $index->toArray();
			}
		}
		if(!empty($indices)) {
			$errors['indices'] = $indices;
		}
		
		// Check if any other package has foreign keys on the table
		$foreignKeys = array();
		foreach($column->getForeignKeys() as $foreignKey) {
			if($foreignKey->getPackageID() != $column->getPackageID()) {
				$foreignKeys[] = $foreignKey->toArray();
			}
		}
		if(!empty($foreignKeys)) {
			$errors['foreignKeys'] = $foreignKeys;
		}
		
		// Throw exception if any errors occured
		if(!empty($errors)) {
			$message = WCF::getLanguage()->getDynamicVariable('wcf.project.error.database.column.notDeletable', $errors);
			
			throw new UserInputException('sqlColumns', $message);
		}
	}
	
	/**
	 * Validates the deletion of a index.
	 */
	protected function validateDeleteIndex() {
		// TODO do not allow deletion of index if it is the only UNIQUE or PRIMARY defined on an auto incremented column
	}
	
	/**
	 * Deletes the table as well as all the columns, indices and foreign keys
	 * of the table and returns the IDs of the associated log entries.
	 * 
	 * @return array<integer>
	 */
	protected function deleteTable() {
		// Init
		$log = $this->getSingleObject();
		$table = ProjectDatabaseCache::getInstance()->getTable($log->sqlTable);
		$logIDs = array();
		
		// Try to delete the table
		try {
			WCF::getDB()->getEditor()->dropTable($log->sqlTable);
		} catch(DatabaseException $e) {
			throw new SystemException($e->_getMessage(), 0, $e->getDescription(), $e);
		}
		
		// Move the log entries to the project sql log table
		// Move table log
		ProjectUtil::logDeletion(
			$log,
			array(
				'sqlData' => serialize($table->toArray())
			)
		);
		$logIDs[] = $log->getObjectID();
		
		// Move column logs
		foreach($table->getColumns() as $column) {
			$columnLog = ProjectDatabaseLogCache::getInstance()->getColumnLog($column->getTable()->getName(), $column->getName());
			ProjectUtil::logDeletion(
				$columnLog,
				array(
					'sqlData' => serialize($column->toArray())
				)
			);
			$logIDs[] = $columnLog->getObjectID();
		}
		
		// Move index logs
		foreach($table->getIndices() as $index) {
			$indexLog = ProjectDatabaseLogCache::getInstance()->getIndexLog($index->getTable()->getName(), $index->getName());
			ProjectUtil::logDeletion(
				$indexLog,
				array(
					'sqlData' => serialize($index->toArray())
				)
			);
			$logIDs[] = $indexLog->getObjectID();
		}
		
		// Move foreign key logs
		foreach($table->getForeignKeys() as $foreignKey) {
			$foreignKeyLog = ProjectDatabaseLogCache::getInstance()->getForeignKeyLog($foreignKey->getTable()->getName(), $foreignKey->getName());
			ProjectUtil::logDeletion(
				$foreignKeyLog,
				array(
					'sqlData' => serialize($foreignKey->toArray())
				)
			);
			$logIDs[] = $foreignKeyLog->getObjectID();
		}
	
		// Return log IDs
		return $logIDs;
	}
	
	/**
	 * Deletes the column as well as indices and foreign keys defined on the column
	 * and returns the IDs of all associated log entries.
	 * 
	 * @return array<integer>
	 */
	protected function deleteColumn() {
		// Init
		$log = $this->getSingleObject();
		$column = ProjectDatabaseCache::getInstance()->getTable($log->sqlTable)->getColumn($log->sqlColumn);
		$logIDs = array();
	
		// Try to delete the column
		try {
			WCF::getDB()->getEditor()->dropColumn($log->sqlTable, $log->sqlColumn);
		} catch(DatabaseException $e) {
			throw new SystemException($e->_getMessage(), 0, $e->getDescription(), $e);
		}
		
		// Move the log entries to the project sql log table
		// Move column log
		ProjectUtil::logDeletion(
			$log,
			array(
				'sqlData' => serialize($column->toArray())
			)
		);
		$logIDs[] = $log->getObjectID();
		
		// Move index logs
		foreach($column->getIndices() as $index) {
			$indexLog = ProjectDatabaseLogCache::getInstance()->getIndexLog($index->getTable()->getName(), $index->getName());
			ProjectUtil::logDeletion(
				$indexLog,
				array(
					'sqlData' => serialize($index->toArray())
				)
			);
			$logIDs[] = $indexLog->getObjectID();
		}
		
		// Move foreign key logs
		foreach($column->getForeignKeys() as $foreignKey) {
			$foreignKeyLog = ProjectDatabaseLogCache::getInstance()->getIndexLog($foreignKey->getTable()->getName(), $foreignKey->getName());
			ProjectUtil::logDeletion(
				$foreignKeyLog,
				array(
					'sqlData' => serialize($foreignKey->toArray())
				)
			);
			$logIDs[] = $foreignKeyLog->getObjectID();
		}
	
		// Return log IDs
		return $logIDs;
	}
	
	/**
	 * Deletes the index and returns the ID of its log entry.
	 * 
	 * @return array<integer>
	 */
	protected function deleteIndex() {
		// Init
		$log = $this->getSingleObject();
		$index = ProjectDatabaseCache::getInstance()->getTable($log->sqlTable)->getIndex($log->sqlIndex);
		
		// Try to delete the index
		try {
			WCF::getDB()->getEditor()->dropIndex($log->sqlTable, $log->sqlIndex);
		} catch(DatabaseException $e) {
			throw new SystemException($e->_getMessage(), 0, $e->getDescription(), $e);
		}
		
		// Move the log entry to the project sql log table
		ProjectUtil::logDeletion(
			$log,
			array(
				'sqlData' => serialize($index->toArray())
			)
		);
	
		// Return ID of log entry
		return array($log->getObjectID());
	}
	
	/**
	 * Deletes the foreign key and returns the ID of its log entry.
	 * 
	 * @return array<integer>
	 */
	protected function deleteForeignKey() {
		// Init
		$log = $this->getSingleObject();
		$foreignKey = ProjectDatabaseCache::getInstance()->getTable($log->sqlTable)->getForeignKey($log->sqlIndex);
		
		// Try to delete the foreign key
		try {
			WCF::getDB()->getEditor()->dropForeignKey($log->sqlTable, $log->sqlIndex);
		} catch(DatabaseException $e) {
			throw new SystemException($e->_getMessage(), 0, $e->getDescription(), $e);
		}
		
		// Move the log entry to the project sql log table
		ProjectUtil::logDeletion(
			$log,
			array(
				'sqlData' => serialize($foreignKey->toArray())
			)
		);
	
		// Return ID of log entry
		return array($log->getObjectID());
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::clearCaches()
	 */
	protected function clearCaches() {
		// Get packageID
		$packageIDs = $this->getPackageIDs();
		$packageID = array_pop($packageIDs);
		
		// Reset project database caches
		ProjectDatabaseTableCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		ProjectDatabaseColumnCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		ProjectDatabaseIndexCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		ProjectDatabaseForeignKeyCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
	}
}
