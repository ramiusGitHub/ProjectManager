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

use wcf\data\page\menu\item\PageMenuItemList;
use wcf\system\WCF;
use wcf\data\page\menu\item\PageMenuItem;
use wcf\system\Regex;
use wcf\system\application\ApplicationHandler;
use wcf\data\package\PackageCache;
use wcf\data\package\Package;
use wcf\util\StringUtil;

/**
 * Extension of the user menu item form for page menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectPageMenuItemAddForm extends ProjectACPMenuItemAddForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$type
	 */
	protected static $type = 'pageMenuItem'; 
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\page\menu\item\ProjectPageMenuItemAction';
	
	/**
	 * @var string
	 */
	public $menuPosition = 'header';
	
	/**
	 * @var string
	 */
	public $itemClassName = '';
	
	/**
	 * Set default value of $menuItem.
	 * 
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$menuItem
	 */
	public $menuItem = '';
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Class name
		if(isset($_POST['className'])) {
			$this->itemClassName = StringUtil::trim($_POST['className']);
		}
		
		// Menu position (header or footer)
		if(isset($_POST['menuPosition']) && $_POST['menuPosition'] == 'footer') {
			$this->menuPosition = 'footer';
		} else {
			$this->menuPosition = 'header';
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		AbstractProjectMenuItemForm::validate();
		
		// Menu item
		if(!isset($this->errorType['menuItem'])) {
			if(!Regex::compile("[a-z]+\.[a-zA-Z]+(\.[a-zA-Z]+)+")->match($this->menuItem)) {
				$this->errorType['menuItem'] = 'invalid';
			} else {
				// Menu item
				$abbreviations = array('wcf');
				foreach(ApplicationHandler::getInstance()->getApplications() as $packageID => $application) {
					$package = PackageCache::getInstance()->getPackage($packageID);
					$abbreviations[] = Package::getAbbreviation($package->package);
				}
					
				$found = false;
				foreach($abbreviations as $abbreviation) {
					if(mb_strpos($this->menuItem, $abbreviation . '.') === 0) {
						$found = true;
						break;
					}
				}
				
				if(!$found) {
					$this->errorType['menuItem'] = 'invalid';
				}
			}
		}
		
		// Menu item controller and link
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
				if(!$class->implementsInterface("\wcf\system\menu\page\IPageMenuItemProvider")) {
					$this->errorType['className'] = 'interface';
				}
			}
		}
	}
	
	/**
	 * @see \wcf\acp\form\ProjectACPMenuItemAddForm::validateParentMenuItem()
	 */
	protected function validateParentMenuItem() {
		if(!isset($this->parentMenuItems[$this->parentMenuItem])) {
			$this->parentMenuItem = '';
		}
	}
	
	/**
	 * @see \wcf\acp\form\ProjectUserMenuItemAddForm::readCategories()
	 */
	public function readCategories() {
		$this->parentMenuItems = array('' => '');
		
		// Get header page menu items
		$list = new PageMenuItemList();
		$list->getConditionBuilder()->add("page_menu_item.parentMenuItem = ''");
		$list->getConditionBuilder()->add('page_menu_item.menuPosition = ?', array('header'));
		$list->sqlOrderBy = "page_menu_item.showOrder ASC";
		$list->readObjects();
		
		foreach($list->getObjects() as $item) {
			$this->parentMenuItems[$item->menuItem] = $item . ' (' . $item->menuItem . ')';
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
				'menuPosition' => $this->menuPosition
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'className' => $this->itemClassName,
			'menuPosition' => $this->menuPosition
		));
	}
}
