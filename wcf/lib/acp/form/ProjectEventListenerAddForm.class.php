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
use wcf\system\cache\builder\EventListenerCacheBuilder;
use wcf\system\cache\builder\ProjectEventListenerCacheBuilder;
use wcf\util\ProjectUtil;

/**
 * Implementation of the database object form for event listeners.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectEventListenerAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\event\listener\ProjectEventListenerAction';
	
	/**
	 * @var string
	 */
	public $listenerClassName = '\wcf\system\event\listener\\';
	
	/**
	 * @var string
	 */
	public $eventName = '';
	
	/**
	 * @var string
	 */
	public $eventClassName = '';
	
	/**
	 * @var string
	 */
	public $environment = 'user';
	
	/**
	 * @var integer
	 */
	public $inherit = 0;
	
	/**
	 * @var integer
	 */
	public $niceValue = 0;
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Environment (admin or user)
		if(isset($_POST['environment']) && $_POST['environment'] == 'admin') {
			$this->environment = 'admin';
		} else {
			$this->environment = 'user';
		}
		
		// Listener class name
		if(isset($_POST['listenerClassName']) && is_string($_POST['listenerClassName'])) {
			$this->listenerClassName = trim(StringUtil::trim($_POST['listenerClassName']), '\\');
		}
		
		// Event name
		if(isset($_POST['eventName']) && is_string($_POST['eventName'])) {
			$this->eventName = StringUtil::trim($_POST['eventName']);
		}
		
		// Event class name
		if(isset($_POST['eventClassName']) && is_string($_POST['eventClassName'])) {
			$this->eventClassName = trim(StringUtil::trim($_POST['eventClassName']), '\\');
		}
		
		// Inherit
		$this->inherit = isset($_POST['inherit']);
		
		// Nice value
		if(isset($_POST['niceValue'])) {
			$this->niceValue = (int)$_POST['niceValue'];
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// TODO when validating if class exists and class has errors (e.g. implements non existent interface) currently fatal error is thrown
		
		// Validate event class name
		if(empty($this->eventClassName)) {
			$this->errorType['eventClassName'] = 'empty';
		} elseif(!class_exists($this->eventClassName) && !interface_exists($this->eventClassName)) {
			$this->errorType['eventClassName'] = 'notFound';
		}
		
		// Validate listener class name
		if(empty($this->listenerClassName)) {
			$this->errorType['listenerClassName'] = 'empty';
		} elseif(!class_exists($this->listenerClassName)) {
			$this->errorType['listenerClassName'] = 'notFound';
		} else {
			$class = new \ReflectionClass($this->listenerClassName);
			if(!$class->implementsInterface('\wcf\system\event\listener\IParameterizedEventListener')) {
				$this->errorType['listenerClassName'] = 'interface';
			}
		}
		
		// Validate event name
		if(empty($this->eventName)) {
			$this->errorType['eventName'] = 'empty';
		}
		
		// Validate nice value
		if($this->niceValue > PROJECT_MANAGER_EVENT_LISTENER_NICE_MAX || $this->niceValue < PROJECT_MANAGER_EVENT_LISTENER_NICE_MIN) {
			$this->errorType['niceValue'] = 'outOfRange';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	listenerID
			FROM	wcf".WCF_N."_event_listener
			WHERE	environment = ?
			AND	eventClassName COLLATE utf8_bin = ?
			AND	eventName COLLATE utf8_bin = ?
			AND	listenerClassName COLLATE utf8_bin = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->environment, $this->eventClassName, $this->eventName, $this->listenerClassName));
		
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
				'environment' => $this->environment,
				'eventClassName' => $this->eventClassName,
				'eventName' => $this->eventName,
				'listenerClassName' => $this->listenerClassName,
				'inherit' => (int) $this->inherit,
				'niceValue' => $this->niceValue
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
			'listenerID' => $this->objectID,
			'environment' => $this->environment,
			'eventName' => $this->eventName,
			'eventClassName' => $this->eventClassName,
			'listenerClassName' => $this->listenerClassName,
			'inherit' => $this->inherit,
			'niceValue' => $this->niceValue
		));
	}
}
