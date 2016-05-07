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

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;
use wcf\data\application\Application;
use wcf\data\project\ProjectEditor;
use wcf\system\event\EventHandler;
use wcf\system\cache\builder\ApplicationCacheBuilder;
use wcf\util\StringUtil;
use wcf\data\package\PackageAction;
use wcf\data\package\PackageCache;
use wcf\data\project\file\log\FileLogList;
use wcf\data\project\file\log\FileLog;
use wcf\data\project\file\log\ProjectFileLogAction;
use wcf\data\project\ProjectDataAction;
use wcf\data\project\file\log\FileLogEditor;
use wcf\system\cache\builder\PackageCacheBuilder;
use wcf\system\cache\builder\ProjectFileCacheBuilder;
use wcf\data\project\ProjectList;
use wcf\data\project\Project;
use wcf\system\Regex;
use wcf\data\project\file\log\ProjectFileLogCache;
use wcf\system\cache\builder\ProjectFileLogCacheBuilder;

/**
 * Minifies javascript files of projects.
 * 
 * Automatically updates minified files if the original javascript has changed.
 * Also deletes minified files if the original file is deleted.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectJSMinifyListener implements IParameterizedEventListener {
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// Get projects
		$projects = ProjectList::getProjectsFromCache();
		if(empty($projects)) {
			return;
		}
		
		// Get logged js files
		$fileLogs = FileLogList::getFilesByPackageIDs(array_keys($projects), Regex::compile('^(acp/)?js/([a-zA-Z0-9\_\.\-]+)\.js$'));
		
		// Iterate over logged js files
		$deleteFiles = array();
		$resetPackageCaches = array();
		foreach($fileLogs as $fileLog) {
			// Find minified js files which do not
			// have a non-minified equivalent -> delete
			// (because the non-minified file was deleted)
			if(StringUtil::endsWith($fileLog->filename, '.min.js')) {
				// Build non-minified filename
				$nonMinifiedFilename = mb_substr($fileLog->getProjectPath(), 0, -7) . '.js';
				
				// Check if non-minified file exists
				// Otherwise delete minified file
				if(!file_exists($nonMinifiedFilename)) {
					$deleteFiles[] = $fileLog;
					
					// Add packageID to reset cache array
					$resetPackageCaches[$fileLog->packageID] = true;
				}
			}
			// Find non-minified js files which
			// do not have a minified equivalent
			// or were updated -> minify
			else {
				$minJsFilename = mb_substr($fileLog->filename, 0, -3) . '.min.js';
				$minJsFilepath = Project::getProject($fileLog->packageID)->getDirectory() . $minJsFilename;
				$minJsFileLog = ProjectFileLogCache::getInstance()->getApplicationFileLog($fileLog->application, $minJsFilename);
				$minJsFileLogExists = ($minJsFileLog !== null);
				
				// Check if minified file log exists
				// or minified file is outdated
				if(!$minJsFileLogExists || $fileLog->getProjectFileModificationTime() > $minJsFileLog->getProjectFileModificationTime()) {
					// Get javascript
					$jsContent = $fileLog->getProjectFileContents();
					
					// Minify javascript
					$minJsContent = $this->minifyJS($jsContent);
					
					// Create new minified file
					if(!$minJsFileLogExists) {
						$minJsFileLog = FileLogEditor::create(array(
							'packageID' => $fileLog->packageID,
							'filename' => $minJsFilename,
							'application' => $fileLog->application
						));
						
						// Add packageID to reset cache array
						$resetPackageCaches[$fileLog->packageID] = true;
					}
					
					// Override file contents
					$minJsFileLog->setProjectFileContents($minJsContent);
					$minJsFileLog->setApplicationFileContents($minJsContent);
				}
			}
		}
		
		// Delete files
		foreach($deleteFiles as $fileLog) {
			$fileLog->deleteProjectFile();
		}
		
		// Delete file logs
		$action = new ProjectFileLogAction($deleteFiles, 'delete');
		$action->executeAction();
		
		// Reset caches
		if(!empty($resetPackageCaches)) {
			ProjectFileLogCacheBuilder::getInstance()->reset();
		}
		
		foreach($resetPackageCaches as $packageID => $tmp) {
			ProjectFileCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
	}
	
	/**
	 * Minifies the given Javascript code. 
	 * 
	 * @see  https://gist.github.com/tovic/d7b310dea3b33e4732c0
	 * @param string $js
	 * @return string
	 */
	public function minifyJS($js) {
		// Check for empty input
		if(trim($js) === "") return $js;
		
		$minified = preg_replace(
			array(
				// Remove comment(s)
				'#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
				// Remove white-space(s) outside the string and regex
				'#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
				// Remove the last semicolon
				'#;+\}#',
				// Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
				'#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
				// --ibid. From `foo['bar']` to `foo.bar`
				'#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
			),
			array(
				'$1',
				'$1$2',
				'}',
				'$1$3',
				'$1.$3'	
			),
			$js
		);
		
		return StringUtil::trim($minified);
	}
}
