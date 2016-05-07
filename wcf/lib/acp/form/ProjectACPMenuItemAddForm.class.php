<?php
namespace wcf\acp\form;

use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\data\acp\menu\item\ACPMenuItemList;
use wcf\data\project\ProjectDataAction;
use wcf\data\acp\menu\item\ACPMenuItemAction;
use wcf\system\cache\builder\ACPMenuCacheBuilder;
use wcf\system\application\ApplicationHandler;
use wcf\data\package\PackageCache;
use wcf\data\package\Package;

/**
 * Implementation of the menu item form for ACP menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACPMenuItemAddForm extends AbstractProjectMenuItemForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$type
	 */
	protected static $type = 'acpMenuItem';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\acp\menu\item\ProjectACPMenuItemAction';
	
	/**
	 * @var array<string>
	 */
	public $parentMenuItems = array();
	
	/**
	 * @var string
	 */
	public $parentMenuItem = '';
	
	/**
	 * @var string
	 */
	public $menuItemController = '';
	
	/**
	 * @var string
	 */
	public $menuItemLink = '';

	/**
	 * Set default value of $menuItem.
	 * 
	 * @see \wcf\acp\form\AbstractProjectMenuItemForm::$menuItem
	 */
	public $menuItem = 'wcf.acp.menu.link.';
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Varchar parameters
		if(isset($_POST['parentMenuItem'])) {
			$this->parentMenuItem = StringUtil::trim($_POST['parentMenuItem']);
		}
		
		if(isset($_POST['menuItemController'])) {
			$this->menuItemController = trim(StringUtil::trim($_POST['menuItemController']), '\\');
		} else {
			$this->menuItemController = '';
		}
		
		if(isset($_POST['menuItemLink'])) {
			$this->menuItemLink = StringUtil::trim($_POST['menuItemLink']);
		} else {
			$this->menuItemLink = '';
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Menu item
		if(!isset($this->errorType['menuItem'])) {
			$abbreviations = array('wcf');
			foreach(ApplicationHandler::getInstance()->getApplications() as $packageID => $application) {
				$package = PackageCache::getInstance()->getPackage($packageID);
				$abbreviations[] = Package::getAbbreviation($package->package);
			}
			
			$found = false;
			foreach($abbreviations as $abbreviation) {
				if(mb_strpos($this->menuItem, $abbreviation . '.acp.menu.link.') === 0) {
					$found = true;
					break;
				}
			}
			
			if(!$found) {
				$this->errorType['menuItem'] = 'invalid';
			}
		}
		
		// Check parentMenuItem
		$this->validateParentMenuItem();
		
		// Check if controller exists
		if(!empty($this->menuItemController) && !class_exists($this->menuItemController)) {
			$this->errorType['menuItemController'] = 'nonExistent';
		}
	}
	
	/**
	 * Validates the parent menu item.
	 */
	protected function validateParentMenuItem() {
		if(!empty($this->parentMenuItem)) {
			$found = false;
			foreach($this->parentMenuItems as $parent => $value) {
				if(isset($value[$this->parentMenuItem]) || $parent == $this->parentMenuItem) {
					$found = true;
					break;
				}
			}
			if(!$found) $this->parentMenuItem = '';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'parentMenuItem' => $this->parentMenuItem,
				'menuItemController' => $this->menuItemController,
				'menuItemLink' => $this->menuItemLink
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		// Read categories before calling parent in order to have
		// the categories and types available in the validation methods
		$this->readCategories();
		
		// Call parent
		parent::readData();
	}
	
	/**
	 * Reads the menu item categories.
	 */
	public function readCategories() {
		$this->parentMenuItems = array('' => '');
		$items = ACPMenuCacheBuilder::getInstance()->getData();
		
		foreach($items as $parentMenuItem => $childs) {
			foreach($childs as $item) {
				// Skip the edited item itself
				if($item->menuItem == $this->menuItem) {
					continue;
				}
				
				// Items with controllers or links cannot operate as categories
				if(!empty($item->menuItemController) || !empty($item->menuItemLink)) {
					continue;
				}
				
				if(empty($parentMenuItem)) {
					$this->parentMenuItems[$item->menuItem] = html_entity_decode(WCF::getLanguage()->get($item->menuItem)) . ' (' . $item->menuItem . ')';
				} else {
					$this->parentMenuItems[html_entity_decode(WCF::getLanguage()->get($parentMenuItem))][$item->menuItem] = html_entity_decode(WCF::getLanguage()->get($item->menuItem)) . ' (' . $item->menuItem . ')';
				}
			}
		}
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'parentMenuItems' => $this->parentMenuItems,
			'parentMenuItem' => $this->parentMenuItem,
			'menuItemController' => $this->menuItemController,
			'menuItemLink' => $this->menuItemLink
		));
	}
}
