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
use wcf\data\project\ProjectEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\util\ProjectUtil;

/**
 * Implementation of the database object form for user notification events.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserNotificationEventAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\user\notification\event\ProjectUserNotificationEventAction';
	
	/**
	 * @var array
	 */
	public $objectTypes = array();
	
	/**
	 * @var string
	 */
	public $eventName = '';
	
	/**
	 * @var integer
	 */
	public $objectTypeID = 0;
	
	/**
	 * @var string
	 */
	public $eventClassName = '\wcf\system\user\notification\event\\';
	
	/**
	 * A user has to have one of these permissions to trigger the event.
	 * 
	 * @var string
	 */
	public $permissions = '';
	
	/**
	 * At least one of these options has to be active so the event will trigger.
	 * 
	 * @var string
	 */
	public $options = '';
	
	/**
	 * @var boolean
	 */
	public $preset = false;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Event name
		if(isset($_POST['eventName']) && is_string($_POST['eventName'])) {
			$this->eventName = StringUtil::trim($_POST['eventName']);
		}
		
		// Class name
		if(isset($_POST['className']) && is_string($_POST['className'])) {
			$this->eventClassName = StringUtil::trim($_POST['className']);
		}
		
		// Permissions
		if(isset($_POST['permissions']) && is_string($_POST['permissions'])) {
			$this->permissions = StringUtil::trim($_POST['permissions']);
		}
		
		// Options
		if(isset($_POST['options']) && is_string($_POST['options'])) {
			$this->options = StringUtil::trim($_POST['options']);
		}
		
		// Object type ID
		if(isset($_POST['objectTypeID'])) {
			$this->objectTypeID = intval($_POST['objectTypeID']);
		}
		
		$this->preset = isset($_POST['preset']);
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// TODO validate user notification event parameters
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	eventID
			FROM	wcf".WCF_N."_user_notification_event
			WHERE	eventName COLLATE utf8_bin = ?
			AND	objectTypeID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->eventName, $this->objectTypeID));
		
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
				'eventName' => $this->eventName,
				'objectTypeID' => $this->objectTypeID,
				'className' => $this->eventClassName,
				'permissions' => str_replace(array("\r\n", "\r", "\n"), ",", $this->permissions),
				'options' => $this->options,
				'preset' => (int) $this->preset
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// Read object types before calling
		// parent in order to have the types
		// available in the validation methods
		$this->objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('com.woltlab.wcf.notification.objectType');
		
		// call parent
		parent::readData();
	}

	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'objectTypes' => $this->objectTypes,
			'listenerID' => $this->objectID,
			'eventName' => $this->eventName,
			'objectTypeID' => $this->objectTypeID,
			'className' => $this->eventClassName,
			'permissions' => str_replace(",", "\n", $this->permissions),
			'options' => $this->options,
			'preset' => $this->preset
		));
	}
}
