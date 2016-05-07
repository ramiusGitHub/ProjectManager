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
use wcf\util\ProjectUtil;
use wcf\system\application\ApplicationHandler;
use wcf\data\package\PackageCache;
use wcf\data\package\Package;
use wcf\data\project\database\log\DatabaseLogEditor;
use wcf\system\database\editor\MySQLDatabaseEditor;
use wcf\data\project\ProjectEditor;
use wcf\system\exception\UserInputException;

/**
 * Implementation of the database form for database tables.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseTableAddForm extends ProjectDatabaseColumnAddForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		AbstractProjectDatabaseForm::validateDuplicate();
		
		// Check duplicate
		if(!isset($this->errorType['sqlTable'])) {
			if(isset($this->tables[$this->sqlTable])) $this->errorType['sqlTable'] = 'duplicate';
		}
	}
	
	/**
	 * @see \wcf\acp\form\ProjectDatabaseColumnAddForm::validateTableName()
	 */
	protected function validateTableName() {
		if(empty($this->sqlTable)) {
			$this->errorType['sqlTable'] = 'empty';
		} elseif(mb_strlen($this->sqlTable) > 64) {
			$this->errorType['sqlTable'] = 'tooLong';
		} elseif(!static::$regex->match($this->sqlTable)) {
			$this->errorType['sqlTable'] = 'invalid';
		} else {
			$abbreviations = array('wcf');
			foreach(ApplicationHandler::getInstance()->getApplications() as $packageID => $application) {
				$package = PackageCache::getInstance()->getPackage($packageID);
				$abbreviations[] = Package::getAbbreviation($package->package);
			}
			
			$found = false;
			foreach($abbreviations as $abbreviation) {
				if(mb_strpos($this->sqlTable, $abbreviation . WCF_N . '_') === 0) {
					$found = true;
					break;
				}
			}
			if(!$found) $this->errorType['sqlTable'] = 'prefix';
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		// Try to create the table
		try {
			$indexData = array();
			if(!empty($this->key)) {
				$indexData[] = array(
					'name' => ($this->key == 'PRIMARY' ? '' : $this->sqlColumn),
					'data' => array(
						'type' => $this->key,
						'columns' => $this->sqlColumn
					)
				);
			}
			
			// Get column data and remove the key, because
			// we add indices via the $indexData
			$columnData = $this->getColumn()->toArray();
			unset($columnData['key']);
			
			WCF::getDB()->getEditor()->createTable(
				$this->sqlTable,
				array(
					array(
						'name' => $this->sqlColumn,
						'data' => $columnData
					)
				),
				$indexData
				
			);
		} catch(DatabaseException $e) {
			throw new UserInputException('databaseException', $e->getDescription());
		}
		
		// Add log entries
		// Table log entry
		DatabaseLogEditor::create(array(
			'packageID' => $this->packageID,
			'sqlTable' => $this->sqlTable
		));
		
		// Column log entry
		AbstractProjectDatabaseObjectForm::save();
		
		// Index log entry
		if(!empty($this->key)) {
			DatabaseLogEditor::create(array(
				'packageID' => $this->packageID,
				'sqlTable' => $this->sqlTable,
				'sqlIndex' => ($this->key == 'PRIMARY' ? 'PRIMARY' : $this->sqlColumn)
			));
		}
	}
}
