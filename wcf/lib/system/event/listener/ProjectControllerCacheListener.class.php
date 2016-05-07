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
use wcf\system\cache\CacheHandler;
use wcf\system\cache\builder\ControllerCacheBuilder;
use wcf\system\application\ApplicationHandler;
use wcf\system\WCF;
use wcf\data\project\file\log\FileLogList;
use wcf\util\StringUtil;
use wcf\data\project\file\log\ProjectFileLogCache;

/**
 * Adds the controllers of external projects to the controller cache file.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectControllerCacheListener implements IParameterizedEventListener {
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// If the autoloader is not used, the controllers are copied into the
		// real application directories. Therefore they are cached automatically.
		if(!PROJECT_MANAGER_USE_AUTOLOADER)
			return;
		
		// Get external projects
		$packageIDs = array();
		$packages = PackageCache::getInstance()->getPackages();
		foreach($packages['packages'] as $package) {
			if(!empty($package->projectDirectory)) $packageIDs[$package->packageID] = true;
		}
		
		// If there are not external projects, extending the cached controllers is not necessary
		if(empty($packageIDs))
			return;
		
		// Get controllers of external projects
		$fileLogList = new FileLogList();
		$fileLogList->getConditionBuilder()->add("packageID IN (?)", array(array_keys($packageIDs)));
		$fileLogList->getConditionBuilder()->add(
			"(filename LIKE ? OR filename LIKE ? OR filename LIKE ? OR filename LIKE ? OR filename LIKE ? OR filename LIKE ?)",
			array(
				'lib/page/%',
				'lib/form/%',
				'lib/action/%',
				'lib/acp/page/%',
				'lib/acp/form/%',
				'lib/acp/action/%'
			)
		);
		$fileLogList->readObjects(false); // False: ordering is not necessary
		$fileLogs = $fileLogList->getObjects();
		
		// Build array of external controllers (controllers in external projects)
		$data = array(
			'user' => array(),
			'admin' => array()
		);
		foreach($fileLogs as $fileLog) {
			// Get environment
			$isACP = StringUtil::startsWith($fileLog->filename, 'lib/acp/');
			
			// Get controller type
			if(StringUtil::startsWith($fileLog->filename, 'lib'.($isACP ? '/acp' : '').'/page')) $type = 'page';
			elseif(StringUtil::startsWith($fileLog->filename, 'lib'.($isACP ? '/acp' : '').'/form')) $type = 'form';
			elseif(StringUtil::startsWith($fileLog->filename, 'lib'.($isACP ? '/acp' : '').'/action')) $type = 'action';
			
			// Build namespace + class name
			$className = basename($fileLog->filename);
			$length = strlen($className) - strlen($type) - 10; // 10 = strlen('.class.php');
			$className = substr($className, 0, $length);
			$fqn = '\\' . $fileLog->application . '\\' . ($isACP ? 'acp\\' : '') . $type . '\\' . $className . ucfirst($type);
			
			// Add controller
			$data[($isACP ? 'admin' : 'user')][$fileLog->application][$type][mb_strtolower($className)] = $fqn;
		}
		
		// Merge external controllers with regular controllers
		foreach(array('user', 'admin') as $environment) {
			// Get cached controllers
			$controllers = ControllerCacheBuilder::getInstance()->rebuild(array('environment' => $environment));
			
			// Add external controllers to cached controllers
			foreach($data[$environment] as $application => $applicationData) {
				foreach($applicationData as $type => $typeData) {
					$controllers[$application][$type] = array_merge($controllers[$application][$type], $typeData);
				}
			}
			
			// Write result to cache
			CacheHandler::getInstance()->set(ControllerCacheBuilder::getInstance(), array('environment' => $environment), $controllers);
		}
	}
}
