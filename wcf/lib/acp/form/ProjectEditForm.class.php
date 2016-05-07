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

use wcf\data\package\Package;
use wcf\form\AbstractForm;
use wcf\data\package\PackageAction;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\system\exception\IllegalLinkException;
use wcf\data\package\PackageCache;
use wcf\data\project\Project;
use wcf\system\language\I18nHandler;
use wcf\data\project\ProjectAction;

/**
 * Edits a project.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectEditForm extends ProjectAddForm {
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'edit';
	
	/**
	 * @var \wcf\data\project\Project
	 */
	public $project;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if(isset($_REQUEST['id'])) {
			$this->packageID = intval($_REQUEST['id']);
			$this->project = Project::getProject($this->packageID);
		}
		
		// Validate package id
		if($this->project === null) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// Update language variables
		$this->packageName = 'wcf.acp.package.packageName.package'.$this->project->packageID;
		if(I18nHandler::getInstance()->isPlainValue('packageName')) {
			I18nHandler::getInstance()->remove($this->packageName);
			$this->packageName = I18nHandler::getInstance()->getValue('packageName');
		} else {
			I18nHandler::getInstance()->save('packageName', $this->packageName, 'wcf.acp.package', 1);
		}

		$this->packageDescription = 'wcf.acp.package.packageDescription.package'.$this->project->packageID;
		if(I18nHandler::getInstance()->isPlainValue('packageDescription')) {
			I18nHandler::getInstance()->remove($this->packageDescription);
			$this->packageDescription = I18nHandler::getInstance()->getValue('packageDescription');
		} else {
			I18nHandler::getInstance()->save('packageDescription', $this->packageDescription, 'wcf.acp.package', 1);
		}
		
		// Prepare package update action
		$this->objectAction = new ProjectAction(
			array($this->project),
			'update',
			array(
				'data' => array_merge($this->additionalFields, array(
					'package' => $this->identifier,
					'packageDir' => $this->packageDir,
					'packageName' => $this->packageName,
					'packageDescription' => $this->packageDescription,
					'packageDate' => $this->packageDate,
					'installDate' => TIME_NOW,
					'updateDate' => TIME_NOW,
					'packageURL' => $this->packageURL,
					'isApplication' => (int) $this->isApplication,
					'author' => $this->author,
					'authorURL' => $this->authorURL,
					'copyright' => $this->copyright,
					'copyrightURL' => $this->copyrightURL,
					'license' => $this->license,
					'licenseURL' => $this->licenseURL
				))
			)
		);
		$this->objectAction->executeAction();
		
		// If package directory changed move files and update file_log table
		// TODO if an application moves, update application table entry
// 		if($this->project->packageDir != $this->projectDir) {
// 			// select files from file_log
// 			$sql = "SELECT	filename
// 				FROM	wcf".WCF_N."_package_installation_file_log
// 				WHERE	packageID = ?";
// 			$statement = WCF::getDB()->prepareStatement($sql);
// 			$statement->execute(array($this->project->packageID));
			
// 			while($row = $statement->fetchArray()) {
// 				// move each file
// 				rename(FileUtil::addTrailingSlash($this->project->packageDir).$row['filename'], FileUtil::addTrailingSlash($this->projectDir).$row['filename']);
// 			}
			
// 			// update application column in file_log table
// 			if(!$this->project->isApplication) {
// 				// look for the application which uses the new package directory
// 				foreach($this->projects as $package) {
// 					if($package->isApplication && $package->packageDir == $this->projectDir) {
// 						$sql = "UPDATE	wcf".WCF_N."_package_installation_file_log
// 							SET	application = ?
// 							WHERE	packageID = ?";
// 						$statement = WCF::getDB()->prepareStatement($sql);
// 						$statement->execute(array(
// 							Package::getAbbreviation($package->package),
// 							$this->project->packageID
// 						));
// 						break;
// 					}
// 				}
// 			}
// 		}
		
		
		// Update excluded, optionals and requirements
		$sql = "DELETE FROM	wcf".WCF_N."_package_exclusion
			WHERE		packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->project->packageID));
		
		$sql = "DELETE FROM	wcf".WCF_N."_package_requirement
			WHERE		packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->project->packageID));
		
		$sql = "DELETE FROM	wcf".WCF_N."_package_optional
			WHERE		packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->project->packageID));
		
		$this->saveAdditionalPackageData();
		
		// Call saved method
		$this->saved();
	}

	/**
	 * @see \wcf\form\AbstractForm::saved()
	 */
	public function saved() {
		AbstractForm::saved();
		
		WCF::getTPL()->assign('success', 'edit');
	}

	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		// Remove this package from the list of packages
		// The list is used for exclusion, requirements and optionals
		// This package cannot exclude or require itself or be optional for itself
		unset($this->projects[$this->project->packageID]);
		
		if(empty($_POST)) {
			// Get package data
			$this->identifier = $this->project->package;
			$this->packageName = $this->project->packageName;
			$this->packageDescription = $this->project->packageDescription;
			$this->projectDirectory = $this->project->projectDirectory;
			$this->projectVersion = $this->project->packageVersion;
			$this->packageDate = $this->project->packageDate;
			$this->packageURL = $this->project->packageURL;
			$this->parentPackageID = $this->project->parentPackageID;
			$this->isApplication = $this->project->isApplication;
			$this->author = $this->project->author;
			$this->authorURL = $this->project->authorURL;
			$this->copyright = $this->project->copyright;
			$this->copyrightURL = $this->project->copyrightURL;
			$this->license = $this->project->license;
			$this->licenseURL = $this->project->licenseURL;
			$this->projectDir = $this->project->getDirectory();
			
			// Get requirements
			$sql = "SELECT		package.packageID, requirement.version AS packageVersion
				FROM		wcf".WCF_N."_package_requirement requirement
				LEFT JOIN	wcf".WCF_N."_package package
				ON		requirement.requirement = package.packageID
				WHERE		requirement.packageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->project->packageID));
			
			$this->requirements = array();
			while($row = $statement->fetchArray()) {
				$this->requirements[$row['packageID']] = $row['packageVersion'];
			}
			
			// Get optionals
			$sql = "SELECT	optionalID
				FROM	wcf".WCF_N."_package_optional
				WHERE	packageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->project->packageID));
			
			$this->optionals = array();
			while($row = $statement->fetchArray()) {
				$this->optionals[] = $row['optionalID'];
			}

			// Get excluded
			$sql = "SELECT	excludedPackage, excludedPackageVersion
				FROM	wcf".WCF_N."_package_exclusion
				WHERE	packageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->project->packageID));
			
			$this->excluded = array();
			while($row = $statement->fetchArray()) {
				$this->excluded[$row['excludedPackage']] = $row['excludedPackageVersion'];
			}
			
			$this->excludedString = '';
			foreach($this->excluded as $excludedPackage => $excludedPackageVersion) {
				if(!empty($this->excludedString)) $this->excludedString .= "\n";
				$this->excludedString .= $excludedPackage.(!empty($excludedPackageVersion) ? ':'.$excludedPackageVersion : '');
			}
			
			// Init values of I18nHandler
			I18nHandler::getInstance()->setOptions('packageName', 1, $this->packageName, 'wcf.acp.package.packageName.package\d+');
			I18nHandler::getInstance()->setOptions('packageDescription', 1, $this->packageDescription, 'wcf.acp.package.packageDescription.package\d+');
		}
	}

	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		
		if(isset($_GET['success'])) {
			WCF::getTPL()->assign('success', 'add');
		}
		
		WCF::getTPL()->assign(array(
			'project' => $this->project,
			'isActiveProject' => $this->project->isActive()
		));
	}
}
