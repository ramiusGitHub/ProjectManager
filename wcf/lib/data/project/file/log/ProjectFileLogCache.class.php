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

use wcf\system\SingletonFactory;
use wcf\system\cache\builder\ProjectFileLogCacheBuilder;

/**
 * Manages the file log cache.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectFileLogCache extends SingletonFactory {
	/**
	 * @var array<mixed>
	 */
	protected $fileLogCache;
	
	/**
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->fileLogCache = ProjectFileLogCacheBuilder::getInstance()->getData();
	}
	
	/**
	 * Returns the file which is logged with the given log ID
	 * or null if no entry with this ID exists.
	 * 
	 * @param int $logID
	 * @return \wcf\data\project\file\log\FileLog
	 */
	public function getFileLog($logID) {
		if(isset($this->fileLogCache['list'][$logID])) {
			return $this->fileLogCache['list'][$logID];
		}
		
		return null;
	}
		
	/**
	 * Returns all file logs which belong to the package ID.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\project\file\log\FileLog>
	 */
	public function getPackageFileLogs($packageID) {
		if(isset($this->fileLogCache['packageFileLogs'][$packageID])) {
			return $this->fileLogCache['packageFileLogs'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all file logs which belong to the application.
	 * 
	 * @param string $application
	 * @return array<\wcf\data\project\file\log\FileLog>
	 */
	public function getApplicationFileLogs($application = 'wcf') {
		if(isset($this->fileLogCache['applicationFileLogs'][$application])) {
			return $this->fileLogCache['applicationFileLogs'][$application];
		}
		
		return array();
	}
	
	/**
	 * Returns the file log which belongs to the application and has the given filename.
	 * 
	 * @param string $application
	 * @param string $filename
	 * @return \wcf\data\project\file\log\FileLog
	 */
	public function getApplicationFileLog($application, $filename) {
		if(isset($this->fileLogCache['applicationFileLogs'][$application][$filename])) {
			return $this->fileLogCache['applicationFileLogs'][$application][$filename];
		}
		
		return null;
	}
}
