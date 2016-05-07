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
namespace wcf\acp\page;

use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\data\project\Project;
use wcf\system\exception\IllegalLinkException;
use wcf\data\package\PackageCache;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\data\project\version\ProjectVersion;
use wcf\data\project\version\merge\ProjectVersionMerger;
use wcf\data\project\ProjectEditor;

/**
 * Displays the versions of a project.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionListPage extends AbstractPage {
	/**
	 * @see wcf\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.package';

	/**
	 * @see \wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.system.projects.canManageProjects');
	
	/**
	 * @var \wcf\data\project\Project
	 */
	public $project = null;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
// 		$sourceVersion = new ProjectVersion(70);
// 		$targetVersion = new ProjectVersion(76);
		
// 		$merger = new ProjectVersionMerger($sourceVersion, $targetVersion);
// 		$merger->loadConflicts();
		
// 		exit;
		
		// Get project
		if(isset($_GET['id'])) {
			$this->project = Project::getProject($_GET['id']);
		}
		
		// Validate ID
		if($this->project === null) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'project' => $this->project
		));
	}
}
