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
use wcf\data\project\database\log\ProjectDatabaseLogList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\data\package\PackageCache;
use wcf\data\project\database\log\ProjectDatabaseLogCache;
use wcf\data\project\database\ProjectDatabaseTable;
use wcf\data\project\database\ProjectDatabaseColumn;
use wcf\data\project\database\ProjectDatabaseIndex;
use wcf\data\project\database\ProjectDatabaseForeignKey;
use wcf\system\Regex;

/**
 * Cache builder for the complete database structure.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @var integer
	 */
	const RESTRICT_RESTRICT = 0;
	
	/**
	 * @var integer
	 */
	const CASCADE_RESTRICT = 1;
	
	/**
	 * @var integer
	 */
	const SET_NULL_RESTRICT = 2;
	
	/**
	 * @var integer
	 */
	const RESTRICT_CASCADE = 4;
	
	/**
	 * @var integer
	 */
	const CASCADE_CASCADE = 5;
	
	/**
	 * @var integer
	 */
	const SET_NULL_CASCADE = 6;
	
	/**
	 * @var integer
	 */
	const RESTRICT_SET_NULL = 8;
	
	/**
	 * @var integer
	 */
	const CASCADE_SET_NULL = 9;
	
	/**
	 * @var integer
	 */
	const SET_NULL_SET_NULL = 9;
	
	/**
	 * @var integer
	 */
	const NO_ACTION_RESTRICT = 16;
	
	/**
	 * @var integer
	 */
	const NO_ACTION_CASCADE = 20;
	
	/**
	 * @var integer
	 */
	const NO_ACTION_SET_NULL = 24;
	
	/**
	 * @var integer
	 */
	const RESTRICT_NO_ACTION = 32;
	
	/**
	 * @var integer
	 */
	const CASCADE_NO_ACTION = 33;
	
	/**
	 * @var integer
	 */
	const SET_NULL_NO_ACTION = 33;
	
	/**
	 * @var integer
	 */
	const NO_ACTION_NO_ACTION = 48;
	
	/**
	 * @var array<mixed>
	 */
	protected $databaseColumns;
	
	/**
	 * @var array<mixed>
	 */
	protected $databaseIndices;
	
	/**
	 * @var array<mixed>
	 */
	protected $databaseTableForeignKeys;
	
	/**
	 * @see \wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$raw = array();
		$structure = array();
		
		// Get ProjectDatabaseTable objects
		$tables = $this->getTables();
		
		// Iterate over tables to get all columns
		foreach($tables as $tableName => $table) {
			// Columns
			$raw[$tableName]['columns'] = $this->getColumns($tableName);
			
			foreach($raw[$tableName]['columns'] as $columnName => $columnData) {
				// Get log entry
				$columnLog = ProjectDatabaseLogCache::getInstance()->getColumnLog($tableName, $columnName);
				if($columnLog === null) {
					$packageID = $table->getPackageID();
				} else {
					$packageID = $columnLog->packageID;
				}
				
				// Create ProjectDatabaseColumn object
				$column = new ProjectDatabaseColumn(
					$packageID,
					$columnName,
					$columnData['type'],
					$columnData['length'],
					$columnData['decimals'],
					$columnData['values'],
					$columnData['notNull'],
					$columnData['key'],
					$columnData['default'],
					$columnData['autoIncrement']
				);
				
				// Set table
				$column->setTable($table);
				
				// Add column to table
				$table->addColumn($column);
				
				// Add column to structure
				$structure[$tableName]['columns'][$columnName] = $column; 
			}
		}
		
		// Iterate over tables again to get all indices and foreign keys
		// and set columns in indices and foreign keys and vica versa
		foreach($tables as $tableName => $table) {
			// Indices
			$raw[$tableName]['indices'] = $this->getIndices($tableName);
			
			foreach($raw[$tableName]['indices'] as $indexName => $indexData) {
				// Get log entry
				$indexLog = ProjectDatabaseLogCache::getInstance()->getIndexLog($tableName, $indexName);
				if($indexLog === null) {
					$packageID = $table->getPackageID();
				} else {
					$packageID = $indexLog->packageID;
				}
				
				// Create ProjectDatabaseIndex object
				$index = new ProjectDatabaseIndex($packageID, $indexName, $indexData['type']);
				
				// Set table
				$index->setTable($table);
				
				// Add index to table
				$table->addIndex($index);
				
				// Set columns in index and index in columns
				foreach($indexData['columns'] as $columnName) {
					$index->addColumn($structure[$tableName]['columns'][$columnName]);
					$structure[$tableName]['columns'][$columnName]->addIndex($index);
				}
				
				// Add index to structure
				$structure[$tableName]['indices'][$indexName] = $index;
			}
			
			// Foreign keys
			$raw[$tableName]['foreignKeys'] = $this->getForeignKeys($tableName);
			
			foreach($raw[$tableName]['foreignKeys'] as $foreignKeyName => $foreignKeyData) {
				// Get log entry
				$foreignKeyLog = ProjectDatabaseLogCache::getInstance()->getForeignKeyLog($tableName, $foreignKeyName);
				if($foreignKeyLog === null) $packageID = $table->getPackageID();
				else $packageID = $foreignKeyLog->packageID;
				
				// Create ProjectDatabaseForeignKey object
				$foreignKey = new ProjectDatabaseForeignKey($packageID, $foreignKeyName, $foreignKeyData['onUpdate'], $foreignKeyData['onDelete']);
				
				// Set table
				$foreignKey->setTable($table);
				
				// Add foreign key to table
				$table->addForeignKey($foreignKey);
				
				// Set referenced table
				$foreignKey->setReferencedTable($tables[$foreignKeyData['referencedTableName']]);
				
				// Add foreign key to referenced table
				$tables[$foreignKeyData['referencedTableName']]->addReferencedByForeignKey($foreignKey);
				
				// Add foreign key to columns and columns to foreign key
				foreach($foreignKeyData['columns'] as $columnName) {
					$foreignKey->addColumn($structure[$tableName]['columns'][$columnName]);
					$structure[$tableName]['columns'][$columnName]->setForeignKey($foreignKey);
				}
				
				// Add foreign key to referenced columns and referenced columns to foreign key
				foreach($foreignKeyData['referencedColumns'] as $columnName) {
					$foreignKey->addReferencedColumn($structure[$foreignKeyData['referencedTableName']]['columns'][$columnName]);
					$structure[$foreignKeyData['referencedTableName']]['columns'][$columnName]->addReferencedByForeignKey($foreignKey);
				}
				
				// Add foreign key to structure
				$structure[$tableName]['foreignKeys'][$foreignKeyName] = $foreignKey;
			}
		}
		
		// Sort by packageID
		$tablesByPackageID = array();
		$columnsByPackageID = array();
		$indicesByPackageID = array();
		$foreignKeysByPackageID = array();
		
		foreach($tables as $tableName => $table) {
			$tablesByPackageID[$table->getPackageID()][$tableName] = $table;
		}
		
		foreach($structure as $tableName => $tableData) {
			if(isset($tableData['columns'])) {
				foreach($tableData['columns'] as $columnName => $column) {
					$columnsByPackageID[$column->getPackageID()][$tableName][$columnName] = $column;
				}
			}
			
			if(isset($tableData['indices'])) {
				foreach($tableData['indices'] as $indexName => $index) {
					$indicesByPackageID[$index->getPackageID()][$tableName][$indexName] = $index;
				}
			}
			
			if(isset($tableData['foreignKeys'])) {
				foreach($tableData['foreignKeys'] as $foreignKeyName => $foreignKey) {
					$foreignKeysByPackageID[$foreignKey->getPackageID()][$tableName][$foreignKeyName] = $foreignKey;
				}
			}
		}
		
		// Return data
		return array(
			'tables' => $tables,
			'byPackageID' => array(
				'tables' => $tablesByPackageID,
				'columns' => $columnsByPackageID,
				'indices' => $indicesByPackageID,
				'foreignKeys' => $foreignKeysByPackageID
			)
		);
	}
	
	/**
	 * Returns all tables.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseTable>
	 */
	protected function getTables() {
		$tables = array();
		
		foreach(WCF::getDB()->getEditor()->getTableNames() as $tableName) {
			$tableLog = ProjectDatabaseLogCache::getInstance()->getTableLog($tableName);
			
			$tables[$tableName] = new ProjectDatabaseTable($tableLog->packageID, $tableName);
		}
		
		return $tables;
	}
	
	/**
	 * Returns the column data of a table.
	 * 
	 * Each column contains the following data:
	 * - columnName
	 * - type
	 * - length
	 * - notNull
	 * - key
	 * - default
	 * - autoIncrement
	 * 
	 * @param string $tableName
	 * @return array<mixed>
	 */
	protected function getColumns($tableName) {
		if(!isset($this->databaseColumns[$tableName])) {
			$typeRegex = Regex::compile('(\w+)\((.*)\)', Regex::CASE_INSENSITIVE);
			
			foreach(WCF::getDB()->getEditor()->getColumns($tableName) as $column) {
				if(empty($column['data']['length'])) {
					$typeRegex->match($column['data']['type']);
					$matches = $typeRegex->getMatches();
						
					if(count($matches)) {
						$column['data']['type'] = $matches[1];
						$values = explode(',', $matches[2]);
						
						if($matches[1] == 'decimal') {
							$column['data']['length'] = $values[0];
							$column['data']['decimals'] = $values[1];
						} else {
							$column['data']['values'] = array();
							foreach($values as $value) {
								$column['data']['values'][] = trim($value, "'");
							}
						}
					}
				} else {
					$column['data']['values'] = array();
				}
				
				$this->databaseColumns[$tableName][$column['name']] = array(
					'columnName' => $column['name'],
					'type' => $column['data']['type'],
					'values' => (isset($column['data']['values']) ? $column['data']['values'] : array()),
					'length' => $column['data']['length'],
					'decimals' => (isset($column['data']['decimals']) ? $column['data']['decimals'] : null),
					'notNull' => $column['data']['notNull'],
					'key' => $column['data']['key'],
					'default' => $column['data']['default'],
					'autoIncrement' => $column['data']['autoIncrement']
				);
			}
		}
		
		// check if table does not exist or has no columns: set empty array
		if(!isset($this->databaseColumns[$tableName])) {
			$this->databaseColumns[$tableName] = array();
		}
		
		return $this->databaseColumns[$tableName];
	}
	
	/**
	 * Returns the indices of the packages' own tables (without indices added
	 * by other packages) and indices the package has on other packages' tables.
	 * 
	 * @return array<mixed>
	 */
	protected function groupIndicesByPackageID() {
		$packageDatabaseIndices = array();

		// Iterate over all packages
		foreach(PackageCache::getInstance()->getPackages() as $packageID => $package) {
			// Get indices on tables of the package
			foreach(ProjectDatabaseLogCache::getInstance()->getPackageTableLogs($packageID) as $entry) {
				foreach($this->getIndices($entry->sqlTable) as $index) {
					$indexLog = ProjectDatabaseLogCache::getInstance()->getIndexLog($entry->sqlTable, $index['indexName']);
					
					// Check if index belongs to the package with the given id
					if($indexLog == null || $indexLog->packageID == $packageID) {
						// Index belongs to the package, add to array
						$packageDatabaseIndices[$packageID][$entry->sqlTable][$index['indexName']] = $index;
					}	
				}
			}
			
			// Get indices the package has on other packages' tables
			foreach(ProjectDatabaseLogCache::getInstance()->getPackageIndexLogs($packageID) as $entry) {
				if(!isset($packageDatabaseIndices[$packageID][$entry->sqlTable])) $packageDatabaseIndices[$packageID][$entry->sqlTable] = array();
				
				$tableIndices = $this->getIndices($entry->sqlTable);
				$packageDatabaseIndices[$packageID][$entry->sqlTable][$entry->sqlIndex] = $tableIndices[$entry->sqlIndex];
			}
		}
		
		return $packageDatabaseIndices;
	}
	
	/**
	 * Returns the index data of a table.
	 * 
	 * Each index contains the following data:
	 * - tableName
	 * - indexName
	 * - columns (array of column names)
	 * - type (INDEX, UNIQUE, FULLTEXT or PRIMARY)
	 * 
	 * @param string $tableName
	 * @return array<mixed>
	 */
	protected function getIndices($tableName) {
		if(!isset($this->databaseIndices)) {
			$this->databaseIndices = array();
			
			if(WCF::getDB() instanceof MySQLDatabase) {
				$sql = "SELECT	*
					FROM	INFORMATION_SCHEMA.STATISTICS
					WHERE	TABLE_SCHEMA = ?"; 
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(WCF::getDB()->getDatabaseName()));
				
				while($row = $statement->fetchArray()) {
					// Ignore INDEX names which end in "_fk", because its an
					//  WCF convention for foreign keys
					// @see https://community.woltlab.com/thread/246501-sql-log-foreign-key-index-namen-immer-mit-fk-suffix/
					if(mb_substr($row['INDEX_NAME'], -3) == '_fk') {
						continue;
					}
					
					// If index was already seen, only add the column...
					if(isset($this->databaseIndices[$row['TABLE_NAME']][$row['INDEX_NAME']])) {
						$this->databaseIndices[$row['TABLE_NAME']][$row['INDEX_NAME']]['columns'][] = $row['COLUMN_NAME'];
					}
					// ... otherwise add the complete index data
					else {
						if($row['INDEX_NAME'] == 'PRIMARY') $type = 'PRIMARY';
						elseif(!$row['NON_UNIQUE']) $type = 'UNIQUE';
						elseif($row['INDEX_TYPE'] == 'FULLTEXT') $type = 'FULLTEXT';
						else $type = 'INDEX';
						
						$this->databaseIndices[$row['TABLE_NAME']][$row['INDEX_NAME']] = array(
							'tableName' => $row['TABLE_NAME'],
							'indexName' => $row['INDEX_NAME'],
							'columns' => array($row['COLUMN_NAME']),
							'type' => $type
						);
					}
				}
			} elseif(WCF::getDB() instanceof PostgreSQLDatabase) {
				$sql = "SELECT		t.relname AS tableName,
							i.relname AS indexName,
							a.attname AS columnName,
							ix.indisunique AS isUnique,
							ix.indisprimary AS isPrimary,
					FROM		pg_class t,
							pg_class i,
							pg_index ix,
							pg_attribute a
					WHERE		t.oid = ix.indrelid
					AND		i.oid = ix.indexrelid
					AND		a.attrelid = t.oid
					AND		a.attnum = ANY(ix.indkey)
					AND		t.relkind = 'r'
					ORDER BY	t.relname,
							i.relname";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute();
				while($row = $statement->fetchArray()) {
					// Ignore INDEX names which end in "_fk" as WCF convention
					// @see https://community.woltlab.com/thread/246501-sql-log-foreign-key-index-namen-immer-mit-fk-suffix/
					if(mb_substr($row['indexName'], -3) == '_fk') continue;
					
					// If index was already seen, only add the column...
					if(isset($this->databaseIndices[$row['tableName']][$row['indexName']])) {
						$this->databaseIndices[$row['tableName']][$row['indexName']]['columns'][] = $row['columnName'];
					}
					// ... otherwise add the complete index data
					else {
						if($row['isPrimary']) $type = 'PRIMARY';
						elseif($row['isUnique']) $type = 'UNIQUE';
						elseif(mb_strpos($row['indexName'], '_fulltext_key') !== false) $type = 'FULLTEXT';
						else $type = 'INDEX';
						
						$this->databaseIndices[$row['tableName']][$row['indexName']] = array(
							'tableName' => $row['tableName'],
							'indexName' => $row['indexName'],
							'columns' => array($row['columnName']),
							'type' => $type
						);
					}
				}
			} else {
				throw new UnsupportedDatabaseException();
			}
		}
		
		// Check if table does not exist or has no indices: set empty array
		if(!isset($this->databaseIndices[$tableName])) {
			$this->databaseIndices[$tableName] = array();
		}

		return $this->databaseIndices[$tableName];
	}
	
	/**
	 * Returns the foreign keys of the packages' own tables (without foreign keys added
	 * by other packages) and foreign keys the package has on other packages' tables.
	 * 
	 * @return array<mixed>
	 */
	protected function groupForeignKeysByPackageID() {
		$packagedatabaseTableForeignKeys = array();
		
		// Iterate over all packages
		foreach(PackageCache::getInstance()->getPackages() as $packageID => $package) {
			// Get foreign keys on tables of the package
			foreach(ProjectDatabaseLogCache::getInstance()->getPackageTableLogs($packageID) as $entry) {
				// Iterate over table's foreign keys
				$tableForeignKeys = $this->getForeignKeys($entry->sqlTable);
				foreach($tableForeignKeys['foreignKeys'] as $foreignKey) {
					$foreignKeyLog = ProjectDatabaseLogCache::getInstance()->getForeignKeyLog($entry->sqlTable, $foreignKey['indexName']);
						
					// Check if foreign key belongs to the package with the given id
					if($foreignKeyLog == null || $foreignKeyLog->packageID == $packageID) {
						// Foreign key belongs to the package, add to array
						$packagedatabaseTableForeignKeys[$packageID][$entry->sqlTable][$foreignKey['indexName']] = $foreignKey;
					}
				}
			}
			
			// Get foreign keys the package has on other packages' tables
			foreach(ProjectDatabaseLogCache::getInstance()->getPackageForeignKeyLogs($packageID) as $entry) {
				if(!isset($packagedatabaseTableForeignKeys[$packageID][$entry->sqlTable])) {
					$packagedatabaseTableForeignKeys[$packageID][$entry->sqlTable] = array();
				}
				
				$tableForeignKeys = $this->getForeignKeys($entry->sqlTable);
				$packagedatabaseTableForeignKeys[$packageID][$entry->sqlTable][$entry->sqlIndex] = $tableForeignKeys['foreignKeys'][$entry->sqlIndex];
			}
		}
		
		return $packagedatabaseTableForeignKeys;
	}
	
	/**
	 * Returns the foreign key data of a table. The returned array contains
	 * under the key 'foreignKeys' the foreign keys pointing from the given
	 * table. The key 'referencedBy' contains the names of foreign keys
	 * which point to the given table.
	 * 
	 * Each foreign key contains the following data:
	 * - tableName
	 * - indexName
	 * - columns (array of column names)
	 * - referencedTableName
	 * - referencedColumns (array of column names)
	 * - onUpdate
	 * - onDelete
	 * 
	 * @param string $tableName
	 * @return array<mixed>
	 */
	protected function getForeignKeys($tableName) {
		if(!isset($this->databaseTableForeignKeys)) {
			$this->databaseTableForeignKeys = array();
		
			if(WCF::getDB() instanceof MySQLDatabase) {
				// Get relations
				$sql = "SELECT		*
					FROM		INFORMATION_SCHEMA.KEY_COLUMN_USAGE
					WHERE		TABLE_SCHEMA = ?
					AND		REFERENCED_COLUMN_NAME != ''";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(
					WCF::getDB()->getDatabaseName()
				));
				
				while($row = $statement->fetchArray()) {
					if(isset($this->databaseTableForeignKeys[$row['TABLE_NAME']][$row['CONSTRAINT_NAME']])) {
						$this->databaseTableForeignKeys[$row['TABLE_NAME']][$row['CONSTRAINT_NAME']]['columns'][] = $row['COLUMN_NAME'];
						$this->databaseTableForeignKeys[$row['TABLE_NAME']][$row['CONSTRAINT_NAME']]['referencedColumns'][] = $row['REFERENCED_COLUMN_NAME'];
					} else {
						$this->databaseTableForeignKeys[$row['TABLE_NAME']][$row['CONSTRAINT_NAME']] = array(
							'indexName' => $row['CONSTRAINT_NAME'],
							'tableName' => $row['TABLE_NAME'],
							'columns' => array($row['COLUMN_NAME']),
							'referencedTableName' => $row['REFERENCED_TABLE_NAME'],
							'referencedColumns' => array($row['REFERENCED_COLUMN_NAME'])
						);
					}
				}
				
				// Get operations and actions
				if(count($this->databaseTableForeignKeys)) {
					$prefix = WCF::getDB()->getDatabaseName() . '/';
					
					$sql = "SELECT		*
						FROM		INFORMATION_SCHEMA.INNODB_SYS_FOREIGN
						WHERE		ID LIKE ?";
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute(array($prefix.'%'));
					
					$prefixLength = mb_strlen($prefix);
					while($row = $statement->fetchArray()) {
						$indexName = mb_substr($row['ID'], $prefixLength);
						$table = mb_substr($row['FOR_NAME'], $prefixLength);
						
						switch($row['TYPE']) {
							case self::RESTRICT_CASCADE:
								$onDelete = 'RESTRICT';
								$onUpdate = 'CASCADE';
								break;
								
							case self::RESTRICT_NO_ACTION:
								$onDelete = 'RESTRICT';
								$onUpdate = 'NO ACTION';
								break;
								
							case self::RESTRICT_RESTRICT:
								$onDelete = 'RESTRICT';
								$onUpdate = 'RESTRICT';
								break;
								
							case self::RESTRICT_SET_NULL:
								$onDelete = 'RESTRICT';
								$onUpdate = 'SET NULL';
								break;

							case self::CASCADE_CASCADE:
								$onDelete = 'CASCADE';
								$onUpdate = 'CASCADE';
								break;
								
							case self::CASCADE_NO_ACTION:
								$onDelete = 'CASCADE';
								$onUpdate = 'NO ACTION';
								break;
								
							case self::CASCADE_RESTRICT:
								$onDelete = 'CASCADE';
								$onUpdate = 'RESTRICT';
								break;
								
							case self::CASCADE_SET_NULL:
								$onDelete = 'CASCADE';
								$onUpdate = 'SET NULL';
								break;

							case self::NO_ACTION_CASCADE:
								$onDelete = 'NO ACTION';
								$onUpdate = 'CASCADE';
								break;
								
							case self::NO_ACTION_NO_ACTION:
								$onDelete = 'NO ACTION';
								$onUpdate = 'NO ACTION';
								break;
								
							case self::NO_ACTION_RESTRICT:
								$onDelete = 'NO ACTION';
								$onUpdate = 'RESTRICT';
								break;
								
							case self::NO_ACTION_SET_NULL:
								$onDelete = 'NO ACTION';
								$onUpdate = 'SET NULL';
								break;

							case self::SET_NULL_CASCADE:
								$onDelete = 'SET NULL';
								$onUpdate = 'CASCADE';
								break;
								
							case self::SET_NULL_NO_ACTION:
								$onDelete = 'SET NULL';
								$onUpdate = 'NO ACTION';
								break;
								
							case self::SET_NULL_RESTRICT:
								$onDelete = 'SET NULL';
								$onUpdate = 'RESTRICT';
								break;
								
							case self::SET_NULL_SET_NULL:
								$onDelete = 'SET NULL';
								$onUpdate = 'SET NULL';
								break;
								
							default:
								throw SystemException('Unknown foreign key type ' . $row['TYPE']);
						}

						$this->databaseTableForeignKeys[$table][$indexName]['onDelete'] = $onDelete;
						$this->databaseTableForeignKeys[$table][$indexName]['onUpdate'] = $onUpdate;
					}
				}
			} elseif(WCF::getDB() instanceof PostgreSQLDatabase) {
				// TODO add postgresql support
				throw new UnsupportedDatabaseException();
			} else {
				throw new UnsupportedDatabaseException();
			}
		}
		
		if(!isset($this->databaseTableForeignKeys[$tableName])) $this->databaseTableForeignKeys[$tableName] = array();
		
		return $this->databaseTableForeignKeys[$tableName];
	}
}
