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
namespace wcf\data\project;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleContainerAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\collapsible\content\UserCollapsibleContentHandler;
use wcf\system\WCF;
use wcf\system\exception\SystemException;
use wcf\data\package\Package;
use wcf\data\package\PackageCache;
use wcf\data\project\version\ProjectVersionAction;
use wcf\data\package\PackageAction;

/**
 * Executes project-related actions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectAction extends AbstractDatabaseObjectAction {
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = '\wcf\data\project\ProjectEditor';
	
	/**
	 * @var \wcf\data\project\Project
	 */
	protected $project;
	
	/**
	 * Initialize a new Project-related action.
	 *
	 * @param array<mixed> $objects
	 * @param string $action
	 * @param array $parameters
	 */
	public function __construct(array $objects, $action, array $parameters = array()) {
		foreach($objects as $index => $object) {
			if(is_int($object)) {
				$objects[$index] = new ProjectEditor(Project::getProject($object));
			} elseif($object instanceof Package) {
				$objects[$index] = new ProjectEditor(new Project($object));
			} elseif($object instanceof Project) {
				$objects[$index] = new ProjectEditor($object);
			}
		}
		
		parent::__construct($objects, $action, $parameters);
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// Create package
		$action = new PackageAction(
			array(),
			'create',
			$this->parameters
		); 
		$result = $action->executeAction();
		
		// Get package
		$newPackage = $result['returnValues'];
		
		// Create version log
		$versionAction = new ProjectVersionAction(
			array(),
			'create',
			array(
				'data' => array(
					'packageID' => $newPackage->getObjectID(),
					'versionID' => 0,
					'packageVersion' => $newPackage->packageVersion
				)
			)
		);
		$versionAction->executeAction();
		
		// Return 
		return $newPackage;
	}
	
	/**
	 * Validates the 'changeStatus' action.
	 */
	public function validateChangeStatus() {
		if(!WCF::getSession()->getPermission('admin.system.projects.canManageProjects')) {
			throw new PermissionDeniedException();
		}
		
		// validate
		$this->getSingleObject();
	}
	
	/**
	 * Changes a projects status.
	 */
	public function changeStatus() {
		$this->getSingleObject()->changeStatus();
		
		return $this->objectIDs;
	}
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::resetCache()
	 */
	protected function resetCache() {
		call_user_func(array("\wcf\data\package\PackageEditor", 'resetCache'));
	}
}
