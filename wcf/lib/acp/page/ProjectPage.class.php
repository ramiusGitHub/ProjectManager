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

use wcf\system\WCF;
use wcf\page\AbstractPage;
use wcf\data\project\Project;
use wcf\data\project\ProjectList;
use wcf\system\cache\builder\LanguageCacheBuilder;
use wcf\data\project\ProjectEditor;

/**
 * Displays an overview over all data, database modifications
 * and files which belong to a project.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectPage extends AbstractPage {
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
		
		// Get project
		if(isset($_GET['id'])) {
			$this->project = Project::getProject($_GET['id']);
		}
		
		if($this->project === null) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// Sort language categories from categoryID to category
		$languageCategories = array();
		$categories = LanguageCacheBuilder::getInstance()->getData(array(), 'categories');
		foreach(LanguageCacheBuilder::getInstance()->getData(array(), 'categoryIDs') as $categoryID => $categoryName) {
			$languageCategories[$categoryID] = $categories[$categoryName];
		}
		
		WCF::getTPL()->assign(array(
			'packageID' => $this->project->packageID,
			'project' => $this->project,
			'projects' => ProjectList::getProjectsFromCache(),
			'languageCategories' => $languageCategories,
			'test' => array('a' => 1, 'b' => 2)
		));
	}
}
