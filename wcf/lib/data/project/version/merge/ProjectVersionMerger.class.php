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
namespace wcf\data\project\version\merge;

use wcf\data\project\Project;
use wcf\system\exception\SystemException;
use wcf\util\ProjectUtil;
use wcf\data\project\version\ProjectVersionFileArchive;
use wcf\data\project\database\ProjectDatabaseCache;

/**
 * Calculates the conflicts between two project versions and merges them.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionMerger {
	/**
	 * @var \wcf\data\project\version\ProjectVersion
	 */
	protected $sourceVersion;
	
	/**
	 * @var \wcf\data\project\version\ProjectVersion
	 */
	protected $targetVersion;
	
	/**
	 * @var array<mixed>
	 */
	protected $sourceData;
	
	/**
	 * @var array<mixed>
	 */
	protected $targetData;
	
	/**
	 * @var array<\wcf\data\project\version\merge\ProjectVersionDataConflict>
	 */
	protected $dataConflicts = array();
	
	/**
	 * @var array<mixed>
	 */
	protected $sourceDatabaseModifications;
	
	/**
	 * @var array<mixed>
	 */
	protected $targetDatabaseModifications;
	
	/**
	 * @var array<\wcf\data\project\version\merge\ProjectVersionDatabaseConflict>
	 */
	protected $databaseConflicts = array();
	
	/**
	 * @var array<mixed>
	 */
	protected $sourceFiles;
	
	/**
	 * @var array<mixed>
	 */
	protected $targetFiles;
	
	/**
	 * @var array<\wcf\data\project\version\merge\ProjectVersionFileConflict>
	 */
	protected $fileConflicts = array();
	
	/**
	 * Creates a new ProjectVersionMerger instance for the two given versions.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion $sourceVersion
	 * @param \wcf\data\project\version\ProjectVersion $targetVersion
	 */
	public function __construct(\wcf\data\project\version\ProjectVersion $sourceVersion, \wcf\data\project\version\ProjectVersion $targetVersion) {
		// Check packageID
		if($sourceVersion->packageID != $targetVersion->packageID) {
			throw new SystemException('Cannot merge versions of two different projects.');
		}
		
		$this->sourceVersion = $sourceVersion;
		$this->targetVersion = $targetVersion;
	}
	
	/**
	 * Loads the conflicts between the versions.
	 */
	public function loadConflicts() {
		$this->loadDatabaseConflicts();
		
		$this->loadFileConflicts();
		
		exit;
	}
	
	/**
	 * Loads the conflicts between the database modifications of the versions.
	 */
	protected function loadDatabaseConflicts() {
		
	}
	
	/**
	 * Loads the conflicts between the files of the versions.
	 */
	protected function loadFileConflicts() {
		$this->sourceFiles = $this->getFiles($this->sourceVersion);
		$this->targetFiles = $this->getFiles($this->targetVersion);
		
		foreach($this->sourceFiles as $sourceFilename => $sourceFileContent) {
			if(!isset($this->targetFiles[$sourceFilename])) {
				$this->fileConflicts[] = new ProjectVersionFileConflict($sourceFilename, $sourceFileContent);
			} elseif($sourceFileContent != $this->targetFiles[$sourceFilename]) {
				$this->fileConflicts[] = new ProjectVersionFileConflict($sourceFilename, $sourceFileContent, $this->targetFiles[$sourceFilename]);
			}
		}
	}
	
	/**
	 * Merges the two versions.
	 */
	public function executeMerge() {
		
	}
	
	/**
	 * Returns an array with all files of the version.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion $version
	 */
	protected function getFiles(\wcf\data\project\version\ProjectVersion $version) {
		$files = array();
		
		// Version is active
		// -> load files from project directory
		if($version->isCurrentVersion()) {
			foreach($version->getProject()->getFiles() as $fileLog) {
				$files[$fileLog->getProjectPath()] = $fileLog->getProjectFileContents();
			}
		}
		// Version is not active
		// -> load files from archive
		else {
			$archive = new ProjectVersionFileArchive($version);
			
			$files = $archive->getFiles();
		}
		
		return $files;
	}
	
	/**
	 * Returns an array with all database modifications of the version.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion $version
	 */
	protected function getDatabaseModifications(\wcf\data\project\version\ProjectVersion $version) {
		$modifications = array();
		
		// Version is active
		// -> load modifications from active database
		if($version->isCurrentVersion()) {
			// ProjectDatabaseCache::getInstance()->
		}
		// Version is not active
		// -> load modifications from sql log
		else {
			
		}
		
		return $modifications;
	}
}
