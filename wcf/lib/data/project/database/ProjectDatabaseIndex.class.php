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
 * A ProjectDatabaseIndex holds all information about an index.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseIndex {
	/**
	 * The package this index belongs to.
	 * 
	 * @var int
	 */
	protected $packageID;
	
	/**
	 * The name of the index.
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * The type of this index (INDEX, UNIQUE, FULLTEXT or PRIMARY)
	 * 
	 * @var string
	 */
	protected $type;
	
	/**
	 * The table this index is defined on.
	 *
	 * @var \wcf\data\project\database\ProjectDatabaseTable
	 */
	protected $table;
	
	/**
	 * Columns which are covered by this index.
	 *
	 * @var array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	protected $columns = array();
	
	/**
	 * Creates a new instance of the ProjectDatabaseIndex class.
	 *
	 * @param int $packageID
	 * @param string $name
	 * @param string $type
	*/
	public function __construct($packageID, $name, $type) {
		$this->packageID = $packageID;
		$this->name = $name;
		$this->type = $type;
	}
	
	/**
	 * Returns the ID of the package this index belongs to. 
	 * 
	 * @return int
	 */
	public function getPackageID() {
		return $this->packageID;
	}
	
	/**
	 * The name of this index.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * The type of this index.
	 * 
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Returns the table this index is defined on.
	 *
	 * @return \wcf\data\project\database\ProjectDatabaseTable
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * Sets the table this index is defined on.
	 *
	 * @param \wcf\data\project\database\ProjectDatabaseTable $table
	 */
	public function setTable(\wcf\data\project\database\ProjectDatabaseTable $table) {
		$this->table = $table;
	}
	
	/**
	 * Returns the column with the given name or null if the index is
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
	 * Returns the columns covered by this index.
	 *
	 * @return array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	public function getColumns() {
		return $this->columns;
	}
	
	/**
	 * Returns the names of the columns covered by this index.
	 *
	 * @return array<string>
	 */
	public function getColumnNames() {
		return array_keys($this->columns);
	}
	
	/**
	 * Sets the columns covered by this index.
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
	 * Adds an column covered by this index.
	 *
	 * @param \wcf\data\project\database\ProjectDatabaseIndex $index
	 */
	public function addColumn(\wcf\data\project\database\ProjectDatabaseColumn $column) {
		$this->columns[$column->getName()] = $column;
	}
	
	/**
	 * Returns the log entry which belongs to this index.
	 * 
	 * @return \wcf\data\project\database\log\DatabaseLog
	 */
	public function getLog() {
		return ProjectDatabaseLogCache::getInstance()->getIndexLog($this->getTable()->getName(), $this->getName());
	}
	
	/**
	 * Returns the index information in array format.
	 * 
	 * @return array<mixed>
	 */
	public function toArray() {
		return array(
			'name' => $this->getName(),
			'tableName' => $this->getTable()->getName(),
			'data' => array(
				'columns' => implode(',', array_keys($this->getColumns())),
				'type' => $this->getType()
			)
		);
	}
}
