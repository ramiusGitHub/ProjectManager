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
use wcf\data\project\ProjectEditor;
use wcf\system\exception\IllegalLinkException;
use wcf\data\project\database\ProjectDatabaseAccess;
use wcf\data\project\database\ProjectDatabaseCache;

/**
 * Edit form for database columns.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDatabaseColumnEditForm extends ProjectDatabaseColumnAddForm {
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'edit';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	protected function validateDuplicate() {
		if($this->sqlColumn != $this->object->sqlColumn) {
			parent::validateDuplicate();
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		// Get column object
		$column = $this->getColumn();
		
		// Try to alter the column
		try {
			WCF::getDB()->getEditor()->alterColumn(
				$this->sqlTable,
				$this->object->sqlColumn,
				$this->sqlColumn,
				$column->toArray()
			);
		} catch(DatabaseException $e) {
			throw new UserInputException('databaseException', $e->_getMessage());
		}
		
		// Update log entry
		AbstractProjectDatabaseObjectForm::save();
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if(empty($_POST)) {
			// Get column
			$column = ProjectDatabaseCache::getInstance()->getTable($this->sqlTable)->getColumn($this->sqlColumn);
			
			$this->type = $column->getType();
			$this->decimals = $column->getDecimals();
			$this->values = $column->getValues();
			$this->length = $column->getLength();
			$this->notNull = $column->getNotNull();
			$this->autoIncrement = $column->getAutoIncrement();
			$this->key = $column->getKey();
			
			if($column->getDefault() === null && !$this->notNull) {
				$this->defaultNull = true;
			} elseif($column->getDefault() == 'CURRENT_TIMESTAMP' && $this->type == 'timestamp') {
				$this->defaultCurrentTimestamp = true;
			} else {
				$this->default = $column->getDefault();
			}
			
			$this->noDefault = ($this->default === null && $this->notNull);
		}
	}
}
