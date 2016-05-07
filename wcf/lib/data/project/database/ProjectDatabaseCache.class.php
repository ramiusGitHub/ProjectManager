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

use wcf\system\SingletonFactory;
use wcf\system\cache\builder\ProjectDatabaseCacheBuilder;

/**
 * Manages the database cache.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseCache extends SingletonFactory {
	/**
	 * @var array<mixed>
	 */
	protected $databaseCache;
	
	/**
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->databaseCache = ProjectDatabaseCacheBuilder::getInstance()->getData();
	}
	
	/**
	 * Returns the table with the given name.
	 * 
	 * @param string $tableName
	 * @return \wcf\data\project\database\ProjectDatabaseTable
	 */
	public function getTable($tableName) {
		if(isset($this->databaseCache['tables'][$tableName])) {
			return $this->databaseCache['tables'][$tableName];
		}
		
		return null;
	}
	
	/**
	 * Returns all tables.
	 * 
	 * @return array<\wcf\data\project\database\ProjectDatabaseTable>
	 */
	public function getTables() {
		return $this->databaseCache['tables'];
	}
	
	/**
	 * Returns the tables of the package.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\project\database\ProjectDatabaseTable>
	 */
	public function getAllPackageTables($packageID) {
		if(isset($this->databaseCache['byPackageID']['tables'][$packageID])) {
			return $this->databaseCache['byPackageID']['tables'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns the columns of the package's own tables (without columns added
	 * by other packages) and columns the package has in other packages' tables.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	public function getAllPackageColumns($packageID) {
		if(isset($this->databaseCache['byPackageID']['columns'][$packageID])) {
			return $this->databaseCache['byPackageID']['columns'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns the indices of the package's own tables (without indices added
	 * by other packages) and indices the package has on other packages' tables.
	 *
	 * @param int $packageID
	 * @return array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	public function getAllPackageIndices($packageID) {
		if(isset($this->databaseCache['byPackageID']['indices'][$packageID])) {
			return $this->databaseCache['byPackageID']['indices'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns the foreign keys of the package's own tables (without indices added
	 * by other packages) and foreign keys the package has on other packages' tables.
	 *
	 * @param int $packageID
	 * @return array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	public function getAllPackageForeignKeys($packageID) {
		if(isset($this->databaseCache['byPackageID']['foreignKeys'][$packageID])) {
			return $this->databaseCache['byPackageID']['foreignKeys'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Creates a generic index name.
	 * 
	 * @param string $tableName
	 * @param string $columns
	 * @param string $suffix
	 * @return string index name
	 * @see  \wcf\system\database\util\SQLParser::getGenericIndexName()
	 */
	public function getGenericIndexName($tableName, $columnName, $suffix = '') {
		$indexName =  md5($tableName . '_' . $columnName) . ($suffix ? '_' . $suffix : '');
		
		// TODO remove this workaround when the following issue is fixed:
		// https://community.woltlab.com/thread/247671-mysqldatabaseeditor-setzt-namen-nicht-in-quotes/?postID=1515232#post1515232
		
		while(is_numeric($indexName[0])) {
			$indexName = mb_substr($indexName, 1);
		}
		
		return $indexName;
	}
}
