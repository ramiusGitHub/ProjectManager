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
namespace wcf\data\project\database;

use wcf\data\project\database\log\ProjectDatabaseLogCache;

/**
 * A ProjectDatabaseColumn holds all information about a column.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseColumn {
	/**
	 * The package this column belongs to.
	 * 
	 * @var int
	 */
	protected $packageID;
	
	/**
	 * The column's name.
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * The column's type.
	 * 
	 * @var string
	 */
	protected $type;

	/**
	 * The column's length.
	 * 
	 * @var int
	 */
	protected $length;

	/**
	 * The column's decimals.
	 * 
	 * @var int
	 */
	protected $decimals;
	
	/**
	 * The column's values.
	 *
	 * @var array<string>
	 */
	protected $values;
	
	/**
	 * Whether the column can contain null.
	 * 
	 * @var boolean
	 */
	protected $notNull;

	/**
	 * The column's key.
	 * 
	 * @var string
	 */
	protected $key;
	
	/**
	 * The column's default value.
	 * 
	 * @var string
	 */
	protected $default;
	
	/**
	 * Whether this is an auto increment column.
	 * 
	 * @var boolean
	 */
	protected $autoIncrement;

	/**
	 * The table this columns belongs to.
	 * 
	 * @var \wcf\data\project\database\ProjectDatabaseTable
	 */
	protected $table;

	/**
	 * The indices which cover this column.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	protected $indices = array();
	
	/**
	 * The foreign key defined on this column.
	 * 
	 * @var \wcf\data\project\database\ProjectDatabaseForeignKey
	 */
	protected $foreignKey;
	
	/**
	 * The foreign keys referencing this column.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	protected $referencedByForeignKeys = array();
	
	/**
	 * Creates a new instance of the ProjectDatabaseColumn class.
	 * 
	 * @param int $packageID
	 * @param string $name
	 * @param string $type
	 * @param int $length
	 * @param int $decimals
	 * @param array<string> $values
	 * @param boolean $notNull
	 * @param string $key
	 * @param string $default
	 * @param boolean $autoIncrement
	 */
	public function __construct($packageID, $name, $type, $length, $decimals, $values, $notNull, $key, $default, $autoIncrement) {
		$this->packageID = $packageID;
		$this->name = $name;
		$this->type = $type;
		$this->length = $length;
		$this->decimals = $decimals;
		$this->values = $values;
		$this->notNull = (bool) $notNull;
		$this->key = $key;
		$this->default = $default;
		$this->autoIncrement = (bool) $autoIncrement;
	}
	
	/**
	 * Returns the ID of the package this column belongs to. 
	 * 
	 * @return int
	 */
	public function getPackageID() {
		return $this->packageID;
	}
	
	/**
	 * Returns the column's name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returns the column's type.
	 * 
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Returns the column's length.
	 * 
	 * @return int
	 */
	public function getLength() {
		return $this->length;
	}
	
	/**
	 * Returns the column's decimals.
	 * 
	 * @return int
	 */
	public function getDecimals() {
		return $this->decimals;
	}
	
	/**
	 * Returns the column's values.
	 * 
	 * @return array<string>
	 */
	public function getValues() {
		return $this->values;
	}
	
	/**
	 * Returns whether the column can contain NULL values.
	 * 
	 * @return boolean
	 */
	public function getNotNull() {
		return $this->notNull;
	}
	
	/**
	 * Returns the column's key.
	 * 
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}
	
	/**
	 * Returns the column's default value.
	 * 
	 * @return string
	 */
	public function getDefault() {
		return $this->default;
	}
	
	/**
	 * Returns whether the column is automatically incremented.
	 * 
	 * @return boolean
	 */
	public function getAutoIncrement() {
		return $this->autoIncrement;
	}
	
	/**
	 * Returns the table this column belongs to.
	 * 
	 * @return \wcf\data\project\database\ProjectDatabaseTable
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * Sets the table this column belongs to.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseTable $table
	 */
	public function setTable(\wcf\data\project\database\ProjectDatabaseTable $table) {
		$this->table = $table;
	}
	
	/**
	 * Returns the index with the given name or null if the column
	 * is not indexed by an index with this name.
	 * 
	 * @param string $indexName
	 * @return \wcf\data\project\database\ProjectDatabaseIndex
	 */
	public function getIndex($indexName) {
		if(isset($this->indices[$indexName])) {
			return $this->indices[$indexName];
		}
		
		return null;
	}
	
	/**
	 * Returns the indices covering this column.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	public function getIndices() {
		return $this->indices;
	}
	
	/**
	 * Sets the indices covering this column.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseIndex> $indices
	 */
	public function setIndices(array $indices) {
		$this->indices = $indices;
	}
	
	/**
	 * Adds an index covering this column.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseIndex $index
	 */
	public function addIndex(\wcf\data\project\database\ProjectDatabaseIndex $index) {
		$this->indices[$index->getName()] = $index;
	}
	
	/**
	 * Returns whether a foreign key is defined on this column.
	 * 
	 * @return boolean
	 */
	public function hasForeignKey() {
		return $this->foreignKey != null;
	}
	
	/**
	 * Returns the foreign key defined on this column.
	 * 
	 * @return \wcf\data\project\database\ProjectDatabaseForeignKey
	 */
	public function getForeignKey() {
		return $this->foreignKey;
	}
	
	/**
	 * Sets the foreign key defined on this column.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey
	 */
	public function setForeignKey(\wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey) {
		$this->foreignKey = $foreignKey;
	}
	
	/**
	 * Returns whether this column is referenced by at least one foreign key.
	 * 
	 * @return boolean
	 */
	public function isReferencedByForeignKeys() {
		return !empty($this->referencedByForeignKeys);
	}
	
	/**
	 * Returns the foreign key with the given name or null if the column is
	 * not referenced by a foreign key with this name.
	 * 
	 * @param string $foreignKeyName
	 * @return \wcf\data\project\database\ProjectDatabaseForeignKey
	 */
	public function getReferencedByForeignKey($foreignKeyName) {
		if(isset($this->referencedByForeignKeys[$foreignKeyName])) {
			return $this->referencedByForeignKeys[$foreignKeyName];
		}
		
		return null;
	}
	
	/**
	 * Returns the foreign keys referencing this column.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	public function getReferencedByForeignKeys() {
		return $this->referencedByForeignKeys;
	}
	
	/**
	 * Sets the foreign keys referencing this column.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseForeignKey> $foreignKeys
	 */
	public function setReferencedByForeignKeys(array $foreignKeys) {
		$this->referencedByForeignKeys = $foreignKeys;
	}
	
	/**
	 * Adds a foreign key to the foreign keys referencing this column.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey
	 */
	public function addReferencedByForeignKey(\wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey) {
		$this->referencedByForeignKeys[$foreignKey->getName()] = $foreignKey;
	}
	
	/**
	 * Returns the log entry which belongs to this column.
	 * 
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getLog() {
		return ProjectDatabaseLogCache::getInstance()->getColumnLog($this->getTable()->getName(), $this->getName());
	}
	
	/**
	 * Returns the column information in an array format suitable for the
	 * WCF's database editor implementations.
	 * 
	 * @return array<mixed>
	 */
	public function toArray() {
		// Default value with or without quotes
		if($this->getType() == 'timestamp' && $this->getDefault() == 'CURRENT_TIMESTAMP') {
			$default = $this->getDefault();
		} elseif($this->getDefault() === null) {
			$default = null;
		} else {
			$default = "'" . $this->getDefault() . "'";
		}
		
		// Return column data as array
		return array(
			'type' => $this->getType(),
			'length' => $this->getLength(),
			'decimals' => $this->getDecimals(),
			'values' => $this->getValues(),
			'notNull' => $this->getNotNull(),
			'key' => $this->getKey(),
			'default' => $default,
			'autoIncrement' => $this->getAutoIncrement()
		);
	}
}
