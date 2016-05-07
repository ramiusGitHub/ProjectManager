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

use wcf\data\package\PackageEditor;
use wcf\data\language\LanguageList;
use wcf\data\language\Language;
use wcf\system\WCF;
use wcf\system\cache\builder\LanguageCacheBuilder;
use wcf\system\request\LinkHandler;
use wcf\data\package\PackageCache;
use wcf\data\package\PackageAction;
use wcf\data\package\Package;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\util\HeaderUtil;
use wcf\data\project\Project;
use wcf\system\language\I18nHandler;
use wcf\data\project\ProjectAction;
use wcf\util\DateUtil;

/**
 * Adds a project to the WCF.
 * // TODO add support for creating applications (Anwendungen)
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectAddForm extends AbstractForm {
	/**
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.package.project.add';

	/**
	 * @see wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.system.projects.canManageProjects');

	/**
	 * @see wcf\page\AbstractPage::$action
	 */
	public $action = 'add';
	
	/**
	 * Id of the currently handled package
	 * 
	 * @var integer
	 */
	public $packageID = 0;
	
	/**
	 * List of all installed packages
	 * 
	 * @var array<\wcf\data\package\Package>
	 */
	public $packages;
	
	/**
	 * List of all available languages
	 * 
	 * @var array<\wcf\data\language\Language>
	 */
	public $languages;
	
	/**
	 * Package's identifier
	 * 
	 * @var string
	 * @see \wcf\data\package\Package::isValidPackageName
	 */
	public $identifier = '';
	
	/**
	 * Human readable package name
	 * 
	 * @var string
	 */
	public $packageName = '';

	/**
	 * Package's description
	 * 
	 * @var string
	 */
	public $packageDescription = '';
	
	/**
	 * The directory of the project
	 * 
	 * @var string
	 */
	public $projectDirectory = '';
	
	/**
	 * Current version
	 * 
	 * @var string
	 */
	public $packageVersion = '1.0.0';
	
	/**
	 * Package's production time
	 * 
	 * @var integer
	 */
	public $packageDate = TIME_NOW;
	
	/**
	 * Package's website
	 * 
	 * @var string
	 */
	public $packageURL = '';
	
	/**
	 * If true, the package is an application
	 * 
	 * @var boolean
	 */
	public $isApplication = false;
	
	/**
	 * Base directory of an application
	 * 
	 * @var string
	 */
	public $packageDir = '';
	
	/**
	 * Author's name
	 * 
	 * @var string
	 */
	public $author = '';
	
	/**
	 * Author's website
	 * 
	 * @var string
	 */
	public $authorURL = '';
	
	/**
	 * @var string
	 */
	public $copyright = '';
	
	/**
	 * @var string
	 */
	public $copyrightURL = '';
	
	/**
	 * @var string
	 */
	public $license = '';
	
	/**
	 * @var string
	 */
	public $licenseURL = '';
	
	/**
	 * Packages this package requires
	 * 
	 * @var array<integer>
	 */
	public $requirements = array();
	
	/**
	 * Optional packages for this package
	 * 
	 * @var array<integer>
	 */
	public $optionals = array();
	
	/**
	 * List of excluded packages and their versions
	 * 
	 * @var string
	 */
	public $excludedString = '';
	
	/**
	 * List of excluded packages and their versions
	 * 
	 * @var array
	 */
	public $excluded = array();
	
	/**
	 * @see \wcf\form\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		$this->languages = LanguageCacheBuilder::getInstance()->getData(array(), 'languages');
		$this->packages = PackageCache::getInstance()->getPackages()['packages'];
		
		// Register language variable
		I18nHandler::getInstance()->register('packageName');
		I18nHandler::getInstance()->register('packageDescription');
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Package name and description
		I18nHandler::getInstance()->readValues();
		if(I18nHandler::getInstance()->isPlainValue('packageName')) {
			$this->packageName = I18nHandler::getInstance()->getValue('packageName');
		}
		if(I18nHandler::getInstance()->isPlainValue('packageDescription')) {
			$this->packageDescription = I18nHandler::getInstance()->getValue('packageDescription');
		}
		
		// Package's identifier
		if(isset($_POST['identifier']) && is_string($_POST['identifier'])) {
			$this->identifier = StringUtil::trim($_POST['identifier']);
		}
		
		// Project's directory
		if(isset($_POST['projectDirectory']) && is_string($_POST['projectDirectory'])) {
			$this->projectDirectory = FileUtil::addTrailingSlash(StringUtil::trim($_POST['projectDirectory']));
		}
		
		// Current version
		if(isset($_POST['packageVersion']) && is_string($_POST['packageVersion'])) {
			$this->packageVersion = StringUtil::trim($_POST['packageVersion']);
		}
		
		// Package's website
		if(isset($_POST['packageURL']) && is_string($_POST['packageURL'])) $this->packageURL = StringUtil::trim($_POST['packageURL']);
		
		// Package's production time
		if(isset($_POST['packageDate']) && !empty($_POST['packageDate'])) {
			try{
				DateUtil::validateDate($_POST['packageDate']);
				$dateTime = new \DateTime($_POST['packageDate']);
				$this->packageDate = $dateTime->getTimestamp();
			} catch(SystemException $e) {
				
			}
		}
		
		// Author's name
		if(isset($_POST['author']) && is_string($_POST['author'])) $this->author = StringUtil::trim($_POST['author']);
		
		// Author's website
		if(isset($_POST['authorURL']) && is_string($_POST['authorURL'])) $this->authorURL = StringUtil::trim($_POST['authorURL']);
		
		// Additional parameters
		if(isset($_POST['copyright']) && is_string($_POST['copyright'])) {
			$this->copyright = StringUtil::trim($_POST['copyright']);
		}
		
		if(isset($_POST['copyrightURL']) && is_string($_POST['copyrightURL'])) {
			$this->copyrightURL = StringUtil::trim($_POST['copyrightURL']);
		}
		
		if(isset($_POST['license']) && is_string($_POST['license'])) {
			$this->license = StringUtil::trim($_POST['license']);
		}
		
		if(isset($_POST['licenseURL']) && is_string($_POST['licenseURL'])) {
			$this->licenseURL = StringUtil::trim($_POST['licenseURL']); 
		}
		
		// Is the package an application?
		$this->isApplication = isset($_POST['isApplication']);
		
		// Directory of the application
		if($this->isApplication && isset($_POST['packageDir']) && is_string($_POST['packageDir'])) {
			$this->packageDir = FileUtil::getRealPath(StringUtil::trim($_POST['packageDir']));
		} else {
			$this->packageDir = '';
		}
		
		// Requirements
		$this->requirements = array();
		if(isset($_POST['requirements']) && is_array($_POST['requirements'])) {
			foreach($_POST['requirements'] as $requirement) {
				if(!$requirement) {
					continue;
				}
				
				$this->requirements[(int) $requirement] = (isset($_POST['requirementsVersion'][(int) $requirement]) ? StringUtil::trim($_POST['requirementsVersion'][(int) $requirement]) : '');
			}
		}
		
		// Optionals
		if(isset($_POST['optionals']) && is_array($_POST['optionals'])) {
			$this->optionals = array_filter(array_map('intval', $_POST['optionals']));
		}
		
		// Exclusion
		if(isset($_POST['excluded']) && is_string($_POST['excluded'])) {
			$this->excludedString = StringUtil::trim($_POST['excluded']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Package name
		if(!I18nHandler::getInstance()->validateValue('packageName')) {
			if (I18nHandler::getInstance()->isPlainValue('packageName')) {
				$this->errorType['packageName'] = 'empty';
			} else {
				$this->errorType['packageName'] = 'multilingual';
			}
		}
		
		// Package description
		if(!I18nHandler::getInstance()->validateValue('packageDescription', false, true)) {
			if(I18nHandler::getInstance()->isPlainValue('packageDescription')) {
				$this->errorType['packageDescription'] = 'empty';
			} else {
				$this->errorType['packageDescription'] = 'multilingual';
			}
		}
		
		// Package identifier
		if(empty($this->identifier)) {
			$this->errorType['identifier'] = 'empty';
		} elseif(mb_strlen($this->identifier) > PROJECT_MANAGER_PACKAGE_MAX_LENGTH) {
			$this->errorType['identifier'] = 'tooLong';
		} elseif(!Package::isValidPackageName($this->identifier)) {
			$this->errorType['identifier'] = 'invalid';
		} else {
			$this->validatePackageNameAlreadyInUse();
		}
		
		// Project directory
		// TODO extend validation of project directory
		if(empty($this->projectDirectory)) {
			$this->errorType['projectDirectory'] = 'empty';
		} else {
			if(file_exists($this->projectDirectory)) {
				if(!is_writable($this->projectDirectory)) {
					$this->errorType['projectDirectory'] = 'notWriteable';
				}
			} elseif(!FileUtil::makePath($this->projectDirectory)) {
				$this->errorType['projectDirectory'] = 'notMakeable';  
			}
		}

		// Version
		if(empty($this->packageVersion)) {
			$this->errorType['packageVersion'] = 'empty';
		} elseif(mb_strlen($this->packageVersion) > PROJECT_MANAGER_VERSION_MAX_LENGTH) {
			$this->errorType['packageVersion'] = 'tooLong';
		} elseif(!Package::isValidVersion($this->packageVersion)) {
			$this->errorType['packageVersion'] = 'invalid';
		}

		// Package url
		if(mb_strlen($this->packageURL) > PROJECT_MANAGER_PACKAGEURL_MAX_LENGTH) $this->errorType['packageURL'] = 'tooLong';
		
		// Author(url)
		if(mb_strlen($this->author) > PROJECT_MANAGER_AUTHOR_MAX_LENGTH) $this->errorType['author'] = 'tooLong';
		if(mb_strlen($this->authorURL) > PROJECT_MANAGER_AUTHORURL_MAX_LENGTH) $this->errorType['authorURL'] = 'tooLong';
		
		// Application path
		if($this->isApplication) {
			if(empty($this->packageDir)) {
				$this->errorType['packageDir'] = 'empty';
			} else {
				if(file_exists($this->packageDir)) {
					if(!is_writable($this->packageDir)) {
						$this->errorType['packageDir'] = 'notWriteable';
					} else {
						foreach($this->packages as $p) {
							if($p['standalone'] && FileUtil::getRealPath(WCF_DIR.$p['packageDir']) == $this->packageDir) $this->errorType['packageDir'] = 'alreadyInUse';
						}
					}
				} elseif(!FileUtil::makePath($this->packageDir)) {
					$this->errorType['packageDir'] = 'notMakeable';  
				}
			}
		}
		
		// Relativize the package directory 
		if(!empty($this->packageDir)) {
			$this->packageDir = FileUtil::getRelativePath(WCF_DIR, $this->packageDir);
		}
		
		if($this->packageDir == './') {
			$this->packageDir = '';
		}
		
		// Requirement versions
		foreach($this->requirements as $requirement => $version) {
			if(!empty($version) && !Package::isValidVersion($version)) {
				$this->errorType['requirementVersion'.$requirement] = 'invalid';
			}
		}
			
		// Split excluded packages string into array parts
		$this->excluded = array();
		foreach(preg_split("/(\r\n)+|(\n|\r)+/", $this->excludedString, -1,  PREG_SPLIT_NO_EMPTY) as $line) {
			$line = explode(':', $line);
			$this->excluded[] = array(
				'excludedPackage' => $line[0],
				'excludedPackageVersion' => (isset($line[1]) ? $line[1] : '') 
			);
			
			// validate excluded package name
			if(!Package::isValidPackageName($line[0])) $this->errorType['excluded'] = 'invalid';
		}
		
		// Throw exception if at least one error was found
		if(!empty($this->errorType)) {
			throw new UserInputException('options', $this->errorType);
		}
	}
	
	/**
	 * Checks if the entered package identifier is already in use.
	 */
	public function validatePackageNameAlreadyInUse() {
		foreach($this->packages as $package) {
			if($package->package == $this->identifier && $package->packageID != $this->packageID) {
				$this->errorType['identifier'] = 'alreadyInUse';
				break;
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// Prepare package create action
		$this->objectAction = new ProjectAction(
			array(),
			'create',
			array(
				'data' => array_merge(
					$this->additionalFields,
					array(
						'package' => $this->identifier,
						'packageDir' => $this->packageDir,
						'projectDirectory' => $this->projectDirectory,
						'packageName' => $this->packageName,
						'packageDescription' => $this->packageDescription,
						'packageVersion' => $this->packageVersion,
						'packageDate' => $this->packageDate,
						'installDate' => TIME_NOW,
						'updateDate' => TIME_NOW,
						'packageURL' => $this->packageURL,
						'isApplication' => (int) $this->isApplication,
						'author' => $this->author,
						'authorURL' => $this->authorURL,
						'isActiveProject' => true,
						'copyright' => $this->copyright,
						'copyrightURL' => $this->copyrightURL,
						'license' => $this->license,
						'licenseURL' => $this->licenseURL
					)
				)
			)
		);
		
		// Execute package create action
		$result = $this->objectAction->executeAction();
		$newPackage = $result['returnValues'];
		
		// Get package id
		$this->packageID = $newPackage->packageID;
		
		// Create language variables
		$updates = array();
		
		if(!I18nHandler::getInstance()->isPlainValue('packageName')) {
			$languageItem = 'wcf.acp.package.packageName.package' . $this->packageID;
			
			I18nHandler::getInstance()->save(
				'packageName',
				$languageItem,
				'wcf.acp.package',
				1
			);
			$updates['data']['packageName'] = $languageItem;
		}

		if(!I18nHandler::getInstance()->isPlainValue('packageDescription')) {
			$languageItem = 'wcf.acp.package.packageDescription.package' . $this->packageID;
			
			I18nHandler::getInstance()->save(
				'packageDescription',
				$languageItem,
				'wcf.acp.package',
				1
			);
			$updates['data']['packageDescription'] = $languageItem;
		}
		
		// Execute immediate update
		if(!empty($updates)) {
			$action = new PackageAction(array($newPackage), 'update', $updates);
			$action->executeAction();
		}
		
		// Save additional data
		$this->saveAdditionalPackageData();
		
		// Call saved method
		$this->saved();
	}
	
	/**
	 * Saves excluded, required and optional packages.
	 * No abstraction by the WCF is available for this.
	 * // TODO make own abstraction?
	 */
	protected function saveAdditionalPackageData() {
		// Save excluded packages
		if(!empty($this->excluded)) {
			$sql = "INSERT INTO	wcf".WCF_N."_package_exclusion
						(packageID, excludedPackage, excludedPackageVersion)
				VALUES		(?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
				
			foreach($this->excluded as $excluded) {
				$statement->execute(array($this->packageID, $excluded['excludedPackage'], $excluded['excludedPackageVersion']));
			}
		}
		
		// Save required packages
		if(!empty($this->requirements)) {
			$sql = "INSERT INTO	wcf".WCF_N."_package_requirement
						(packageID, requirement, version)
				VALUES		(?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
				
			foreach($this->requirements as $requirementID => $packageVersion) {
				$version = ($packageVersion == PackageCache::getInstance()->getPackage($requirementID)->packageVersion ? '' : $packageVersion);
				$statement->execute(array($this->packageID, $requirementID, $version));
			}
		}
		
		// Save optional packages
		if(!empty($this->optionals)) {
			$sql = "INSERT INTO	wcf".WCF_N."_package_optional
						(packageID, optionalID)
				VALUES		(?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
		
			foreach($this->optionals as $optionalID) {
				$statement->execute(array($this->packageID, $optionalID));
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::saved()
	 */
	public function saved() {
		parent::saved();
		
		$link = LinkHandler::getInstance()->getLink(
			'ProjectEdit',
			array('id' => $this->packageID),
			'success=1'
		);
		HeaderUtil::redirect($link);
		exit;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// Get default package dir
		if(empty($this->packageDir)) {
			$this->packageDir = FileUtil::getRealPath(WCF_DIR);
		}
		
		// Assign language variables
		I18nHandler::getInstance()->assignVariables();
		
		// Assign variables
		WCF::getTPL()->assign(array(
			'languages' => $this->languages,
			'packages' => $this->packages,
			'identifier' => $this->identifier,
			'projectDirectory' => $this->projectDirectory,
			'packageVersion' => $this->packageVersion,
			'packageURL' => $this->packageURL,
			'author' => $this->author,
			'authorURL' => $this->authorURL,
			'copyright' => $this->copyright,
			'copyrightURL' => $this->copyrightURL,
			'license' => $this->license,
			'licenseURL' => $this->licenseURL,
			'isApplication' => $this->isApplication,
			'packageDir' => $this->packageDir,
			'packageDate' => $this->packageDate,
			'requirements' => $this->requirements,
			'optionals' => $this->optionals,
			'excluded' => $this->excludedString,
			'packageID' => $this->packageID
		));
	}
}
