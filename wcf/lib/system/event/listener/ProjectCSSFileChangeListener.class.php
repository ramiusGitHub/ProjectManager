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
use wcf\util\ProjectUtil;

/**
 * If at least one .less file changed the compilation of the CSS files is triggered.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectCSSFileChangeListener implements IParameterizedEventListener {
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// Read filenames of .less stylesheets and get date of latest file change
		$conditions = new PreparedStatementConditionBuilder();
		$conditions->add("filename REGEXP ?", array('style/([a-zA-Z0-9\_\-\.]+)\.less'));
		
		$sql = "SELECT	*
			FROM	wcf".WCF_N."_package_installation_file_log
			".$conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($conditions->getParameters());
		$files = array();
		while($row = $statement->fetchArray()) {
			$files[] = $row;
		}
		
		$latestChangeTime = 0;
		foreach($files as $file) {
			$filename = Application::getDirectory($file['application']) . $file['filename'];
			
			if(file_exists($filename)) {
				$time = filemtime($filename);
				if($time > $latestChangeTime) $latestChangeTime = $time;
			}
		}
		
		// Get compiled css files
		$cssFiles = array_merge(
			ProjectUtil::getFileList(WCF_DIR.'style/', array(), array(), array(), array('css'), false),
			ProjectUtil::getFileList(WCF_DIR.'acp/style/', array(), array(), array(), array('css'), false)
		);
		
		// Unlink all css files older than the latest .less-file change
		foreach($cssFiles as $cssFile) {
			if(filemtime($cssFile) < $latestChangeTime) {
				unlink($cssFile);
			}
		}
	}
}
