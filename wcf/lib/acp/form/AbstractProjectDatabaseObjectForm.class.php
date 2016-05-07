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
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\data\project\ProjectDataAction;

/**
 * Abstract form which handles all similarities among database objects.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectDatabaseObjectForm extends AbstractProjectDataForm {
	/**
	 * @var \wcf\data\DatabaseObject
	 */
	public $object;
	
	/**
	 * @var mixed
	 */
	public $objectID = 0;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Get objectID
		if(isset($_GET['id'])) {
			$this->objectID = $_GET['id'];
		}
		
		// Get object
		if($this->objectID > 0) {
			$baseClassName = $this->getBaseClassName();
			$this->object = new $baseClassName($this->objectID);
			$this->objectID = $this->object->getObjectID();
			
			// Validate objectID
			if(!$this->objectID) {
				throw new IllegalLinkException();
			}
			
			// Get reflection object
			$reflection = new \ReflectionObject($this);
			
			// Read object data
			foreach($this->object->getData() as $col => $value) {
				// Has property
				if(!$reflection->hasProperty($col)) {
					continue;
				}
				
				// Is static property
				$property = $reflection->getProperty($col);
				if($property->isStatic()) {
					continue;
				}
				
				// Skip properties which are declared in parent classes
				// (like templateName in case of Template objects)
				if(!$property->getDeclaringClass()->isSubclassOf('\wcf\acp\form\AbstractProjectDatabaseObjectForm')) {
					continue;
				}
				
				// Assign value
				$this->$col = $value;
			}
		}
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// Get package id of object
		if($this->packageID === null && $this->object !== null) {
			$this->packageID = $this->object->packageID;
		}
		
		// Call parent
		parent::readData();
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// Either create or update object
		if($this->action == 'add') {
			
			// Create object
			$action = new static::$className(
				array(),
				'create',
				array_merge(
					$this->additionalFields,
					array(
						'data' => $this->getActionData()
					)
				)
			);
			$result = $action->executeAction();
			$this->object = $result['returnValues'];
		} else {
			// Update object
			$action = new static::$className(
				array($this->object),
				'update',
				array_merge(
					$this->additionalFields,
					array(
						'data' => $this->getActionData()
					)
				)
			);
			$action->executeAction();
		}
	}
	
	/**
	 * Returns an array with the object specific data.
	 * 
	 * @return array<mixed>
	 */
	public function getActionData() {
		return array(
			'packageID' => $this->packageID
		);
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::addForwardParameters()
	 */
	protected function addForwardParameters() {
		parent::addForwardParameters();
		
		if($this->action == 'edit') {
			$this->forwardParameters[] = 'id=' . $this->object->getObjectID();
		}
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'object' => $this->object,
			'objectID' => $this->objectID
		));	
	}
	
	/**
	 * Returns the name of the class affected by the action.
	 *
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getBaseClassName()
	 * @return string
	 */
	protected function getBaseClassName() {
		if(method_exists(static::$className, 'getBaseClassName')) {
			return call_user_func(array(static::$className, 'getBaseClassName'));
		} else {
			return mb_substr(static::$className, 0, -6);
		}
	}
}
