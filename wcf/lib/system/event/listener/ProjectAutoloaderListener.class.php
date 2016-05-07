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
use wcf\system\application\ApplicationHandler;

/**
 * Adds an autoloader in order to load classes from the project directories.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectAutoloaderListener implements IParameterizedEventListener {
	/**
	 * list of autoload directories
	 * @var array
	 */
	protected static $autoloadDirectories = null;
	
	/**
	 * Creates a new ProjectAutoloaderListener instance.
	 */
	public function __construct() {
		// This is a bit tricky: We have to register the autoloader right here
		// because the EventHandler tries to instanciate all EventListener,
		// which are registered on the same event as this one, before executing them.
		// If the autoloader is not registered beforehand, those other
		// EventListeners will not be found and an exception is thrown.
		
		// Check if autoloader is activated
		if(!PROJECT_MANAGER_USE_AUTOLOADER) {
			return;
		}
		
		// Register additional project autoload function
		spl_autoload_register(array('wcf\system\event\listener\ProjectAutoloaderListener', 'autoload'));
	}
	
	/**
	 * @see \wcf\system\event\listener\IParameterizedEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		// Does nothing, see constructor
	}
	
	/**
	 * Includes classes in projects' directories automatically.
	 * 
	 * @param string $className
	 * @see  spl_autoload_register()
	 */
	public static final function autoload($className) {
		// Check if previous autoloader already found the class
		if(class_exists($className)) return;
		
		// Init autoload directories
		if(!isset(static::$autoloadDirectories)) {
			static::$autoloadDirectories = array();
			
			$packages = PackageCache::getInstance()->getPackages();
			foreach($packages['packages'] as $package) {
				if($package->isActiveProject && !empty($package->projectDirectory)) static::$autoloadDirectories[] = $package->projectDirectory;
			}
		}
		
		// Try to find class
		$namespaces = explode('\\', $className);
		if(count($namespaces) > 1) {
			$application = array_shift($namespaces);
			array_unshift($namespaces, $application, 'lib');
			$classPath = implode('/', $namespaces) . '.class.php';
			
			foreach(static::$autoloadDirectories as $directory) {
				$filename = $directory . $classPath;
				
				if(file_exists($filename)) {
					require_once($filename);
					return;
				}
			}
		}
	}
}
