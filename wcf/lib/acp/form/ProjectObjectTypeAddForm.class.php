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
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\object\type\definition\ObjectTypeDefinitionList;
use wcf\data\project\ProjectEditor;
use wcf\util\ProjectUtil;
use wcf\system\cache\builder\ObjectTypeCacheBuilder;

/**
 * Implementation of the database object form for object types.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectObjectTypeAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\object\type\ProjectObjectTypeAction';
	
	/**
	 * @var array<\wcf\data\object\type\definition\ObjectTypeDefinition>
	 */
	public $definitions;
	
	/**
	 * @var integer
	 */
	public $definitionID = 0;
	
	/**
	 * @var string
	 */
	public $objectType = '';
	
	/**
	 * @var string
	 */
	public $objectClassName = '';
	
	/**
	 * @var array<mixed>
	 */
	public $additionalData = array();
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Internal name of the object type
		if(isset($_POST['objectType']) && is_string($_POST['objectType'])) {
			$this->objectType = StringUtil::trim($_POST['objectType']);
			
			// Add prefix
			$package = $this->projects[$this->packageID]->package;
			if(!StringUtil::startsWith($this->objectType, $package)) $this->objectType = $package.'.'.$this->objectType;
		}
		
		// Implemented definition
		if(isset($_POST['definitionID'])) {
			$this->definitionID = intval($_POST['definitionID']);
		}
		
		// Class name
		if(isset($_POST['className']) && is_string($_POST['className'])) {
			$this->objectClassName = trim(StringUtil::trim($_POST['className']), '\\');
		}
		
		// Additional data
		if(isset($_POST['additionalData']) && !empty($_POST['additionalData'])) {
			try {
				$this->additionalData = array();
		
				foreach(preg_split("/(\r\n)+|(\n|\r)+/", $_POST['additionalData'], -1,  PREG_SPLIT_NO_EMPTY) as $row) {
					$parts = explode(":", $row);
					$this->additionalData[StringUtil::trim($parts[0])] = StringUtil::trim($parts[1]);
				}
			} catch (Exception $e) {
				$this->errorType['additionalData'] = 'format';
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Definition
		if(!isset($this->definitions[$this->definitionID])) {
			$this->errorType['definitionID'] = 'invalid';
		}
		
		// Object type
		if(empty($this->objectType)) {
			$this->errorType['objectType'] = 'empty';
		}
		
		// Class name
		// TODO when validating if class exists and class has errors (e.g. implements non existent interface) currently fatal error is thrown
		if(!empty($this->objectClassName) && !class_exists($this->objectClassName)) {
			$this->errorType['className'] = 'nonExistent';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	objectTypeID
			FROM	wcf".WCF_N."_object_type
			WHERE	objectType COLLATE utf8_bin = ?
			AND	definitionID = ?
			AND	packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->objectType,
			$this->definitionID,
			$this->packageID
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
				'definitionID' => $this->definitionID,
				'objectType' => $this->objectType,
				'className' => $this->objectClassName,
				'additionalData' => serialize($this->additionalData)
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// Read definitions before calling parent in order to
		// have the definitions available in the validation methods
		$this->definitions = ObjectTypeCacheBuilder::getInstance()->getData(array(), 'definitions');
		
		// Call parent
		parent::readData();	
	}

	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'definitions' => $this->definitions,
			'definitionID' => $this->definitionID,
			'objectType' => $this->objectType,
			'className' => $this->objectClassName,
			'additionalData' => $this->outputAdditionalData($this->additionalData),
		));
	}
	
	/**
	 * Returns string representation of an array for a textarea.
	 * 
	 * @param array<mixed> $array
	 * @return string $output
	 */
	protected function outputAdditionalData($array) {
		if(empty($array)) return '';
		
		$output = array();
		foreach($array as $key => $value) {
			$output[] = $key . ":" . $value;
		}
		
		return implode("\n", $output);
	}
}
