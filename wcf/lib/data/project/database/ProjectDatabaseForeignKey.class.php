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
 * A ProjectDatabaseForeignKey holds all information about a foreign key.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseForeignKey {
	/**
	 * The package this foreign key belongs to.
	 * 
	 * @var int
	 */
	protected $packageID;
	
	/**
	 * The name of the foreign key.
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * The table the foreign key is defined on.
	 * 
	 * @var \wcf\data\project\database\ProjectDatabaseTable
	 */
	protected $table;

	/**
	 * The columns the foreign key is defined on.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	protected $columns = array();

	/**
	 * The table the foreign key references.
	 * 
	 * @var \wcf\data\project\database\ProjectDatabaseTable
	 */
	protected $referencedTable;

	/**
	 * The columns the foreign key references.
	 * 
	 * @var array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	protected $referencedColumns = array();
	
	/**
	 * The onUpdate operation.
	 * 
	 * @var string
	 */
	protected $onUpdate;
	
	/**
	 * The onDelete operation.
	 * 
	 * @var string
	 */
	protected $onDelete;
	
	/**
	 * Creates a new instance of the ProjectDatabaseForeignKey class.
	 * 
	 * @param int $packageID
	 * @param string $name
	 * @param string $onUpdate
	 * @param string $onDelete
	 */
	public function __construct($packageID, $name, $onUpdate, $onDelete) {
		$this->packageID = $packageID;
		$this->name = $name;
		$this->onUpdate = $onUpdate;
		$this->onDelete = $onDelete;
	}
	
	/**
	 * Returns the ID of the package this foreign key belongs to. 
	 * 
	 * @return int
	 */
	public function getPackageID() {
		return $this->packageID;
	}
	
	/**
	 * Returns the foreign key's name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Returns the onUpdate operation.
	 * 
	 * @return string
	 */
	public function getOnUpdate() {
		return $this->onUpdate;
	}
	
	/**
	 * Returns the onDelete operation.
	 * 
	 * @return string
	 */
	public function getOnDelete() {
		return $this->onDelete;
	}
	
	/**
	 * Returns the table the foreign key is defined on.
	 * 
	 * @return \wcf\data\project\database\ProjectDatabaseTable
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * Sets the table the foreign key is defined on.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseTable $table
	 */
	public function setTable(\wcf\data\project\database\ProjectDatabaseTable $table) {
		$this->table = $table;
	}
	
	/**
	 * Returns the column with the given name or null if the foreign key is
	 * not defined on a column with this name.
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
	 * Returns the columns the foreign key is defined on.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	public function getColumns() {
		return $this->columns;
	}
	
	/**
	 * Returns the names of the columns the foreign key is defined on.
	 *
	 * @return array<string>
	 */
	public function getColumnNames() {
		return array_keys($this->columns);
	}
	
	/**
	 * Sets the columns the foreign key is defined on.
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
	 * Adds a column to the columns the foreign key is defined on.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseColumn $column
	 */
	public function addColumn(\wcf\data\project\database\ProjectDatabaseColumn $column) {
		$this->columns[$column->getName()] = $column;
	}
	
	/**
	 * Returns the table the foreign key references.
	 * 
	 * @return \wcf\data\project\database\ProjectDatabaseTable
	 */
	public function getReferencedTable() {
		return $this->referencedTable;
	}	
	
	/**
	 * Sets the table the foreign key references.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseTable $table
	 */
	public function setReferencedTable(\wcf\data\project\database\ProjectDatabaseTable $table) {
		$this->referencedTable = $table;
	}
	
	/**
	 * Returns the column with the given name or null if the foreign key is
	 * not referencing a column with this name.
	 * 
	 * @param string $columnName
	 * @return \wcf\data\project\database\ProjectDatabaseColumn
	 */
	public function getReferencedColumn($columnName) {
		if(isset($this->referencedColumns[$columnName])) {
			return $this->referencedColumns[$columnName];
		}
		
		return null;
	}
	
	/**
	 * Returns the columns the foreign key references.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	public function getReferencedColumns() {
		return $this->referencedColumns;
	}
	
	/**
	 * Returns the names of the referenced columns the foreign key references.
	 *
	 * @return array<string>
	 */
	public function getReferencedColumnNames() {
		return array_keys($this->referencedColumns);
	}
	
	/**
	 * Sets the columns the foreign key references.
	 * 
	 * @param array<\wcf\data\project\database\ProjectDatabaseColumn> $columns
	 */
	public function setReferencedColumns(array $columns) {
		$this->referencedColumns = array();
		
		foreach($columns as $column) {
			$this->addReferencedColumn($column);
		}
	}
	
	/**
	 * Adds a column to the columns the foreign key references.
	 * 
	 * @param \wcf\data\project\database\ProjectDatabaseColumn $column
	 */
	public function addReferencedColumn(\wcf\data\project\database\ProjectDatabaseColumn $column) {
		$this->referencedColumns[$column->getName()] = $column;
	}
	
	/**
	 * Returns the log entry which belongs to this foreign key.
	 * 
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getLog() {
		return ProjectDatabaseLogCache::getInstance()->getForeignKeyLog($this->getTable()->getName(), $this->getName());
	}
	
	/**
	 * Returns the foreign key information in array format.
	 * 
	 * @return array<mixed>
	 */
	public function toArray() {
		return array(
			'name' => $this->getName(),
			'tableName' => $this->getTable()->getName(),
			'data' => array(
				'columns' => implode(',', array_keys($this->getColumns())),
				'referencedTable' => $this->getReferencedTable()->getName(),
				'referencedColumns' => implode(',', array_keys($this->getReferencedColumns())),
				'ON DELETE' => $this->getOnDelete(),
				'ON UPDATE' => $this->getOnUpdate()
			)
		);
	}
}
