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

use wcf\data\project\ProjectList;
use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;
use wcf\util\StringUtil;
use wcf\data\project\ProjectDataAction;
use wcf\data\project\Project;
use wcf\system\event\EventHandler;

/**
 * Abstract form which provides a common base for all data forms.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectDataForm extends AbstractForm {
	/**
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.package';
	
	/**
	 * @see \wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.system.projects.canManageProjects');
	
	/** 
	 * @var string
	 */
	protected static $className = '';
	
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'add';
	
	/**
	 * @var array<\wcf\data\project\Project>
	 */
	public $projects = array();
	
	/**
	 * @var int
	 */
	public $packageID;
	
	/**
	 * @var string
	 */
	public $forwardParameters = array();

	/**
	 * @see \wcf\form\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		// Get packageID
		if(isset($_REQUEST['packageID'])) {
			$this->packageID = intval($_REQUEST['packageID']);
		}

		// Get list of projects
		$this->projects = ProjectList::getProjectsFromCache();
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if(isset($_POST['packageID'])) {
			$this->packageID = intval($_POST['packageID']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		$this->validateDuplicate();
		
		if(!isset($this->projects[$this->packageID])) {
			$this->errorType['packageID'] = 'invalid';
		} else {
			if($this->projects[$this->packageID]->getCurrentVersion()->isReleased()) {
				$this->errorType['projectVersion'] = 'isReleased';
			}
		}
	}
	
	/**
	 * Checks if an entry with the entered data already exists.
	 */
	protected function validateDuplicate() {}
	
	/**
	 * @see	\wcf\form\IForm::submit()
	 */
	public function submit() {
		// Call submit event
		EventHandler::getInstance()->fireAction($this, 'submit');
	
		$this->readFormParameters();
	
		try {
			$this->validate();
			
			if(!empty($this->errorType)) {
				throw new UserInputException('errors', $this->errorType);
			}
			
			// No errors
			$this->save();
			$this->saved();
		} catch (UserInputException $e) {
			$this->errorField = $e->getField();
			$this->errorType = $e->getType();
		}
	}
	
	/**
	 * @see \wcf\form\AbstractForm::saved()
	 */
	protected function saved() {
		parent::saved();
		
		// Add forward parameters
		$this->addForwardParameters();
		
		// Add success parameter
		$this->forwardParameters[] = 'success=true';
		
		// Add package id parameter
		if($this->action == 'add') {
			$this->forwardParameters[] = 'packageID=' . $this->packageID;
		}

		// Redirect
		$className = get_class($this);
		$link = LinkHandler::getInstance()->getLink(
			mb_substr($className, mb_strrpos($className, '\\') + 1, -4),
			array('isACP' => true),
			implode('&', $this->forwardParameters)
		);
		HeaderUtil::redirect($link);
		exit;
	}
	
	/**
	 * Adds parameters to the forward parameters array before redirection.
	 */
	protected function addForwardParameters() { }
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// Validate packageID
		if(!isset($this->projects[$this->packageID])) {
			throw new IllegalLinkException();
		}
		
		parent::readData();
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'project' => $this->projects[$this->packageID],
			'projects' => $this->projects,
			'packageID' => $this->packageID
		));
		
		// Show success message
		if(isset($_REQUEST['success'])) {
			WCF::getTPL()->assign('success', true);
		}
	}
}
