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

use wcf\data\package\PackageList;
use wcf\data\package\PackageCache;
use wcf\system\WCF;

/**
 * Represents a list of projects.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectList extends PackageList {
	/**
	 * Creates a new ProjectList object.
	 */
	public function __construct() {
		parent::__construct();
		
		$this->conditionBuilder->add("isActiveProject = ?", array(1));
	}
	
	/**
	 * @see \wcf\data\DatabaseObjectList::getObjects()
	 */
	public function getObjects() {
		$objects = parent::getObjects();
		
		// Decorate packages in projects
		foreach($objects as &$object) {
			if(!($object instanceof Project)) {
				$object = new Project($object);
			}
		}
		
		// Sort projects
		static::sortProjects($objects);
		
		return $objects;
	}
	
	/**
	 * Returns all projects from cache.
	 * 
	 * @return array<\wcf\data\project\Project>
	 */
	public static function getProjectsFromCache() {
		$packages = PackageCache::getInstance()->getPackages();
		$projects = array();
		
		foreach($packages['packages'] as $package) {
			$project = new Project($package);
			
			if(!empty($project->getDirectory())) {
				$projects[$package->packageID] = $project;
			}
		}
		
		static::sortProjects($projects);
		
		return $projects;
	}
	
	/**
	 * Sorts the referenced array of projects.
	 * 
	 * @param array $projects
	 */
	public static function sortProjects(array &$projects) {
		uasort($projects, function($a, $b) {
			$aName = WCF::getLanguage()->get($a->packageName);
			$bName = WCF::getLanguage()->get($b->packageName);
	
			return strcasecmp($aName, $bName);
		});
	}
}
