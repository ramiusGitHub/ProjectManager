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

/**
 * Implementation of the database object form for object type definitions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectObjectTypeDefinitionAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\object\type\definition\ProjectObjectTypeDefinitionAction';
	
	/**
	 * @var string
	 */
	public $definitionName = '';
	
	/**
	 * @var string
	 */
	public $interfaceName = '';
	
	/**
	 * @var string
	 */
	public $categoryName = '';
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Internal name of the definition
		if(isset($_POST['definitionName']) && is_string($_POST['definitionName'])) {
			$this->definitionName = StringUtil::trim($_POST['definitionName']);
		}
		
		// Full qualified interface name
		if(isset($_POST['interfaceName']) && is_string($_POST['interfaceName'])) {
			$this->interfaceName = trim(StringUtil::trim($_POST['interfaceName']), '\\');
		}
		
		// Category name
		if(isset($_POST['categoryName']) && is_string($_POST['categoryName'])) {
			$this->categoryName = StringUtil::trim($_POST['categoryName']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Definition name
		if(empty($this->definitionName)) {
			$this->errorType['definitionName'] = 'empty';
		}
		
		// Interface name
		if(!empty($this->interfaceName) && !interface_exists($this->interfaceName)) {
			$this->errorType['interfaceName'] = 'nonExistent';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	definitionID
			FROM	wcf".WCF_N."_object_type_definition
			WHERE	definitionName COLLATE utf8_bin = ?
			AND	interfaceName COLLATE utf8_bin = ?
			AND	categoryName COLLATE utf8_bin = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->definitionName,
			$this->interfaceName,
			$this->categoryName
		));
		
		if($statement->getAffectedRows() > 0) {
			$this->errorType['duplicate'] = true;
		}
	}
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'definitionName' => $this->definitionName,
				'interfaceName' => $this->interfaceName,
				'categoryName' => $this->categoryName
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
			'definitionID' => $this->objectID,
			'definitionName' => $this->definitionName,
			'interfaceName' => $this->interfaceName,
			'categoryName' => $this->categoryName
		));
	}
}
