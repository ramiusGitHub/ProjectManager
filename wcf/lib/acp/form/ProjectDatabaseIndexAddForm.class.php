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
use wcf\data\project\ProjectEditor;
use wcf\data\project\database\ProjectDatabaseCache;

/**
 * Implementation of the database form for database indices.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseIndexAddForm extends AbstractProjectDatabaseForm {
	/**
	 * Available keys
	 * 
	 * @var array<string>
	 */
	public static $keys = array(
		'INDEX',
		'UNIQUE',
		'FULLTEXT'
		// Primary keys have to be added via ColumnAdd and ColumnEdit forms.
		// The SQLParser only allows primary keys on package's own tables
		// and only on one column. 
		// 'PRIMARY'
	);
	
	/**
	 * Index name
	 * 
	 * @var string
	 */
	public $sqlIndex;
	
	/**
	 * Selected key
	 * 
	 * @var string
	 */
	public $key;
	
	/**
	 * @var array<string>
	 */
	public $columns;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Name of the index
		if(isset($_POST['sqlIndex'])) {
			$this->sqlIndex = StringUtil::trim($_POST['sqlIndex']);
		}
		
		// Columns to be indexed
		foreach($this->tables as $tableName => $table) {
			if(isset($_POST['columns'][$tableName])) $this->columns[$tableName] = $_POST['columns'][$tableName];
		}
		
		// Type of the index
		if(isset($_POST['key'])) {
			$this->key = StringUtil::trim($_POST['key']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Index name
		if(mb_substr($this->sqlIndex, -3) == '_fk') {
			$this->errorType['sqlIndex'] = 'invalid';
		}
		
		// Indexed columns
		if(!isset($this->columns[$this->sqlTable]) || empty($this->columns[$this->sqlTable])) {
			$this->errorType['columns'] = 'empty';
		}
		
		// Key
		if(!in_array($this->key, static::$keys)) {
			$this->errorType['key'] = 'invalid';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDatabaseForm::validateTableName()
	 */
	protected function validateTableName() {
		if(!isset($this->tables[$this->sqlTable])) {
			$this->errorType['tableName'] = 'invalid';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		if(!empty($this->sqlTable) && !empty($this->sqlIndex)) {
			$indices = ProjectDatabaseCache::getInstance()->getTable($this->sqlTable)->getIndices();
			
			if(isset($indices[$this->sqlIndex])) {
				$this->errorType['sqlIndex'] = 'duplicate';
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		// Get the automatically generated index name
		if(empty($this->sqlIndex)) {
			$this->sqlIndex = ProjectDatabaseCache::getInstance()->getGenericIndexName($this->sqlTable, reset($this->columns[$this->sqlTable]));
		}
		
		// Try to create the index
		try {
			WCF::getDB()->getEditor()->addIndex(
				$this->sqlTable,
				$this->sqlIndex,
				array(
					'type' => $this->key,
					'columns' => implode(',', $this->columns[$this->sqlTable])
				)
			);
		} catch(DatabaseException $e) {
			throw $e;
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
			'keys' => static::$keys,
			'key' => $this->key,
			'sqlIndex' => $this->sqlIndex,
			'columns' => $this->columns
		));
	}
}
