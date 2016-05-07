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
namespace wcf\acp\form;

use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\system\Regex;
use wcf\system\database\DatabaseException;
use wcf\system\exception\UserInputException;
use wcf\data\project\database\log\DatabaseLogEditor;
use wcf\util\ArrayUtil;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\data\project\database\ProjectDatabaseColumn;

/**
 * Implementation of the database form for database columns.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseColumnAddForm extends AbstractProjectDatabaseForm {
	/**
	 * All column types supported by MySQL.
	 * Some types are not supported by the WCF PostgreSQL implementation.
	 * 
	 * @var array<mixed>
	 */
	public static $types = array(
		// Typical wcf types
		0 => 'tinyint',
		1 => 'int',
		2 => 'varchar',
		3 => 'text',
		
		'numeric' => array(
			'tinyint',
			'smallint',
			'mediumint',
			'int',
			'bigint',
			'disabled',
			'decimal',
			'float',
			'double',
			'real',
			'disabled',
			'bit',
			'boolean',
			'serial' // Unsported by postgresql
		),
		
		// All unsported by postgresql
		'dateAndTime' => array(
			'date',
			'datetime',
			'timestamp',
			'time',
			'year'
		),
		
		'string' => array(
			'char',
			'varchar',
			'disabled',
			'tinytext',
			'text',
			'mediumtext',
			'longtext',
			'disabled',
			'binary',
			'varbinary',
			'disabled',
			'tinyblob',
			'mediumblob',
			'blob',
			'longblob',
			'disabled',
			'enum',
			// 'set' // Unsported by postgresql and MySQLDatabaseEditor! 'values' are not parsed
		),
		
		// All unsported by postgresql
		'spatial' => array(
			'geometry',
			'point',
			'linestring',
			'polygon',
			'multipoint',
			'multilinestring',
			'multipolygon',
			'geometrycollection'
		)
	);
	
	/**
	 * Available keys
	 * 
	 * @var array<string>
	 */
	public static $keys = array(
		'',
		'INDEX',
		'UNIQUE',
		'PRIMARY',
		'FULLTEXT'
	);
	
	/**
	 * Name of the column
	 * 
	 * @var string
	 */
	public $sqlColumn;
	
	/**
	 * Selected type
	 * 
	 * @var string
	 */
	public $type;
	
	/**
	 * Does the column allow null values
	 * 
	 * @var boolean
	 */
	public $notNull = true;
	
	/**
	 * If true, the column has no default value
	 * 
	 * @var boolean
	 */
	public $noDefault = true;
	
	/**
	 * The string default value
	 * 
	 * @var string
	 */
	public $default;
	
	/**
	 * Does the column have null as default value
	 * 
	 * @var boolean
	 */
	public $defaultNull;

	/**
	 * Does the column have the current timestamp as default value
	 * 
	 * @var boolean
	 */
	public $defaultCurrentTimestamp;
	
	/**
	 * Does the column increment automatically
	 * 
	 * @var boolean
	 */
	public $autoIncrement;
	
	/**
	 * Available values of the enumeration or set
	 * 
	 * @var array<string>
	 */
	public $values;
	
	/**
	 * Length of various types
	 * 
	 * @var int
	 */
	public $length;
	
	/**
	 * Number of decimals for the decimal type
	 * 
	 * @var int
	 */
	public $decimals;
	
	/**
	 * Selected key
	 * 
	 * @var string
	 */
	public $key;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Column name
		if(isset($_POST['sqlColumn'])) {
			$this->sqlColumn = StringUtil::trim($_POST['sqlColumn']);
		}
		
		// Column type
		if(isset($_POST['type'])) {
			$this->type = StringUtil::trim($_POST['type']);
		}
		
		// NULL value
		$this->notNull = isset($_POST['notNull']);
		
		// Values
		if(isset($_POST['values'])) {
			$this->values = explode("\n", $_POST['values']);
			$this->values = ArrayUtil::trim($this->values, true);
			$this->values = array_map('escapeString', $this->values);
		}
		
		// Decimals
		if(isset($_POST['decimals'])) {
			$this->decimals = intval($_POST['decimals']);
		}
		
		// Key
		if(isset($_POST['key'])) $this->key = StringUtil::trim($_POST['key']);

		// Auto increment
		if(($this->key == 'UNIQUE' || $this->key == 'PRIMARY') && in_array($this->type, array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'))) {
			if(isset($_POST['autoIncrement'])) $this->autoIncrement = intval($_POST['autoIncrement']);
		}
		
		// Length
		switch($this->type) {
			case 'char':
				if(isset($_POST['charLength'])) $this->length = intval($_POST['charLength']);
				break;
			case 'varchar':
				if(isset($_POST['varcharLength'])) $this->length = intval($_POST['varcharLength']);
				break;
			case 'decimal':
				if(isset($_POST['decimalLength'])) $this->length = intval($_POST['decimalLength']);
				if(isset($_POST['decimalDecimal'])) $this->decimals = intval($_POST['decimalDecimal']);
				break;
		}
		
		// Default value
		$this->default = null;
		if(!isset($_POST['noDefault'])) {
			switch($this->type) {
				case 'tinytext':
				case 'text':
				case 'mediumtext':
				case 'longtext':
				case 'disabled':
				case 'binary':
				case 'varbinary':
				case 'disabled':
				case 'tinyblob':
				case 'mediumblob':
				case 'blob':
				case 'longblob':
					// no default value allowed
					break;
				case 'timestamp':
					if(isset($_POST['defaultCurrentTimestamp'])) {
						$this->defaultCurrentTimestamp = true;
						break;
					}
				default:
					if(!$this->notNull && isset($_POST['defaultNull'])) {
						$this->defaultNull = true;
					} elseif(isset($_POST['default']) && !isset($_POST['noDefault'])) {	
						$this->default = StringUtil::trim($_POST['default']);
					}
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Validate the column name
		$this->validateColumnName();
		
		// Type
		if(empty($this->type)) $this->errorType['type'] = 'empty';
		else {
			$found = false;
			foreach(static::$types as $type) {
				if(is_array($type)) {
					foreach($type as $t) {
						if($this->type == $t) {
							$found = true;
							break(2);
						}
					}
				} elseif($this->type == $type) {
					$found = true;
					break;
				}
			}
			if(!$found) $this->errorType['type'] = 'invalid';
		}

		// Key
		if(!in_array($this->key, static::$keys)) {
			$this->errorType['key'] = 'invalid';
		} elseif($this->key == 'FULLTEXT') {
			// A fulltext key needs MyISAM engine, but MyISAM
			// does not support foreign keys.
			// Therefore we cannot switch to MyISAM to add
			// a fulltext index if the table already has or
			// is referenced by at least one foreign key.
			if(count(ProjectDatabaseCache::getInstance()->getTable($this->sqlTable)->getForeignKeys()) > 0 ||
			count(ProjectDatabaseCache::getInstance()->getTable($this->sqlTable)->getReferencedByForeignKeys()) > 0) {
				$this->errorType['key'] = 'foreignKeys';
			}
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		if(!isset($this->errorType['sqlTable']) && isset($this->tables[$this->sqlTable])) {
			$columns = ProjectDatabaseCache::getInstance()->getTable($this->sqlTable)->getColumns();
			
			if(isset($columns[$this->sqlColumn])) {
				$this->errorType['sqlColumn'] = 'duplicate';
			}
		}
	}
	
	/**
	 * Validates the column name.
	 */
	protected function validateColumnName() {
		if(empty($this->sqlColumn)) {
			$this->errorType['sqlColumn'] = 'empty';
		} elseif(mb_strlen($this->sqlColumn) > 64) {
			$this->errorType['sqlColumn'] = 'tooLong';
		} elseif(!static::$regex->match($this->sqlColumn)) {
			$this->errorType['sqlColumn'] = 'invalid';
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		// Get column object
		$column = $this->getColumn();
		
		// Try to create the column
		try {
			$columnData = $column->toArray();
			unset($columnData['autoIncrement']);
			
			WCF::getDB()->getEditor()->addColumn(
				$this->sqlTable,
				$this->sqlColumn,
				$columnData
			);
		} catch(DatabaseException $e) {
			throw new UserInputException('databaseException', $e->_getMessage());
		}

		// Try to create the index
		try {
			if(!empty($this->key)) {
				if($this->key == 'PRIMARY') {
					$indexName = 'PRIMARY';
				} else {
					$indexName = $this->sqlColumn;
				}
				
				WCF::getDB()->getEditor()->addIndex(
					$this->sqlTable,
					$indexName,
					array(
						'type' => $this->key,
						'columns' => $this->sqlColumn
					)
				);
			
				// Add index log entry
				DatabaseLogEditor::create(array(
					'packageID' => $this->packageID,
					'sqlTable' => $this->sqlTable,
					'sqlIndex' => $indexName
				));
			}
		} catch(DatabaseException $e) {
			// Remove the column
			WCF::getDB()->getEditor()->dropColumn($this->sqlTable, $this->sqlColumn);
			
			throw new UserInputException('databaseException', $e->_getMessage());
		}
		
		// Add auto increment
		// This 'alter column' is necessary, because MySQL does not allow
		// column definitions with a normal INDEX key in one query. In
		// order to create an auto increment column the column has to be indexed.
		// We have to create the index separately and afterward apply the auto
		// increment definition to the column.
		if(!empty($this->key) && $this->autoIncrement) {
			WCF::getDB()->getEditor()->alterColumn(
				$this->sqlTable,
				$this->sqlColumn,
				$this->sqlColumn,
				$column->toArray()
			);
		}
		
		// Add column log entry
		parent::save();
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'sqlTable' => $this->sqlTable,
				'sqlColumn' => $this->sqlColumn
			)
		);
		
		return $result;
	}
	
	/**
	 * Returns a column with the forms data.
	 * 
	 * @return \wcf\data\project\database\ProjectDatabaseColumn
	 */
	protected function getColumn() {
		return new ProjectDatabaseColumn(
			$this->packageID,
			$this->sqlColumn,
			$this->type,
			$this->length,
			$this->decimals,
			$this->values,
			$this->notNull,
			$this->key,
			$this->default,
			$this->autoIncrement
		);
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::addForwardParameters()
	 */
	protected function addForwardParameters() {
		parent::addForwardParameters();
		
		$this->forwardParameters[] = 'sqlTable='.$this->sqlTable;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'types' => static::$types,
			'keys' => static::$keys,
			'sqlColumn' => $this->sqlColumn,
			'type' => $this->type,
			'notNull' => $this->notNull,
			'noDefault' => $this->noDefault,
			'default' => $this->default,
			'defaultNull' => $this->defaultNull,
			'defaultCurrentTimestamp' => $this->defaultCurrentTimestamp,
			'autoIncrement' => $this->autoIncrement,
			'values' => $this->values,
			'length' => $this->length,
			'decimals' => $this->decimals,
			'key' => $this->key
		));
	}
}
