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
namespace wcf\util;

use wcf\data\package\PackageCache;
use wcf\system\WCF;
use wcf\system\Regex;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\project\Project;
use wcf\system\application\ApplicationHandler;
use wcf\system\event\EventHandler;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\data\project\ProjectDataTables;

/**
 * The ProjectUtil provides some methods statically which are used at various locations
 * and did not fit in any other class.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUtil {
	/**
	 * Generates a common archive name.
	 * Archive path: WCF_DIR/projects/versions/PACKAGE_ID/VERSION_ID.tar
	 *
	 * @param \wcf\data\project\version\ProjectVersion $version
	 * @return string
	 */
	public static function generateVersionArchiveName(\wcf\data\project\version\ProjectVersion $version) {
		return WCF_DIR . 'projects/versions/' . $version->packageID . '/' . $version->getObjectID() . '.tar';
	}

	/**
	 * Generates a common export name.
	 * Export path: WCF_DIR/projects/exports/PACKAGE_ID/VERSION_ID/
	 *
	 * @param \wcf\data\project\version\ProjectVersion $version
	 * @return string
	 */
	public static function generateVersionExportName(\wcf\data\project\version\ProjectVersion $version) {
		return WCF_DIR . 'projects/exports/' . $version->packageID . '/' . $version->getObjectID() . '/';
	}
	
	/**
	 * Deletes a folder and recursively its subfolders if the folder
	 * is empty or only contains empty subfolders.
	 * 
	 * @param string $path
	 * @return boolean whether the directory got deleted
	 */
	public static function deleteEmptyFolders($path) {
		// Get directory
		$directory = DirectoryUtil::getInstance($path);
		
		// Count files
		$files = $directory->getFileObjects(DirectoryUtil::SORT_NONE);
		$count = count($files);
		
		// Directory only contains '.' and '..', therefore it is empty
		if($count == 2) {
			// Delete directory
			unlink($path);
			
			// Return true to indicate it was deleted
			return true;
		}
		// Directory contains more files, iterate over them
		else {
			// Init result
			$empty = true;
			
			// Iterate over all files in the directory
			foreach($files as $file) {
				if($file->isDir()) {
					$empty = $empty && static::deleteEmptyFolders($file->getPathname());
				} elseif($file->getBasename() != '.' && $file->getBasename() != '..') {
					$empty = false;
				}
			}
			
			// If $empty is true, then all subdirectories are empty
			// and this directory itself contains no files, therefore
			// it will be deleted
			if($empty) {
				unlink($path);
			}
			
			// Return result
			return $empty;
		}
	}
	
	/**
	 * Loads all filenames (excluding '.' and '..') from a directory and it's subdirectories.
	 *
	 * @param string $root the root directory
	 * @param array<string>	$exDirs excluded directories relative to the root without leading and trailing slashes (e.g. if the root is the wcf directory, icon/flag)
	 * @param array<\wcf\system\Regex> $exRegexes the filename may no match with any of these regular expressions
	 * @param array<string> $exSuffixes the filename may not end in any of these suffixes
	 * @param array<string> $incSuffixes the filename has to end in at least one of these suffixes (\wcf\system\Regex is also possible)
	 * @param boolean $scanSubDirectories should the function call itself recursivly for sub directories
	 * @param string $dir is appended to the root directory
	 * @return array<string>
	 */
	public static function getFileList($root, array $exDirs = array(), array $exRegexes = array(), $exSuffixes = array(), array $incSuffixes = array(), $scanSubDirectories = true, $dir = '') {
		// Init
		$root = FileUtil::addTrailingSlash($root);
		$path = FileUtil::addTrailingSlash($root . $dir);
		if(!is_dir($path)) {
			return array();
		}
		
		// Open directory
		$handle = opendir($path);
	
		// Read file by file from the directory
		$files = array();
		while($filename = readdir($handle)) {
			if($filename == '.' || $filename == '..') continue;
			
			$file = $path . $filename;
			
			if(is_dir($file)) {
				$subDir = (!empty($dir) ? FileUtil::addTrailingSlash($dir) : '') . $filename;
				
				// Check excluded directories
				if(in_array($subDir, $exDirs)) continue;
				
				// If scanning of subdirectories is enabled, call getFileList recursivly
				if($scanSubDirectories) {
					$files = array_merge($files, static::getFileList($root, $exDirs, $exRegexes, $exSuffixes, $incSuffixes, $scanSubDirectories, $subDir));
				}
			} else {
				// Check excluded suffixes
				foreach($exSuffixes as $suffix) {
					if(StringUtil::endsWith($file, $suffix)) {
						continue(2);
					}
				}
					
				// Check included suffixes
				if(!empty($incSuffixes)) {
					$found = false;
					foreach($incSuffixes as $suffix) {
						if(StringUtil::endsWith($file, $suffix)) {
							$found = true;
							break;
						}
					}
					if(!$found) continue;
				}
				
				// Check regular expressions
				foreach($exRegexes as $regex) {
					if($regex->match($file)) {
						continue(2);
					}
				}
				
				// Add file
				$files[] = $file;
			}
			
		}
		
		// Return result
		return $files;
	}
	
	/**
	 * Copies a directory (and its subdirectories) to the target location.
	 * 
	 * @param string $source source directory path
	 * @param string $target target directory path
	 * @param boolean $deepCopy copy subdirectories recursively
	 */
	public static function copyDirectory($source, $target, $deepCopy = true) {
		// Symlinks
		if(is_link($source)) {
			return symlink(readlink($source), $target);
		}
	
		// File
		if(is_file($source)) {
			return copy($source, $target);
		}
	
		// Make destination directory
		if(!is_dir($target)) {
			FileUtil::makePath($target);
		}
		
		// Loop through folder (and subfolders)
		$dir = dir($source);
		while($entry = $dir->read()) {
			// Skip pointers
			if($entry == '.' || $entry == '..') {
				continue;
			}
			
			// Maybe skip subdirectories
			$newSource = $source . "/" . $entry;
			if(!$deepCopy && is_dir($newSource)) {
				continue;
			}
			
			// Copy entries
			$newTarget = $target . "/" . $entry;
			static::copyDirectory($newSource, $newTarget, true);
		}
		$dir->close();
	}
	
	/**
	 * Returns the class names of all classes within the relative namepsace.
	 * 
	 * @param string $relativeNamespace
	 * @param string $suffix
	 * @param boolean $abstract
	 * @param string $interface
	 * @param array<\wcf\data\application\Application> $applications
	 */
	public static function getClassListByNamespace($relativeNamespace, $suffix = '', $includeAbstract = false, $interface = '', array $applications = array()) {
		// TODO also get classes of external projects (maybe use file_log table entries)
		
		// init
		if(empty($relativeNamespace)) return array();
		elseif(!StringUtil::endsWith($relativeNamespace, '\\')) $relativeNamespace .= '\\';
		
		// default: get all applications (and the WCF)
		if(empty($applications)) {
			$applications = ApplicationHandler::getInstance()->getApplications();
			array_unshift($applications, ApplicationHandler::getInstance()->getWCF());
		}
		
		// get option directories of applications
		foreach($applications as $application) {
			$package = PackageCache::getInstance()->getPackage($application->packageID);
			$abbreviation = ApplicationHandler::getInstance()->getAbbreviation($application->packageID);
			
			$dirs[$abbreviation] = FileUtil::unifyDirSeparator(WCF_DIR.$package->packageDir.'lib/'.$relativeNamespace);
		}
		
		// scan directories for classes
		$classes = array();
		foreach($dirs as $abbreviation => $dir) {
			// check if directory exists
			if(!file_exists($dir)) continue;
			
			// get all files in the directory
			$files = static::getFileList(
				$dir,
				array(),
				array(),
				array(),
				array($suffix.'.class.php'),
				false
			);
			
			// iterate over files and fetch classnames
			foreach($files as $file) {
				$filename = basename($file);
				
				if($includeAbstract || !StringUtil::startsWith($filename, 'Abstract')) {
					// check the suffix
					if(!StringUtil::endsWith($filename, $suffix.'.class.php')) continue;
					
					// get the full className
					$shortClassName = StringUtil::replace('.class.php', '', $filename);
					$className = $abbreviation.'\\'.$relativeNamespace.$shortClassName;
					
					// check if the class exists
					if(!class_exists($className)) continue;
					
					// check if the class implements the interface
					if(!empty($interface) && !ClassUtil::isInstanceOf($className, $interface)) continue;
					
					// check for abstract classes
					if(!$includeAbstract) {
						$reflection = new \ReflectionClass($className);
						if($reflection->isAbstract()) continue;
					}
					
					// add array entry for application
					if(!isset($classes[$abbreviation])) $classes[$abbreviation] = array();
					
					// add class to result
					if(!empty($suffix)) {
						$resultClassName = substr($shortClassName, 0, mb_strlen($shortClassName) - mb_strlen($suffix));
						$classes[$abbreviation][$resultClassName] = $resultClassName;
					} else {
						$classes[$abbreviation][$shortClassName] = $shortClassName;
					}
				}
			}
		}
		
		return $classes;
	}
	
	/**
	 * Logs the deletion of the given DatabaseObject.
	 *
	 * @param \wcf\data\DatabaseObject $objects
	 * @param array<mixed> $additionalParameters
	 */
	public static function logDeletion(\wcf\data\DatabaseObject $object, array $additionalParameters = array()) {
		$tableName = $object->getDatabaseTableName();
		
		// Get table
		$table = ProjectDatabaseCache::getInstance()->getTable($tableName);
		$columnNames = array_keys($table->getColumns());
		
		// Get parameters
		// Init
		$parameters = array(
			'isDeleted' => 1
		);
		
		// Parameters from database object
		foreach($columnNames as $columnName) {
			if($object->$columnName !== null) {
				$value = $object->$columnName;
				
				if(is_array($value)) {
					$value = serialize($value);
				}
				
				$parameters[$columnName] = $value;
			}
		}
		
		// Additional parameters
		foreach($additionalParameters as $parameterName => $parameterValue) {
			$parameters[$parameterName] = $parameterValue;
		}
		
		// Version ID parameter
		if(isset($parameters['packageID'])) {
			$parameters['versionID'] = Project::getProject($parameters['packageID'])->getCurrentVersion()->getVersionID();
		}
		
		// Prepare statement
		$sql = "INSERT INTO	wcf" . WCF_N . "_" . ProjectDataTables::getInstance()->getLogTableName($tableName) . "
					(" . implode(', ', array_keys($parameters)) . ")
			VALUES		(:" . implode(', :', array_keys($parameters)) . ")";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		// Execute statement
		$statement->execute($parameters);
	}
	
	/**
	 * Deletes ALL compiled templates.
	 */
	public static function deleteCompiledTemplates() {
		// Templates
		DirectoryUtil::getInstance(WCF_DIR.'templates/compiled/')->removePattern(new Regex('.*_\d+_.*\.php$'));
		
		// ACP templates
		DirectoryUtil::getInstance(WCF_DIR.'acp/templates/compiled/')->removePattern(new Regex('.*_\d+_.*\.php$'));
	}
}
