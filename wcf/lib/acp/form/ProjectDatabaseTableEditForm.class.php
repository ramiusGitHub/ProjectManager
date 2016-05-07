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

use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\system\database\MySQLDatabase;
use wcf\system\database\PostgreSQLDatabase;
use wcf\system\exception\UnsupportedDatabaseException;
use wcf\system\exception\UserInputException;

/**
 * Edit form for database tables.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseTableEditForm extends ProjectDatabaseTableAddForm {
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'edit';

	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(!$this->object->isNew()) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		AbstractProjectDatabaseForm::validate();
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		if($this->object->sqlTable != $this->sqlTable) {
			parent::validateDuplicate();
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {	
		AbstractProjectDataForm::save();
		
		// Only rename table if table name changed
		if($this->object->sqlTable != $this->sqlTable) {
			// Try to rename the table
			try {
				if(WCF::getDB() instanceof MySQLDatabase) {
					$sql = "RENAME TABLE " . $this->object->sqlTable . " TO " . $this->sqlTable;
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute();
				} elseif(WCF::getDB() instanceof PostgreSQLDatabase) {
					$sql = "ALTER TABLE " . $this->oldTableName . " RENAME TO " . $this->sqlTable;
					$statement = WCF::getDB()->prepareStatement($sql);
					$statement->execute();
				} else {
					throw new UnsupportedDatabaseException();
				}
			} catch(DatabaseException $e) {
				throw new UserInputException('databaseException', $e->getDescription());
			}
			
			// Update all log entries
			$sql = "UPDATE		wcf".WCF_N."_package_installation_sql_log
				SET		sqlTable = ?
				WHERE		sqlTable = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->sqlTable, $this->object->sqlTable));
		}
	}
}
