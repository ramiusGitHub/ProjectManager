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
namespace wcf\system\cache\builder;

use wcf\data\project\version\ProjectVersionList;

/**
 * Cache builder for the versions of a project. The data has the following indices:
 * versions: An array with the version IDs as keys and the associated version as value.
 * logIDs: An array with the log IDs as keys and the version ID as value.
 * current: The currently active version of the project.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionCacheBuilder extends AbstractProjectCacheBuilder {
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectCacheBuilder::getProjectData()
	 */
	protected function getProjectData() {
		// Get versions
		$versionList = new ProjectVersionList();
		$versionList->getConditionBuilder()->add("packageID = ?", array($this->project->packageID));
		$versionList->sqlOrderBy = "versionID ASC";
		$versionList->readObjects();
		
		// Find current version and build versions and logIDs arrays
		$versions = array();
		$logIDs = array();
		
		foreach($versionList->getObjects() as $version) {
			$versions[$version->getVersionID()] = $version;
			$logIDs[$version->getObjectID()] = $version->getVersionID();
			
			if($version->getVersionString() == $this->project->packageVersion) {
				$current = $version;
			}
		}
		
		// Return data
		return array(
			'versions' => $versions,
			'logIDs' => $logIDs,
			'current' => $current
		);
	}
	
	/**
	 * @see \wcf\system\cache\builder\AbstractProjectCacheBuilder::count()
	 */
	protected function count(array &$data) {
		return count($data['versions']);
	}
}
