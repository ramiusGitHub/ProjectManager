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
namespace wcf\system\event\listener;

use wcf\data\package\PackageCache;
use wcf\data\project\file\log\FileLogList;
use wcf\data\application\Application;
use wcf\data\project\file\log\ProjectFileLogAction;
use wcf\system\WCF;
use wcf\data\project\file\log\FileLogEditor;
use wcf\util\FileUtil;
use wcf\data\project\ProjectEditor;
use wcf\data\template\TemplateList;
use wcf\data\acp\template\ACPTemplateList;
use wcf\data\template\TemplateEditor;
use wcf\data\acp\template\ACPTemplateEditor;
use wcf\data\project\file\log\ProjectFileLogCache;
use wcf\data\project\acp\template\ProjectACPTemplateLogCache;
use wcf\data\project\template\ProjectTemplateLogCache;
use wcf\system\Regex;
use wcf\system\exception\DuplicateFilenameException;
use wcf\data\project\file\log\FileLog;
use wcf\data\acp\template\ACPTemplate;
use wcf\system\exception\FileLockedException;
use wcf\data\package\PackageAction;
use wcf\system\application\ApplicationHandler;
use wcf\system\database\DatabaseException;
use wcf\system\exception\SystemException;
use wcf\util\StringUtil;
use wcf\system\cache\builder\ProjectFileCacheBuilder;
use wcf\system\cache\builder\ProjectACPTemplateCacheBuilder;
use wcf\system\cache\builder\ProjectTemplateCacheBuilder;
use wcf\data\project\Project;
use wcf\system\cache\builder\ProjectACPTemplateLogCacheBuilder;
use wcf\system\cache\builder\ProjectTemplateLogCacheBuilder;
use wcf\system\cache\builder\FileLogCacheBuilder;
use wcf\data\template\TemplateAction;
use wcf\data\acp\template\ACPTemplateAction;
use wcf\util\ProjectUtil;
use wcf\system\cache\builder\ProjectFileLogCacheBuilder;

/**
 * Synchronizes the files between the project directories and the WCF and application directories.
 * Updates the file_log table accordingly.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectFileSynchronizationListener implements IParameterizedEventListener {
	/**
	 * @var array<\wcf\data\project\Project>
	 */
	public $projects = array();
	
	/**
	 * @var array<\wcf\system\Regex>
	 */
	public $regexes = array();
	
	/**
	 * @var array<string>
	 */
	public $abbreviations = array();
	
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// Get projects with external directories
		$packages = PackageCache::getInstance()->getPackages();
		foreach($packages['packages'] as $packageID => $package) {
			if(!empty($package->projectDirectory)) $this->projects[$packageID] = Project::getProject($packageID);
		}
		if(empty($this->projects)) {
			return;
		}
		
		// Prepare regular expressions
		if(PROJECT_MANAGER_IGNORED_FILES != '') {
			$lines = explode("\n", PROJECT_MANAGER_IGNORED_FILES);
			foreach($lines as $line) {
				$regex = Regex::compile($line);
				if($regex->isValid()) {
					$this->regexes[] = $regex;
				}
			}
		}
		
		// Get application abbreviations
		$this->abbreviations = array('wcf');
		foreach(ApplicationHandler::getInstance()->getApplications() as $packageID => $application) {
			$this->abbreviations[] = ApplicationHandler::getInstance()->getAbbreviation($packageID);
		}
		
		// Begin transaction
		WCF::getDB()->beginTransaction();
		
		// Synchronize files
		$resetFileLogCache = $this->updateAndDeleteFiles();
		$resetFileLogCache = $resetFileLogCache || $this->copyNewFiles();
		
		// Synchronize templates
		$resetTemplateLogCache = $this->updateAndDeleteTemplates(false);
		$resetTemplateLogCache = $resetTemplateLogCache || $this->copyNewTemplates(false);
		
		// Synchronize ACP templates
		$resetACPTemplateLogCache = $this->updateAndDeleteTemplates(true);
		$resetACPTemplateLogCache = $resetACPTemplateLogCache || $this->copyNewTemplates(true);
		
		// Commit transaction
		WCF::getDB()->commitTransaction();
		
		// Reset file log cache
		if($resetFileLogCache) {
			ProjectFileLogCacheBuilder::getInstance()->reset();
		}
		
		// Reset template log cache
		if($resetTemplateLogCache) {
			ProjectTemplateLogCacheBuilder::getInstance()->reset();
		}
		
		// Reset ACP template log cache
		if($resetACPTemplateLogCache) {
			ProjectACPTemplateLogCacheBuilder::getInstance()->reset();
		}
	}
	
	/**
	 * Deletes files from the wcf and file_log which were deleted from their project directory.
	 * Updates files which were changed in the project directory by copying them into the wcf.
	 * 
	 * @return boolean true if at least one file was deleted
	 */
	public function updateAndDeleteFiles() {
		$delete = array();
		$clearCache = array();
		
		// Update and delete files
		foreach($this->projects as $packageID => $project) {
			$fileLogs = ProjectFileLogCache::getInstance()->getPackageFileLogs($packageID);
			
			foreach($fileLogs as $fileLog) {
				try {
					// Project file deleted
					// Remove application file and delete database entry
					if(!$fileLog->projectFileExists()) {
						// Delete previously created copy
						$fileLog->deleteApplicationFile();
						
						// Delete log entry
						$delete[] = $fileLog;
						
						// Remember to clear the project's cache
						$clearCache[$fileLog->packageID] = true;
					}
					// Project file exists
					else {
						// Check for exclusion
						foreach($this->regexes as $regex) {
							if($regex->match($fileLog->getProjectPath())) {
								// Delete log entry
								$delete[] = $fileLog;
								
								// This deletion is necessary if the matching
								// regular expression was added after the
								// file was copied to the application path
								$fileLog->deleteApplicationFile();
								
								// Remember to clear cache
								$clearCache[$fileLog->packageID] = true;
								
								// Skip to next log entry
								continue(2);
							}
						}
						
						// Exclude lib directory if autoloader is used...
						if(PROJECT_MANAGER_USE_AUTOLOADER && StringUtil::startsWith($fileLog->filename, 'lib/')
							// ... but do not exclude the autoloader itself!
							&& !$this->isAutoloaderFile($fileLog->application, $fileLog->filename)) {
							
							// This removal is necessary if the USE_AUTOLOADER option
							// was set to false before and is changed to true now
							// -> delete previously copied files which are not copied anymore
							$fileLog->deleteApplicationFile();
						}
						// Check for update
						elseif($fileLog->applicationFileIsDeprecated()) {
							$fileLog->copyToApplicationPath();
						}
					}
				} catch(SystemException $e) {
					// TODO check when a FileLockedException should be thrown instead
					throw $e;
					
					// throw new FileLockedException(PackageCache::getInstance()->getPackage($fileLog->packageID), $source, $target);
				}
			}
		}
		
		// Delete file log entries
		if(!empty($delete)) {
			$action = new ProjectFileLogAction($delete, 'delete');
			$action->executeAction();
		}
		
		// Reset caches
		foreach($clearCache as $packageID => $tmp) {
			ProjectFileCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
		
		// Return whether a file was deleted
		return !empty($delete);
	}

	/**
	 * Copies new files from the project directories to the application
	 * directories and adds the corresponding file_log entries.
	 * 
	 * @return boolean true if at least one file was copied
	 */
	public function copyNewFiles() {
		$result = false;
		
		foreach($this->projects as $packageID => $project) {
			$oneFileWasCopied = false;
			
			// Get logged files
			$fileLogs = ProjectFileLogCache::getInstance()->getPackageFileLogs($packageID);
			$loggedFiles = array();
			foreach($fileLogs as $fileLog) {
				if(!isset($loggedFiles[$fileLog->application])) $loggedFiles[$fileLog->application] = array();
			
				$loggedFiles[$fileLog->application][] = $fileLog->filename;
			}
			
			// Ignored directories
			$ignoredDirectories = array('templates', 'acp/templates');
			
			// Iterate over application abbreviations in the project directory
			foreach($this->abbreviations as $abbreviation) {
				$directory = $project->projectDirectory . $abbreviation . '/';
				
				// Get files from project directory
				$files = ProjectUtil::getFileList(
					$directory,
					$ignoredDirectories,
					$this->regexes
				);
				
				// Remove directory prefix
				foreach($files as &$file) {
					$file = str_replace($directory, '', $file);
				}
				
				// Get unlogged files by difference
				if(isset($loggedFiles[$abbreviation])) {
					$unloggedFiles = array_diff($files, $loggedFiles[$abbreviation]);
				} else {
					$unloggedFiles = $files;
				}
				if(empty($unloggedFiles)) continue;
				
				// Iterate over unlogged files
				foreach($unloggedFiles as $unloggedFile) {
					try {
						// Create database entry
						$fileLog = FileLogEditor::create(array(
							'packageID' => $packageID,
							'filename' => $unloggedFile,
							'application' => $abbreviation
						));
						
						// Copy file...
						// ... but exclude lib directory if autoloader is used
						// ... and do not exclude the autoloader itself!
						if(!PROJECT_MANAGER_USE_AUTOLOADER || !StringUtil::startsWith($unloggedFile, 'lib/') || $this->isAutoloaderFile($abbreviation, $unloggedFile)) {
							$fileLog->copyToApplicationPath();
						}
							
						// Remember to reset cache
						$oneFileWasCopied = true;
					} catch(DatabaseException $e) {
						// TODO check when a DuplicateFilenameException should be thrown instead
						throw $e;
						
// 						$entry = FileLog::load($abbreviation, $file);
							
// 						if($packageID != $entry->packageID) {
// 							$package = PackageCache::getInstance()->getPackage($entry->packageID);
// 							throw new DuplicateFilenameException($project->getDecoratedObject(), $package, $source, $target);
// 						}
					}
				}
			}

			// Reset cache			
			if($oneFileWasCopied) {
				$result = true;
				
				ProjectFileCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
			}
		}
		
		// Returns whether at least one file was copied
		return $result;
	}
	
	/**
	 * Deletes templates from the wcf and template table which were deleted from their project directory.
	 * Updates templates which were changed in the project directory by copying them into the wcf.
	 * If $acp is true the acp templates will be updated and deleted.
	 * 
	 * @return boolean true if at least one template was deleted
	 */
	public function updateAndDeleteTemplates($acp) {
		$cache = ($acp ? ProjectACPTemplateLogCache::getInstance() : ProjectTemplateLogCache::getInstance());
		$dir = ($acp ? 'acp/' : '') . 'templates/';
		$delete = array();
		$clearCache = array();
		
		// Get all template logs of the projects to update and delete them
		foreach($this->projects as $packageID => $project) {
			$templates = $cache->getPackageTemplateLogs($packageID);
		
			foreach($templates as $template) {
				$source = $project->projectDirectory . $template->application . '/' . $dir . $template->templateName . '.tpl';
				$target = Application::getDirectory($template->application) . $dir . $template->templateName . '.tpl';
				
				try {
					// Source deleted, remove target file and delete database entry
					if(!$this->fileExists($source)) {
						// Delete previously created copy
						if(file_exists($target)) {
							unlink($target);
						}
						
						// Delete log entry
						$delete[] = $template;
						
						// Remember to clear cache
						$clearCache[$template->packageID] = true;
					} else {
						// Source exists, check for exclusion
						foreach($this->regexes as $regex) {
							if($regex->match($source)) {
								// Delete log entry
								$delete[] = $template;
								
								// This deletion is necessary if the matching
								// regular expression was added after the
								// file was copied to the application path
								if(file_exists($target)) {
									unlink($target);
								}
						
								// Remember to clear cache
								$clearCache[$template->packageID] = true;
								
								continue(2);
							}
						}
						
						// Source exists, check for update
						if(@filemtime($source) > @filemtime($target)) {
							copy($source, $target);
						}
					}
				} catch(SystemException $e) {
					// TODO check when a FileLockedException should be thrown instead
					throw $e;
					
					// throw new FileLockedException(PackageCache::getInstance()->getPackage($template->packageID), $source, $target);
				}
			}
		}
		
		// Delete template log entries
		if(!empty($delete)) {
			if($acp) $action = new ACPTemplateAction($delete, 'delete');
			else $action = new TemplateAction($delete, 'delete');
			$action->executeAction();
		}
		
		// Reset cache
		if($acp) $cacheBuilder = ProjectACPTemplateCacheBuilder::getInstance();
		else $cacheBuilder = ProjectTemplateCacheBuilder::getInstance();
		
		foreach($clearCache as $packageID => $tmp) {
			$cacheBuilder->reset(array('packageID' => $packageID));
		}
		
		// Return whether at least one template was deleted
		return !empty($delete);
	}
	
	/**
	 * Copies new templates from the project directories to the application
	 * directories and adds the corresponding template table entries.
	 * If $acp is true the acp templates will be copied.
	 * 
	 * @return boolean true if at least one template was copied
	 */
	public function copyNewTemplates($acp) {
		$result = false;
		
		foreach($this->projects as $packageID => $project) {
			$oneFileWasCopied = false;
			
			foreach($this->abbreviations as $abbreviation) {
				$sourceDirectory = $project->projectDirectory . $abbreviation . ($acp ? '/acp' : '') . '/templates/';
								
				// Get files
				$templates = ProjectUtil::getFileList(
					$sourceDirectory,
					array('compiled'),
					$this->regexes	
				);
				
				// Remove dir and .tpl file extension
				foreach($templates as &$template) {
					$template = str_replace($sourceDirectory, '', $template);
					if(mb_substr($template, -4) == '.tpl') $template = mb_substr($template, 0, mb_strlen($template) - 4);
				}

				// Get logged files
				if($acp) {
					$loggedTemplates = ProjectACPTemplateLogCache::getInstance()->getApplicationTemplateLogs($abbreviation);
				} else {
					$loggedTemplates = ProjectTemplateLogCache::getInstance()->getApplicationTemplateLogs($abbreviation);
				}
				foreach($loggedTemplates as &$template) $template = $template->templateName;
				
				// Get files by difference
				$templates = array_diff($templates, $loggedTemplates);
				if(empty($templates)) continue;
				
				$appDir = Application::getDirectory($abbreviation);
				$templateDir = ($acp ? 'acp/' : '') . 'templates/';
				
				FileUtil::makePath(dirname($appDir . $templateDir));
				foreach($templates as $template) {
					$source = $project->projectDirectory . $abbreviation . '/' . $templateDir . $template . '.tpl';
					$target = $appDir . $templateDir . $template . '.tpl';
					
					try {
						// Create database entry
						$parameters = array(
							'packageID' => $packageID,
							'templateName' => $template,
							'application' => $abbreviation
						);
						if($acp) {
							ACPTemplateEditor::create($parameters);
						} else {
							$parameters['lastModificationTime'] = @filemtime($source);
							TemplateEditor::create($parameters);
						}
							
						// Copy file
						copy($source, $target);
						
						// Remember to clear cache
						$oneFileWasCopied = true;
					} catch(DatabaseException $e) {
						$sql = "SELECT	packageID
							FROM	wcf".WCF_N.($acp ? "_acp" : "")."_template
							WHERE	templateName = ?
							AND	application = ?";
						$statement = WCF::getDB()->prepareStatement($sql);
						$statement->execute(array($template, $abbreviation));
						$row = $statement->fetchArray();
						
						if($packageID != $row['packageID']) {
							$package = PackageCache::getInstance()->getPackage($package->packageID);
							throw new DuplicateFilenameException($project->getDecoratedObject(), $package, $source, $target);
						}
					}
				}
			}
			
			// Reset cache			
			if($oneFileWasCopied) {
				$result = true;
				
				if($acp) ProjectACPTemplateCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
				else ProjectTemplateCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
			}
		}
		
		return $result;
	}
	
	/**
	 * Returns whether the given file is the project manager's autoloader.
	 * 
	 * @param string $application
	 * @param string $filename
	 * @return boolean
	 */
	protected function isAutoloaderFile($application, $filename) {
		return ($application == 'wcf' && $filename == 'lib/system/event/listener/ProjectAutoloaderListener.class.php');
	}
	
	/**
	 * Returns whether the given file exists (case sensitive).
	 * 
	 * @param string $filename
	 * @return boolean
	 */
	protected function fileExists($filename) {
		if(file_exists($filename)) {
			// file_exists might return true on a case insensitive OS (like Windows)
			// even when the real filename and the given $filename do not match case.
			// Therefore we use the SplFileInfo to retrieve the filename from the
			// OS and compare it with the given $filename. 
			$spl = new \SplFileInfo($filename);
			
			return ($spl->getPathname() == $filename);
		}
		
		return false;
	}
}
