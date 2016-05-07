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
use wcf\system\style\StyleHandler;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\system\cache\builder\ProjectDatabaseCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseLogCacheBuilder;

/**
 * Abstract form which handles all similarities among database manipulations.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class AbstractProjectDatabaseForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\database\log\ProjectDatabaseLogAction';
	
	/**
	 * A regular expression used to validate table, column and index names.
	 * 
	 * @var \wcf\system\Regex
	 */
	protected static $regex;
	
	/**
	 * Available tables.
	 *
	 * @var array
	*/
	public $tables;

	/**
	 * Selected table.
	 *
	 * @var string
	 */
	public $sqlTable;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Get tables
		$this->tables = ProjectDatabaseCache::getInstance()->getTables();
		
		// Create regex
		if(static::$regex == null) {
			static::$regex = Regex::compile('[a-zA-Z_][a-zA-Z0-9_]*');
		}
		
		// Get table name
		if(isset($_REQUEST['sqlTable'])) {
			$this->sqlTable = StringUtil::trim($_REQUEST['sqlTable']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		$this->validateTableName();
	}

	/**
	 * Validates the table parameter.
	 */
	protected function validateTableName() {
		if(empty($this->sqlTable)) {
			$this->errorType['sqlTable'] = 'empty';
		}
		elseif(!isset($this->tables[$this->sqlTable])) {
			$this->errorType['sqlTable'] = 'invalid';
		}
	}

	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'tables' => $this->tables,
			'sqlTable' => $this->sqlTable
		));
	}
}
