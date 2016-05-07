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

use wcf\form\AbstractForm;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\data\package\PackageCache;
use wcf\data\project\Project;
use wcf\util\StringUtil;
use wcf\data\package\Package;
use wcf\system\exception\UserInputException;
use wcf\system\exception\UserException;
use wcf\data\project\ProjectEditor;
use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;

/**
 * Form for adding new versions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionAddForm extends AbstractForm {
	/**
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.package';
	
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'add';
	
	/**
	 * @see \wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.system.projects.canManageProjects');
	
	/**
	 * @var \wcf\data\project\Project
	 */
	public $project;
	
	/**
	 * @var string
	 */
	public $targetVersion = "";
	
	/**
	 * @var \wcf\data\project\version\ProjectVersion
	 */
	public $sourceVersion;

	/**
	 * @see \wcf\form\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Get project
		if(isset($_REQUEST['packageID'])) {
			$this->project = Project::getProject($_REQUEST['packageID']);
		}
		
		if($this->project === null) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if(isset($_POST['targetVersion'])) {
			$this->targetVersion = StringUtil::trim($_POST['targetVersion']);
			
			foreach($this->project->getVersions() as $version) {
				if(Package::compareVersion($this->targetVersion, $version->getVersionString(), '>')) {
					if($this->sourceVersion === null) {
						$this->sourceVersion = $version;
					} elseif(Package::compareVersion($this->sourceVersion->getVersionString(), $version->getVersionString(), '>')) {
						$this->sourceVersion = $version;
					}
				}
			}
		}
		
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if(empty($this->targetVersion)) {
			throw new UserInputException('version');
		} elseif(!Package::isValidVersion($this->targetVersion)) {
			throw new UserInputException('version', 'invalid');
		} elseif($this->sourceVersion === null) {
			throw new UserInputException('version', 'tooSmall');
		}
		
		// TODO remove this validation and the info message in the template
		// as soon as merging versions is possible
		
		foreach($this->project->getVersions() as $version) {
			if(!$version->isReleased()) {
				throw new UserInputException('beta', 'All versions have to be exported and released.');
			}
			
			if(Package::compareVersion($this->targetVersion, $version->getVersionString(), '<')) {
				throw new UserInputException('beta', 'New version has to have the highest version number.');
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$editor = new ProjectEditor($this->project);
		$editor->createVersionLog($this->sourceVersion, $this->targetVersion);
		
		$this->saved();
	}
	
	/**
	 * @see \wcf\form\AbstractForm::saved()
	 */
	public function saved() {
		parent::saved();
		
		// Redirect
		$link = LinkHandler::getInstance()->getLink(
			'ProjectVersionAdd',
			array('isACP' => true),
			'packageID=' . $this->project->packageID . '&success=true'
		);
		HeaderUtil::redirect($link);
		exit;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'project' => $this->project,
			'targetVersion' => $this->targetVersion
		));
		
		// Success message
		if(isset($_GET['success'])) {
			WCF::getTPL()->assign('success', true);
		}
	}
}
