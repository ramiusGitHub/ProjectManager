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
 * A ProjectDatabaseTable holds all information about a table.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseTable {
	/**
	 * The ID of the package this table belongs to.
	 * 
	 * @var int
	 */
	protected $packageID;
	
	/**
	 * The table's name.
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * The columns of the table.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	protected $columns = array();

	/**
	 * The indices of the table.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	protected $indices = array();
	
	/**
	 * The foreign keys defined on this table.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	protected $foreignKeys = array();
	
	/**
	 * The foreign keys referencing this table.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	protected $referencedByForeignKeys = array();
	
	/**
	 * Creates a new instance of the ProjectDatabaseTable class.
	 * 
	 * @param int $packageID
	 * @param string $name
	 */
	public function __construct($packageID, $name) {
		$this->packageID = $packageID;
		$this->name = $name;
	}
	
	/**
	 * Returns the ID of the package this table belongs to. 
	 * 
	 * @return int
	 */
	public function getPackageID() {
		return $this->packageID;
	}
	
	/**
	 * Returns the table's name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returns the column with the given name or null if the table has
	 * no column with this name.
	 * 
	 * @param string $columnName
	 * @return \wcf\data\project\database\ProjectDatabaseColumn
	 */
	public function getColumn($columnName) {
		if(isset($this->columns[$columnName])) {
			return $this->columns[$columnName];
		}
		
		return null;
	}
	
	/**
	 * Returns the columns of this table.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	public function getColumns() {
		return $this->columns;
	}
	
	/**
	 * Returns the names of the columns of this table.
	 * 
	 * @return array<string>
	 */
	public function getColumnNames() {
		return array_keys($this->columns);
	}
	
	/**
	 * Sets the columns of this table.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseColumn> $columns
	 */
	public function setColumns(array $columns) {
		$this->columns = array();
		
		foreach($columns as $column) {
			$this->addColumn($column);
		}
	}
	
	/**
	 * Adds a column to the columns of this table.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseColumn $column
	 */
	public function addColumn(\wcf\data\project\database\ProjectDatabaseColumn $column) {
		$this->columns[$column->getName()] = $column;
	}
	
	/**
	 * Returns the index with the given name or null if the table has
	 * no index with this name.
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
	 * Returns the indices of this table.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	public function getIndices() {
		return $this->indices;
	}
	
	/**
	 * Sets the indices of this table.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseIndex> $indices
	 */
	public function setIndices(array $indices) {
		$this->indices = $indices;
	}
	
	/**
	 * Adds an index to the indices of this table.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseIndex $index
	 */
	public function addIndex(\wcf\data\project\database\ProjectDatabaseIndex $index) {
		$this->indices[$index->getName()] = $index;
	}
	
	/**
	 * Returns the foreign key with the given name or null if the table has
	 * no foreign key with this name.
	 * 
	 * @param string $foreignKeyName
	 * @return \wcf\data\project\database\ProjectDatabaseForeignKey
	 */
	public function getForeignKey($foreignKeyName) {
		if(isset($this->foreignKeys[$foreignKeyName])) {
			return $this->foreignKeys[$foreignKeyName];
		}
		
		return null;
	}
	
	/**
	 * Returns the foreign keys defined on this table.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	public function getForeignKeys() {
		return $this->foreignKeys;
	}
	
	/**
	 * Sets the foreign keys defined on this table.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseForeignKey> $foreignKeys
	 */
	public function setForeignKeys(array $foreignKeys) {
		$this->foreignKeys = $foreignKeys;
	}
	
	/**
	 * Adds a foreign key defined on this table.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey
	 */
	public function addForeignKey(\wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey) {
		$this->foreignKeys[$foreignKey->getName()] = $foreignKey;
	}
	
	/**
	 * Returns whether this table is referenced by at least one foreign key.
	 * 
	 * @return boolean
	 */
	public function isReferencedByForeignKeys() {
		return !empty($this->referencedByForeignKeys);
	}
	
	/**
	 * Returns the foreign key with the given name or null if the table is
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
	 * Returns the foreign keys referencing this table.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	public function getReferencedByForeignKeys() {
		return $this->referencedByForeignKeys;
	}
	
	/**
	 * Sets the foreign keys referencing this table.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseForeignKey> $foreignKeys
	 */
	public function setReferencedByForeignKeys(array $foreignKeys) {
		$this->referencedByForeignKeys = $foreignKeys;
	}
	
	/**
	 * Adds a foreign key to the foreign keys referencing this table.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey
	 */
	public function addReferencedByForeignKey(\wcf\data\project\database\ProjectDatabaseForeignKey $foreignKey) {
		$this->referencedByForeignKeys[$foreignKey->getName()] = $foreignKey;
	}
	
	/**
	 * Returns the log entry which belongs to this table.
	 * 
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getLog() {
		return ProjectDatabaseLogCache::getInstance()->getTableLog($this->getName());
	}
	
	/**
	 * Returns the table information in array format.
	 * 
	 * @return array<mixed>
	 */
	public function toArray() {
		return array(
			'name' => $this->getName()
		);
	}
}
