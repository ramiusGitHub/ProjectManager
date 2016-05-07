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
namespace wcf\data\project\cronjob;

use wcf\data\project\AbstractProjectDatabaseObjectAction;
use wcf\system\cache\builder\ProjectCronjobCacheBuilder;
use wcf\system\cache\builder\CronjobCacheBuilder;

/**
 * Implementation of the project database object action for cronjobs.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectCronjobAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::$actionClassName
	 */
	public static $actionClassName = '\wcf\data\cronjob\CronjobAction';
	
	/**
	 * @see \wcf\data\AbstractDatabaseObjectAction::create()
	 */
	public function create() {
		// Call parent
		$object = parent::create();
		
		// If language items are provided for the description, we have to update
		// the created cronjob object's description field to the language item name
		if(isset($this->parameters['languageItems']['description']['languageItemValues'])) {
			$languageItems = $this->getLanguageItemNames($object);
			$action = new ProjectCronjobAction(
				array($result),
				'update',
				array(
					'description' => $languageItems['description']
				)
			);
			$action->executeAction();
		}
		
		// Return object
		return $object;
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getLanguageItemNames()
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		return array(
			'description' => 'wcf.acp.cronjob.description.cronjob' . $object->getObjectID()
		);
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::clearCaches()
	 */
	protected function clearCaches() {
		// Clear project caches
		foreach($this->getPackageIDs() as $packageID) {
			ProjectCronjobCacheBuilder::getInstance()->reset(array('packageID' => $packageID));
		}
		
		// Clear regular caches
		CronjobCacheBuilder::getInstance()->reset();
	}
}
