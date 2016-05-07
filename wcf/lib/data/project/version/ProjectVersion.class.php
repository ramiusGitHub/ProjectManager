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

use wcf\data\DatabaseObject;
use wcf\data\package\PackageCache;
use wcf\data\project\Project;

/**
 * Represents a project version.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersion extends DatabaseObject {
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'project_version_log';
	
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'logID';
	
	/**
	 * Returns the project this version belongs to.
	 * 
	 * @return \wcf\data\project\Project
	 */
	public function getProject() {
		return Project::getProject($this->packageID);
	}
	
	/**
	 * Returns the version's ID.
	 * 
	 * @return int
	 * 
	 */
	public function getVersionID() {
		return $this->versionID;
	}
	
	/**
	 * Returns the version's string representation.
	 * 
	 * @return string
	 */
	public function getVersionString() {
		return $this->packageVersion;
	}
	
	/**
	 * Returns whether this version was exported.
	 * 
	 * @return boolean
	 */
	public function isExported() {
		return $this->exported;
	}
	
	/**
	 * Returns the relative path to the exported archive.
	 * 
	 * @return string
	 */
	public function getExported() {
		return $this->exported;
	}
	
	/**
	 * Returns whether this version was released.
	 * 
	 * @return boolean
	 */
	public function isReleased() {
		return $this->released;
	}
	
	/**
	 * Returns whether this version is the currently active version of the project.
	 * 
	 * @return boolean
	 */
	public function isCurrentVersion() {
		return $this->getProject()->getCurrentVersion()->getVersionString() == $this->getVersionString();
	}
	
	/**
	 * Returns whether this version be be edited.
	 * 
	 * @return boolean
	 */
	public function isEditable() {
		return !$this->isReleased();
	}
	
	/**
	 * Returns whether this version can be deleted.
	 * 
	 * @return boolean
	 */
	public function isDeletable() {
		return !$this->isCurrentVersion();
	}
	
	/**
	 * Returns whether switching to this version is possible.
	 * 
	 * @return boolean
	 */
	public function isSwitchable() {
		return !$this->isCurrentVersion() && $this->getProject()->isActive();
	}
	
	/**
	 * Returns whether this version can be downloaded.
	 * 
	 * @return boolean
	 */
	public function isDownloadable() {
		return $this->isExported();
	}
	
	/**
	 * Returns whether this version can be exported.
	 * 
	 * @return boolean
	 */
	public function isExportable() {
		return !$this->isReleased();
	}
}
