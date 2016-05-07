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
namespace wcf\acp\form;

use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\system\cache\builder\UserMenuCacheBuilder;
use wcf\system\application\ApplicationHandler;
use wcf\data\package\PackageCache;
use wcf\data\package\Package;

/**
 * Extension of the ACP menu item form for user menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectUserMenuItemAddForm extends ProjectACPMenuItemAddForm  {
	/**
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$type
	 */
	protected static $type = 'userMenuItem';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\user\menu\item\ProjectUserMenuItemAction';
	
	/**
	 * @var string
	 */
	public $itemClassName = '';
	
	/**
	 * @var string
	 */
	public $iconClassName = '';
	
	/**
	 * Set default value of $menuItem.
	 * 
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$menuItem
	 */
	public $menuItem = 'wcf.user.menu.';
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Class name
		if(isset($_POST['className'])) {
			$this->itemClassName = StringUtil::trim($_POST['className']);
		}
		
		// Icon class name
		if(isset($_POST['iconClassName'])) {
			$this->iconClassName = StringUtil::trim($_POST['iconClassName']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		AbstractProjectMenuItemForm::validate();
		
		// Menu item
		if(!isset($this->errorType['menuItem'])) {
			$abbreviations = array('wcf');
			foreach(ApplicationHandler::getInstance()->getApplications() as $packageID => $application) {
				$package = PackageCache::getInstance()->getPackage($packageID);
				$abbreviations[] = Package::getAbbreviation($package->package);
			}
			
			$found = false;
			foreach($abbreviations as $abbreviation) {
				if(mb_strpos($this->menuItem, $abbreviation . '.user.menu.') === 0) {
					$found = true;
					break;
				}
			}
			
			if(!$found) {
				$this->errorType['menuItem'] = 'invalid';
			}
		}
		
		// Check if all links are empty
		if(empty($this->itemClassName) && empty($this->menuItemController) && empty($this->menuItemLink)) {
			$this->errorType['className'] = 'allEmpty';
			$this->errorType['menuItemController'] = 'allEmpty';
			$this->errorType['menuItemLink'] = 'allEmpty';
		}
		
		// Item class name
		if(!empty($this->itemClassName)) {
			if(!class_exists($this->itemClassName)) {
				$this->errorType['className'] = 'nonExistent';
			} else {
				$class = new \ReflectionClass($this->itemClassName);
				if(!$class->implementsInterface("wcf\system\menu\user\IUserMenuItemProvider")) {
					$this->errorType['className'] = 'interface';
				}
			}
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'className' => $this->itemClassName,
				'iconClassName' => $this->iconClassName
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\acp\form\ProjectACPMenuItemAddForm::readCategories()
	 */
	public function readCategories() {
		$this->parentMenuItems = array('' => '');
		$data = UserMenuCacheBuilder::getInstance()->getData();
		$items = $data[""];
		
		foreach($items as $item) {
			if($item->menuItem != $this->menuItem) {
				$this->parentMenuItems[$item->menuItem] = WCF::getLanguage()->get($item->menuItem) . ' (' . $item->menuItem . ')';
			}
		}
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array(
			'className' => $this->itemClassName,
			'iconClassName' => $this->iconClassName
		));
	}
}
