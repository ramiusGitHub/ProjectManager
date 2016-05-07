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
namespace wcf\data\project\version;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;
use wcf\system\exception\PermissionDeniedException;
use wcf\data\project\Project;
use wcf\system\exception\UserInputException;
use wcf\data\project\ProjectEditor;
use wcf\util\ProjectUtil;
use wcf\system\cache\builder\ProjectVersionCacheBuilder;
use wcf\data\project\ProjectAction;

/**
 * Executes project version related actions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionAction extends AbstractDatabaseObjectAction {
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\project\version\ProjectVersionEditor';
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.system.projects.canManageProjects');
	
	/**
	 * Initialize a new ProjectVersionAction.
	 * 
	 * @param array $objects
	 * @param string $action
	 * @param array $parameters
	 */
	public function __construct(array $objects, $action, array $parameters = array()) {
		parent::__construct($objects, $action, $parameters);
		
		// Add method to reset cache array
		$this->resetCache[] = 'release';
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::update()
	 */
	public function update() {
		parent::update();
		
		if(isset($this->parameters['data']['packageVersion'])) {
			foreach($this->objects as $version) {
				if($version->isCurrentVersion()) {
					$action = new ProjectAction(
						array($version->getProject()),
						'update',
						array(
							'data' => array(
								'packageVersion' => $this->parameters['data']['packageVersion']
							)
						)
					);
					$action->executeAction();
				}
			}
		}
	}
	
	/**
	 * Validates the 'deactivateCurrentVersion' action.
	 */
	public function validateDeactivateCurrentVersion() {
		// Check permissions
		if(!WCF::getSession()->getPermission('admin.system.projects.canManageProjects')) {
			throw new PermissionDeniedException();
		}
		
		// Get data
		$version = $this->getSingleObject();
		
		// Get project
		$project = Project::getProject($version->packageID);
		
		// Validate version
		if($project->getCurrentVersion()->getVersionID() != $version->getVersionID()) {
			throw new UserInputException('versionID');
		}
		
		// Validate deactivation
		// TODO check conflicts
	}
	
	/**
	 * Deactivates the currently active project version.
	 */
	public function deactivateCurrentVersion() {
		// Get editor
		$editor = new ProjectEditor(Project::getProject($version->packageID));
		
		// Deactivate current version
		$editor->deactivateCurrentVersion();
	}
	
	/**
	 * Validates the 'activateVersion' action.
	 */
	public function validateActivateVersion() {
		// Check permissions
		if(!WCF::getSession()->getPermission('admin.system.projects.canManageProjects')) {
			throw new PermissionDeniedException();
		}
		
		// Get data
		$version = $this->getSingleObject();
		
		// Get project
		$project = Project::getProject($version->packageID);
		
		// Validate version
		if($project->getCurrentVersion()->getVersionID() == $version->getVersionID()) {
			throw new UserInputException('versionID');
		}
		
		// Validate target version
		// TODO check conflicts
	}
	
	/**
	 * Activates a specific version.
	 */
	public function activateVersion() {
		// Get version
		$version = $this->getSingleObject();
		
		// Get editor
		$editor = new ProjectEditor(Project::getProject($version->packageID));
		
		// Switch version
		$editor->activateVersion($version->getDecoratedObject());
	}
	
	/**
	 * Validates the 'switchToVersion' action.
	 */
	public function validateSwitchToVersion() {
		// Check permissions
		if(!WCF::getSession()->getPermission('admin.system.projects.canManageProjects')) {
			throw new PermissionDeniedException();
		}
		
		// Get data
		$version = $this->getSingleObject();
		
		// Get project
		$project = Project::getProject($version->packageID);
		
		// Validate version
		if($project->getCurrentVersion()->getVersionID() == $version->getVersionID()) {
			throw new UserInputException('versionID');
		}
		
		// Validate target version
		// 1. Check data relations
		// 2. Check database modifications
		
		// Check if foreign ACL options refer to ACL option categories of this package
	}
	
	/**
	 * Switches the project to a specific version.
	 */
	public function switchToVersion() {
		// Get version
		$version = $this->getSingleObject();
		
		// Get editor
		$editor = new ProjectEditor(Project::getProject($version->packageID));
		
		// Switch version
		$editor->switchVersion($version->getDecoratedObject());
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::delete()
	 */
	public function delete() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		foreach($this->objects as $version) {
			if($version->isReleased()) {
				$version->update(array('isDeleted' => 1));
			} else {
				$version->delete();
			}
		}
	}
	
	/**
	 * Validates the 'release' action.
	 */
	public function validateRelease() {
		WCF::getSession()->checkPermissions(array('admin.system.projects.canManageProjects'));
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::delete()
	 */
	public function release() {
		if(empty($this->objects)) {
			$this->readObjects();
		}
		
		foreach($this->objects as $object) {
			$object->update(array('released' => 1));
		}
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::resetCache()
	 */
	protected function resetCache() {
		parent::resetCache();
		
		$packageIDs = array();
		
		// Package IDs of affected objects
		foreach($this->getObjects() as $object) {
			$packageIDs[$object->packageID] = true;
		}
		
		// Package ID of parameters
		if(isset($this->parameters['data']['packageID'])) {
			$packageIDs[$this->parameters['data']['packageID']] = true;
		}
		
		// Clear project cache
		foreach(array_keys($packageIDs) as $packageID) {
			ProjectVersionCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
	}
}
