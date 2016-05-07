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
use wcf\util\StringUtil;
use wcf\data\DatabaseObject;
use wcf\data\project\Project;

/**
 * Represents a database log.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class DatabaseLog extends DatabaseObject {
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'package_installation_sql_log';
	
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'sqlLogID';
	
	/**
	 * Returns true if this log logs a table.
	 * 
	 * @return boolean
	 */
	public function isTableLog() {
		return empty($this->sqlColumn) && empty($this->sqlIndex);
	}

	/**
	 * Returns true if this log logs a column.
	 *
	 * @return boolean
	 */
	public function isColumnLog() {
		return !empty($this->sqlColumn);
	}

	/**
	 * Returns true if this log logs an index.
	 *
	 * @return boolean
	 */
	public function isIndexLog() {
		if(empty($this->sqlIndex)) {
			return false;
		}
		
		return (mb_substr($this->sqlIndex, -3) != '_fk');
	}

	/**
	 * Returns true if this log logs a foreign key.
	 *
	 * @return boolean
	 */
	public function isForeignKeyLog() {
		return !empty($this->sqlIndex) && !$this->isIndexLog();
	}
	
	/**
	 * Returns whether this log entry was created in the current version.
	 * 
	 * @return boolean
	 */
	public function isNew() {
		$logs = ProjectDatabaseLogCache::getInstance()->getLoggedLog($this->sqlLogID);
		$versionID = Project::getProject($this->packageID)->getCurrentVersion()->getVersionID() - 1;
		
		return !isset($logs[$versionID]);
	}
}
