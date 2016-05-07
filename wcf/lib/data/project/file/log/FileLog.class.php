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
namespace wcf\data\project\file\log;

use wcf\system\WCF;
use wcf\data\application\Application;
use wcf\data\DatabaseObject;
use wcf\data\project\Project;
use wcf\util\FileUtil;

/**
 * Represents a file log.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class FileLog extends DatabaseObject {
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'package_installation_file_log';
	
	/**
	 * @see \wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'fileLogID';
	
	/**
	 * Returns the file log entry with the given application and filename.
	 * 
	 * @param string $application
	 * @param string $filename
	 * @return \wcf\data\project\file\log\FileLog
	 */
	public static function load($application, $filename) {
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_package_installation_file_log
			WHERE	application = ?
			AND	filename = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($application, $filename));
		
		$row = $statement->fetchSingleRow();
		if($row === false) {
			return null;
		}
		
		return new static(null, $row);
	}
	
	/**
	 * Returns the full path to the external file in the project directory.
	 * 
	 * @return string
	 */
	public function getProjectPath() {
		return Project::getProject($this->packageID)->getDirectory() . $this->application . '/' . $this->filename;
	}
	
	/**
	 * Returns the full path to the file in the application directory.
	 * 
	 * @return string
	 */
	public function getApplicationPath() {
		return Application::getDirectory($this->application) . $this->filename;
	}
	
	/**
	 * Returns whether the logged file exists in the project directory.
	 * 
	 * @return boolean
	 */
	public function projectFileExists() {
		return $this->exists($this->getProjectPath());
	}
	
	/**
	 * Returns whether the logged file exists in the project directory.
	 * 
	 * @return boolean
	 */
	public function applicationFileExists() {
		return $this->exists($this->getApplicationPath());
	}
	
	/**
	 * Returns whether the file exists.
	 * 
	 * @param string $path
	 * @return boolean
	 */
	protected function exists($path) {
		if(file_exists($path)) {
			// file_exists might return true on a case insensitive OS (like Windows)
			// even when the real filename and the given $filename do not match case.
			// Therefore we use the SplFileInfo to retrieve the filename from the
			// OS and compare it with the given $filename.
			$spl = new \SplFileInfo($path);
			
			// Convert backslashes (e.g. on Windows) to forward slashes
			$realPath = FileUtil::unifyDirSeparator($spl->getRealPath());
			
			// Check real path against given path
			return ($realPath == $path);
		}
		
		return false;
	}
	
	/**
	 * Deletes the logged file in the project directory.
	 */
	public function deleteProjectFile() {
		$this->delete($this->getProjectPath());
	}
	
	/**
	 * Deletes the logged file in the application directory.
	 */
	public function deleteApplicationFile() {
		$this->delete($this->getApplicationPath());
	}
	
	/**
	 * Delete the file.
	 * 
	 * @param string $path
	 */
	protected function delete($path) {
		if($this->exists($path)) {
			unlink($path);
		}
	}
	
	/**
	 * Returns the contents of the logged file in the project directory.
	 * 
	 * @return string
	 */
	public function getProjectFileContents() {
		return $this->getContents($this->getProjectPath());
	}
	
	/**
	 * Returns the contents of the logged file in the application directory.
	 * 
	 * @return string
	 */
	public function getApplicationFileContents() {
		return $this->getContents($this->getApplicationPath());
	}
	
	/**
	 * Returns the contents of the file.
	 * 
	 * @param string $path
	 * @return string
	 */
	protected function getContents($path) {
		return file_get_contents($path);
	}
	
	/**
	 * Overrides the contents of the logged file in the project directory.
	 * 
	 * @param string $contents
	 */
	public function setProjectFileContents($contents) {
		return $this->setContents($this->getProjectPath(), $contents);
	}
	
	/**
	 * Overrides the contents of the logged file in the application directory.
	 * 
	 * @param string $contents
	 */
	public function setApplicationFileContents($contents) {
		return $this->setContents($this->getApplicationPath(), $contents);
	}
	
	/**
	 * Overrides the contents of the file.
	 *
	 * @param string $path
	 * @param string $contents
	 */
	protected function setContents($path, $contents) {
		file_put_contents($path, $contents);
	}
	
	/**
	 * Returns the modification timestamp of the logged file in the project directory.
	 * 
	 * @return int
	 */
	public function getProjectFileModificationTime() {
		return $this->getModificationTime($this->getProjectPath());
	}
	
	/**
	 * Returns the modification timestamp of the logged file in the application directory.
	 * 
	 * @return int
	 */
	public function getApplicationFileModificationTime() {
		return $this->getModificationTime($this->getApplicationPath());
	}
	
	/**
	 * Returns the modification timestamp of the file.
	 * 
	 * @param string $path
	 * @return int
	 */
	protected function getModificationTime($path) {
		if(!$this->exists($path)) {
			return 0;
		}
		
		return filemtime($path);
	}
	
	/**
	 * Sets the modification time of the logged file in the
	 * project directory to the given timestamp.
	 * 
	 * @param int $timestamp
	 */
	public function setProjectFileModifyTime($timestamp = TIME_NOW) {
		$this->setModifiyTime($this->getProjectPath(), $timestamp);
	}
	
	/**
	 * Sets the modification time of the logged file in the
	 * application directory to the given timestamp.
	 * 
	 * @param int $timestamp
	 */
	public function setApplicationFileModifyTime($timestamp = TIME_NOW) {
		$this->setModifiyTime($this->getApplicationPath(), $timestamp);
	}
	
	/**
	 * Sets the modification time of the file to the given timestamp.
	 * 
	 * @param string $path
	 * @param int $timestamp
	 */
	protected function setModifiyTime($path, $timestamp) {
		touch($path, $timestamp);
	}
	
	/**
	 * Copies the file located in the project directory to the application directory.
	 */
	public function copyToApplicationPath() {
		/// Create directory in application
		FileUtil::makePath(dirname($this->getApplicationPath()));
		
		// Copy file to from project to application
		copy($this->getProjectPath(), $this->getApplicationPath());
	}
	
	/**
	 * Returns whether the modification time of the application file
	 * is less than the modification time of the project file.
	 * 
	 * @return boolean
	 */
	public function applicationFileIsDeprecated() {
		return $this->getProjectFileModificationTime() > $this->getApplicationFileModificationTime();
	}
}