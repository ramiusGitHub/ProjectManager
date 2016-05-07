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
use wcf\system\database\DatabaseException;
use wcf\system\exception\UserInputException;
use wcf\system\exception\UserException;
use wcf\data\project\database\ProjectDatabaseAccess;
use wcf\system\database\editor\MySQLDatabaseEditor;
use wcf\system\database\util\SQLParser;
use wcf\system\package\PackageInstallationSQLParser;
use wcf\data\project\ProjectEditor;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\data\project\database\ProjectDatabaseTable;
use wcf\data\project\ProjectDataTables;

/**
 * Implementation of the database form for database foreign keys.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseForeignKeyAddForm extends AbstractProjectDatabaseForm {
	/**
	 * @var integer
	 */
	const ER_DUP_KEY = 23000;
	
	/**
	 * Available actions.
	 * 
	 * @var array<string>
	 */
	public static $actions = array(
		'CASCADE',
		'SET NULL',
		'RESTRICT',
		'NO ACTION'
	);
	
	/**
	 * @var string
	 */
	public $sqlIndex;
	
	/**
	 * @var array<string>
	 */
	public $selectedColumns;

	/**
	 * Selected table
	 *
	 * @var string
	 */
	public $referencedSqlTable;
	
	/**
	 * @var array<string>
	 */
	public $referencedColumns;

	/**
	 * Action on delete
	 *
	 * @var string
	 */
	public $deleteAction;

	/**
	 * Action on update
	 *
	 * @var string
	 */
	public $updateAction;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Name of the referenced table
		if(isset($_POST['referencedSqlTable'])) {
			$this->referencedSqlTable = StringUtil::trim($_POST['referencedSqlTable']);
		}
		
		// Selected and referenced columns
		$this->selectedColumns = array();
		$this->referencedColumns = array();
		foreach($this->tables as $sqlTable => $columns) {
			if(isset($_POST['columns'][$sqlTable])) $this->selectedColumns[$sqlTable] = $_POST['columns'][$sqlTable];
			if(isset($_POST['referencedColumns'][$sqlTable])) $this->referencedColumns[$sqlTable] = $_POST['referencedColumns'][$sqlTable];
		}
		
		// Actions
		if(isset($_POST['deleteAction'])) {
			$this->deleteAction = StringUtil::trim($_POST['deleteAction']);
		}
		
		if(isset($_POST['updateAction'])) {
			$this->updateAction = StringUtil::trim($_POST['updateAction']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Selected columns
		if(!isset($this->selectedColumns[$this->sqlTable]) || empty($this->selectedColumns[$this->sqlTable])) $this->errorType['selectedColumns'] = 'empty';
		
		// Referenced table
		if(empty($this->referencedSqlTable)) {
			$this->errorType['referencedSqlTable'] = 'empty';
		} elseif(!isset($this->tables[$this->referencedSqlTable])) {
			$this->errorType['referencedSqlTable'] = 'invalid';
		}
		
		// Referenced columns
		if(!isset($this->referencedColumns[$this->referencedSqlTable]) || empty($this->referencedColumns[$this->referencedSqlTable])) $this->errorType['referencedColumns'] = 'empty';
		
		// Actions
		if(!in_array($this->deleteAction, static::$actions)) {
			$this->errorType['deleteAction'] = 'invalid';
		}
		
		if(!in_array($this->updateAction, static::$actions)) {
			$this->errorType['updateAction'] = 'invalid';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDatabaseForm::validateTableName()
	 */
	protected function validateTableName() {
		if(!isset($this->tables[$this->sqlTable])) {
			$this->errorType['sqlTable'] = 'invalid';
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		// Get the automatically generated index name
		$this->sqlIndex = ProjectDatabaseCache::getInstance()->getGenericIndexName($this->sqlTable, reset($this->selectedColumns[$this->sqlTable]), 'fk');
		
		// Try to create the foreign key
		try {
			WCF::getDB()->getEditor()->addForeignKey(
				$this->sqlTable,
				$this->sqlIndex,
				array(
					'columns' => implode(',', $this->selectedColumns[$this->sqlTable]),
					'referencedTable' => $this->referencedSqlTable,
					'referencedColumns' => implode(',', $this->referencedColumns[$this->referencedSqlTable]),
					'ON DELETE' => $this->deleteAction,
					'ON UPDATE' => $this->updateAction
				)
			);
		} catch(DatabaseException $e) {
			if($e->getErrorDesc() == 'Cannot add foreign key constraint') {
				// Get error message
				$sql = "SHOW ENGINE INNODB STATUS";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute();
				$status = $statement->fetchArray()['Status'];
				
				// Start
				$startString = "LATEST FOREIGN KEY ERROR\n------------------------\n";
				$start = mb_strpos($status, $startString) + mb_strlen($startString);
				
				// End
				$endString = "------------\nTRANSACTIONS";
				$end = mb_strpos($status, $endString);
				
				// Cut message from start to end
				$message = mb_substr($status, $start, $end - $start);
			} else {
				switch($e->getErrorNumber()) {
					case self::ER_DUP_KEY:
						$columnNames = array();
						foreach($this->selectedColumns[$this->sqlTable] as $columnName) {
							if(ProjectDatabaseCache::getInstance()->getTable($this->sqlTable)->getColumn($columnName)->hasForeignKey()) {
								$columnNames[] = $columnName;
							}
						}
						$message = WCF::getLanguage()->getDynamicVariable('wcf.project.error.database.foreignKey.databaseException.foreignKeysAlreadyDefined', array('columnNames' => $columnNames));
						break;
					default:
						$message = $e->_getMessage();
				}
				
			}
			
			throw new UserInputException('databaseException', $message);
		}
	
		// Add log entry
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
				'sqlIndex' => $this->sqlIndex
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'actions' => static::$actions,
			'sqlIndex' => $this->sqlIndex,
			'selectedColumns' => $this->selectedColumns,
			'referencedSqlTable' => $this->referencedSqlTable,
			'referencedColumns' => $this->referencedColumns,
			'deleteAction' => $this->deleteAction,
			'updateAction' => $this->updateAction
		));
	}
}
