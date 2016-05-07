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
use wcf\util\ProjectUtil;
use wcf\system\cache\builder\TemplateListenerCacheBuilder;
use wcf\system\cache\builder\TemplateListenerCodeCacheBuilder;
use wcf\system\Regex;

/**
 * Implementation of the database object form for template listeners.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectTemplateListenerAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\template\listener\ProjectTemplateListenerAction';
	
	/**
	 * @var string
	 */
	public $name = '';
	
	/**
	 * @var string
	 */
	public $environment = 'user';
	
	/**
	 * @var string
	 */
	public $listenerTemplateName = '';
	
	/**
	 * @var string
	 */
	public $eventName = '';
	
	/**
	 * @var string
	 */
	public $templateCode = '';
	
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
		if(isset($_POST['environment'])) {
			$this->environment = 'admin';
		}
		
		// Internal name
		if(isset($_POST['name']) && is_string($_POST['name'])) {
			$this->name = trim(StringUtil::trim($_POST['name']), '\\');
		}
		
		// Event name
		if(isset($_POST['eventName']) && is_string($_POST['eventName'])) {
			$this->eventName = StringUtil::trim($_POST['eventName']);
		}
		
		// Listener template name
		if(isset($_POST['listenerTemplateName']) && is_string($_POST['listenerTemplateName'])) {
			$this->listenerTemplateName = trim(StringUtil::trim($_POST['listenerTemplateName']), '\\');
		}
		
		// Template code
		if(isset($_POST['templateCode']) && is_string($_POST['templateCode'])) {
			$this->templateCode = StringUtil::trim($_POST['templateCode']);
		}
		
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
		
		// Listener template name
		if(empty($this->listenerTemplateName)) {
			$this->errorType['listenerTemplateName'] = 'empty';
		}
		
		// Event name
		if(empty($this->eventName)) {
			$this->errorType['eventName'] = 'empty';
		}
		
		// Template code
		if(empty($this->templateCode)) {
			$this->errorType['templateCode'] = 'empty';
		}
		
		// Nice value
		if($this->niceValue > PROJECT_MANAGER_TEMPLATE_LISTENER_NICE_MAX || $this->niceValue < PROJECT_MANAGER_TEMPLATE_LISTENER_NICE_MIN) {
			$this->errorType['niceValue'] = 'outOfRange';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	listenerID
			FROM	wcf".WCF_N."_template_listener
			WHERE	packageID = ?
			AND	name COLLATE utf8_bin = ?
			AND	templateName COLLATE utf8_bin = ?
			AND	eventName COLLATE utf8_bin = ?
			AND	environment = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->packageID,
			$this->name,
			$this->listenerTemplateName,
			$this->eventName,
			$this->environment
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
				'environment' => $this->environment,
				'name' => $this->name,
				'eventName' => $this->eventName,
				'templateName' => $this->listenerTemplateName,
				'templateCode' => $this->templateCode,
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
			'name' => $this->name,
			'environment' => $this->environment,
			'eventName' => $this->eventName,
			'listenerTemplateName' => $this->listenerTemplateName,
			'templateCode' => $this->templateCode,
			'niceValue' => $this->niceValue
		));
	}
}
