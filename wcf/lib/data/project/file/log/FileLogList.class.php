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

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of file logs.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class FileLogList extends DatabaseObjectList {
	/**
	 * @see \wcf\data\DatabaseObjectList::$className
	 */
	public $className = '\wcf\data\project\file\log\FileLog';
	
	/**
	 * Reads the objects from database.
	 * 
	 * @param boolean $orderByFilename
	 */
	public function readObjects($orderByFilename = true) {
		if($orderByFilename) {
			$this->sqlOrderBy = "SUBSTRING_INDEX(filename, '.', -1) ASC, SUBSTRING_INDEX(filename, '.', -2) ASC, filename ASC";
		}
		
		parent::readObjects();
	}
	/**
	 * Returns all project files of the project with the given package ID.
	 * If a regular expression is passed, only files which have matching filenames are returned.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\project\file\log\FileLog>
	 */
	public static function getFilesByPackageID($packageID, \wcf\system\Regex $regex = null) {
		// Get package's file logs
		$fileLogs = ProjectFileLogCache::getInstance()->getPackageFileLogs($packageID);
		
		// Check regex
		if(isset($regex)) {
			foreach($fileLogs as $index => $fileLog) {
				if(!$regex->match($fileLog->filename)) unset($fileLogs[$index]);
			}
		}
		
		return $fileLogs;
	}
	
	/**
	 * Returns all project files of the projects with the given package IDs.
	 * If a regular expression is passed, only files which have matching filenames are returned.
	 * 
	 * @param array $packageIDs
	 * @return array<\wcf\data\project\file\log\FileLog>
	 */
	public static function getFilesByPackageIDs(array $packageIDs, \wcf\system\Regex $regex = null) {
		$files = array();
		foreach($packageIDs as $packageID) {
			$files = array_merge($files, static::getFilesByPackageID($packageID, $regex));
		}
		
		return $files;
	}
}
