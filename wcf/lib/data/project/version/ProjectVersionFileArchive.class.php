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

use wcf\util\ProjectUtil;
use wcf\system\io\TarWriter;
use wcf\data\application\Application;
use wcf\util\FileUtil;
use wcf\system\io\Tar;
use wcf\util\StringUtil;

/**
 * The ProjectVersionFileArchive class is used to create and access
 * archives with the files, templates and ACP templates of a specific
 * version of a project.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	WCoding Project Manager Licence (WCPMEL) <http://wcoding.de/licenses/wcpmel.txt>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionFileArchive {
	/**
	 * The filename of the tar archive.
	 * 
	 * @var string
	 */
	protected $archiveName;
	
	/**
	 * The Tar archive object.
	 * 
	 * @var \wcf\system\io\Tar
	 */
	protected $tar;
	
	/**
	 * Creates a new ProjectVersionFileArchive instance for the given version.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion $version
	 */
	public function __construct(\wcf\data\project\version\ProjectVersion $version) {
		$this->archiveName = ProjectUtil::generateVersionArchiveName($version);
	}
	
	/**
	 * Returns the path to this archive.
	 * 
	 * @return string
	 */
	public function getPath() {
		return $this->archiveName;
	}
	
	/**
	 * Returns the tar archive.
	 * 
	 * @return \wcf\system\io\Tar
	 */
	public function getTar() {
		if($this->tar === null) {
			$this->tar = new Tar($this->getPath());
		}
			
		return $this->tar;
	}
	
	/**
	 * Returns whether the archive is empty.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return !file_exists($this->getPath());
	}
	
	/**
	 * Deletes the archive.
	 */
	public function delete() {
		if(!$this->isEmpty()) {
			unlink($this->getPath());
		}
	}
	
	/**
	 * Returns all files and their contents.
	 * 
	 * @return array<string>
	 */
	public function getFiles() {
		$files = array();
		
		if(!$this->isEmpty()) {
			// Iterate over content list of the archive
			foreach($this->getTar()->getContentList() as $tarFile) {
				// Only read files
				if($tarFile['type'] == 'file') {
					$files[$tarFile['filename']] = $this->getTar()->extractToString($tarFile['index']);
				}
			}
		}
		
		return $files;
	}
	
	/**
	 * Extracts the files from the archive and writes
	 * them to the target directory.
	 * 
	 * @param string $targetDirectory
	 */
	public function extract($targetDirectory) {
		if(!$this->isEmpty()) {
			// Iterate over content list of the archive
			foreach($this->getTar()->getContentList() as $tarFile) {
				// Only copy files
				if($tarFile['type'] == 'file') {
					// Get absolute directory
					$destination = $targetDirectory . $tarFile['filename'];
						
					// Create directory
					FileUtil::makePath(dirname($destination));
						
					// Extract file
					$this->getTar()->extract($tarFile['index'], $destination);
				}
			}
		}
	}
	
	/**
	 * Creates a new archive with the files, templates and ACP templates of
	 * the source version and labels the archive with the target version.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion $sourceVersion
	 * @param \wcf\data\project\version\ProjectVersion $targetVersion
	 */
	public static function create(\wcf\data\project\version\ProjectVersion $sourceVersion, \wcf\data\project\version\ProjectVersion $targetVersion) {
		$targetTar = ProjectUtil::generateVersionArchiveName($targetVersion);
		FileUtil::makePath(dirname($targetTar));
		
		// Source version is currently active version
		// -> copy files from project directory
		if($sourceVersion->isCurrentVersion()) {
			// Check whether project has files, templates or ACP templates
			if($sourceVersion->getProject()->getFileCount() || $sourceVersion->getProject()->getTemplateCount() || $sourceVersion->getProject()->getACPTemplateCount()) {
				// Create tar archive in target dir
				$tar = new TarWriter($targetTar);
				$archiveDirectories = array();
		
				// Copy files
				foreach($sourceVersion->getProject()->getFiles() as $fileLog) {
					// Add directory
					$archiveDirectories[] = $fileLog->application . '/' . dirname($fileLog->filename);
		
					// Add file
					$removeDir = StringUtil::replaceIgnoreCase($fileLog->filename, '', $fileLog->getProjectPath());
					$tar->add($fileLog->getProjectPath(), $fileLog->application . '/', $removeDir);
				}
					
				// Copy templates
				foreach($sourceVersion->getProject()->getTemplates() as $template) {
					$sourceName = $template->getPath();
					$relativeName = mb_substr($sourceName, mb_strlen(Application::getDirectory($template->application)));
					$targetName = $template->application . '/' . $relativeName;
		
					// Add directory
					$archiveDirectories[] = dirname($targetName);
		
					// Add template
					$tar->add($sourceName, $template->application . '/', Application::getDirectory($template->application));
				}
					
				// Copy ACP templates
				foreach($sourceVersion->getProject()->getACPTemplates() as $template) {
					$relativeName = 'acp/templates/' . $template->templateName . '.tpl';
					$sourceName = Application::getDirectory($template->application) . $relativeName;
					$targetName = $template->application . '/' . $relativeName;
		
					// Add directory
					$archiveDirectories[] = dirname($targetName);
		
					// Add template
					$tar->add($sourceName, $template->application . '/', Application::getDirectory($template->application));
				}
		
				// Add directory headers
				$adddedDirectories = array();
				foreach($archiveDirectories as $directory) {
					$parts = '';
					foreach(explode('/', $directory) as $part) {
						$parts .= $part . '/';
							
						if(!isset($adddedDirectories[$parts])) {
							$tar->writeHeaderBlock($parts, 0, 0, 755, '5');
							$adddedDirectories[$parts] = true;
						}
					}
				}
		
				// Create archive and make file writeable
				$tar->create();
				FileUtil::makeWritable($targetTar);
			}
		}
		// Source version is not active
		// -> copy archive
		else {
			// Get source archive
			$sourceArchive = new ProjectVersionFileArchive($sourceVersion);
		
			// If the source version has no files, templates and ACP templates
			// then the source's archive is empty
			if(!$sourceArchive->isEmpty()) {
				copy($sourceArchive->getPath(), $targetTar);
			}
		}
	}
}
