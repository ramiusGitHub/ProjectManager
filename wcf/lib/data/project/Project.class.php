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
namespace wcf\data\project;

use wcf\data\package\Package;
use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\data\option\OptionList;
use wcf\data\option\category\OptionCategoryList;
use wcf\data\user\group\option\category\UserGroupOptionCategoryList;
use wcf\data\user\group\option\UserGroupOptionList;
use wcf\data\user\option\UserOptionList;
use wcf\data\user\option\category\UserOptionCategoryList;
use wcf\data\acp\menu\item\ACPMenuItemList;
use wcf\data\page\menu\item\PageMenuItemList;
use wcf\data\user\menu\item\UserMenuItemList;
use wcf\data\user\profile\menu\item\UserProfileMenuItemList;
use wcf\data\event\listener\EventListenerList;
use wcf\data\template\listener\TemplateListenerList;
use wcf\data\cronjob\CronjobList;
use wcf\data\language\item\LanguageItemList;
use wcf\data\template\TemplateList;
use wcf\data\acp\template\ACPTemplateList;
use wcf\system\language\LanguageFactory;
use wcf\data\language\category\LanguageCategoryList;
use wcf\data\object\type\ObjectTypeList;
use wcf\data\object\type\definition\ObjectTypeDefinitionList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\util\FileUtil;
use wcf\data\clipboard\action\ClipboardActionList;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\data\core\object\CoreObjectList;
use wcf\data\user\notification\event\UserNotificationEventList;
use wcf\data\acp\search\provider\ACPSearchProviderList;
use wcf\data\acl\option\ACLOptionList;
use wcf\data\acl\option\category\ACLOptionCategoryList;
use wcf\data\dashboard\box\DashboardBoxList;
use wcf\data\bbcode\BBCodeList;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\Regex;
use wcf\data\sitemap\SitemapList;
use wcf\data\smiley\SmileyList;
use wcf\data\style\StyleList;
use wcf\system\cache\CacheHandler;
use wcf\system\cache\builder\ProjectCacheBuilder;
use wcf\system\cache\builder\ProjectOptionCacheBuilder;
use wcf\system\cache\builder\ICacheBuilder;
use wcf\system\cache\builder\ProjectOptionCategoryCacheBuilder;
use wcf\system\cache\builder\ProjectUserGroupOptionCacheBuilder;
use wcf\system\cache\builder\ProjectUserGroupOptionCategoryCacheBuilder;
use wcf\system\cache\builder\ProjectUserOptionCategoryCacheBuilder;
use wcf\system\cache\builder\ProjectUserOptionCacheBuilder;
use wcf\system\cache\builder\ProjectACLOptionCategoryCacheBuilder;
use wcf\system\cache\builder\ProjectACLOptionCacheBuilder;
use wcf\system\cache\builder\ProjectACPMenuItemCacheBuilder;
use wcf\system\cache\builder\ProjectPageMenuItemCacheBuilder;
use wcf\system\cache\builder\ProjectUserMenuItemCacheBuilder;
use wcf\system\cache\builder\ProjectUserProfileMenuItemCacheBuilder;
use wcf\system\cache\builder\LanguageCacheBuilder;
use wcf\system\cache\builder\ProjectEventListenerCacheBuilder;
use wcf\system\cache\builder\ProjectTemplateListenerCacheBuilder;
use wcf\system\cache\builder\ProjectCronjobCacheBuilder;
use wcf\system\cache\builder\ProjectLanguageItemCacheBuilder;
use wcf\system\cache\builder\ProjectFileCacheBuilder;
use wcf\system\cache\builder\ProjectTemplateCacheBuilder;
use wcf\system\cache\builder\ProjectACPTemplateCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseTableCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseColumnCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseIndexCacheBuilder;
use wcf\system\cache\builder\ProjectDatabaseForeignKeyCacheBuilder;
use wcf\system\cache\builder\ProjectObjectTypeCacheBuilder;
use wcf\system\cache\builder\ProjectObjectTypeDefinitionCacheBuilder;
use wcf\system\cache\builder\ProjectCoreObjectCacheBuilder;
use wcf\system\cache\builder\ProjectClipboardActionCacheBuilder;
use wcf\system\cache\builder\ProjectUserNotificationEventCacheBuilder;
use wcf\system\cache\builder\ProjectACPSearchProviderCacheBuilder;
use wcf\system\cache\builder\ProjectStyleCacheBuilder;
use wcf\system\cache\builder\ProjectBBCodeCacheBuilder;
use wcf\system\cache\builder\ProjectSmileyCacheBuilder;
use wcf\system\cache\builder\ProjectDashboardBoxCacheBuilder;
use wcf\system\cache\builder\ProjectSitemapCacheBuilder;
use wcf\data\project\version\ProjectVersionList;
use wcf\system\exception\SystemException;
use wcf\data\application\Application;
use wcf\system\application\ApplicationHandler;
use wcf\system\event\EventHandler;
use wcf\data\package\PackageCache;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\system\cache\builder\ProjectVersionCacheBuilder;
use wcf\data\project\database\ProjectDatabaseIndex;
use wcf\system\cache\builder\ProjectInstructionCacheBuilder;

/**
 * A project decorates a package object and offers methods which provide information about
 * the package (options, menus and other PIPs) and the project (versions, project directory,
 * logged and deleted PIP objects which belong to the package).
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class Project extends DatabaseObjectDecorator {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\wcf\data\package\Package';
	
	/**
	 * All project versions.
	 * 
	 * @var array<\wcf\data\project\version\ProjectVersion>
	 */
	protected $versions;
	
	/**
	 * The projects current version.
	 * 
	 * @var \wcf\data\project\version\ProjectVersion
	 */
	protected $version;
	
	/**
	 * The latest released version number.
	 * 
	 * @var \wcf\data\project\version\ProjectVersion
	 */
	protected $latestReleasedVersion;
	
	/**
	 * Contains this project's data from the database.
	 * 
	 * @var array<mixed>
	 */
	protected $lists = array();
	
	/**
	 * Contains this project's data from the cache.
	 * 
	 * @var array<mixed>
	 */
	protected $cache = array();
	
	/**
	 * The directories containing the files of the project. 
	 * 
	 * @var array<string>
	 */
	protected $applicationDirectories;
	
	/**
	 * @var array<\wcf\data\project\Project>
	 */
	protected static $projects = array();
	
	/**
	 * Returns a common Project object for the package with the given package ID.
	 * 
	 * @param integer $packageID
	 * @return \wcf\data\project\Project
	 */
	public static function getProject($packageID) {
		if(!isset(static::$projects[$packageID])) {
			$package = PackageCache::getInstance()->getPackage($packageID);
			
			// Check if package exists
			if($package === null)
				return null;
			
			static::$projects[$packageID] = new Project($package);
		}
		
		return static::$projects[$packageID];
	}
	
	/**
	 * Returns the project's external directory.
	 * 
	 * @return string
	 */
	public function getDirectory() {
		return $this->projectDirectory;
	}
	
	/**
	 * Returns whether the project is currently in the active state.
	 * 
	 * @return boolean
	 */
	public function isActive() {
		return $this->isActiveProject;
	}
	
	/**
	 * Returns cached data of the given cache builder.
	 * 
	 * @param \wcf\system\cache\builder\ICacheBuilder $builder
	 * @return string $index
	 */
	protected function getCache(ICacheBuilder $builder, $index) {
		$cache = $builder->getData(array('packageID' => $this->packageID));
		
		return $cache[$index];
	}
	
	/**
	 * Clears all cached data.
	 */
	public function clearCaches() {
		$param = array('packageID' => $this->packageID);
		
		ProjectACLOptionCacheBuilder::getInstance()->reset($param);
		ProjectACLOptionCategoryCacheBuilder::getInstance()->reset($param);
		ProjectACPMenuItemCacheBuilder::getInstance()->reset($param);
		ProjectACPSearchProviderCacheBuilder::getInstance()->reset($param);
		ProjectACPTemplateCacheBuilder::getInstance()->reset($param);
		ProjectBBCodeCacheBuilder::getInstance()->reset($param);
		ProjectClipboardActionCacheBuilder::getInstance()->reset($param);
		ProjectCoreObjectCacheBuilder::getInstance()->reset($param);
		ProjectCronjobCacheBuilder::getInstance()->reset($param);
		ProjectDashboardBoxCacheBuilder::getInstance()->reset($param);
		ProjectDatabaseColumnCacheBuilder::getInstance()->reset($param);
		ProjectDatabaseForeignKeyCacheBuilder::getInstance()->reset($param);
		ProjectDatabaseIndexCacheBuilder::getInstance()->reset($param);
		ProjectDatabaseTableCacheBuilder::getInstance()->reset($param);
		ProjectEventListenerCacheBuilder::getInstance()->reset($param);
		ProjectFileCacheBuilder::getInstance()->reset($param);
		ProjectLanguageItemCacheBuilder::getInstance()->reset($param);
		ProjectObjectTypeCacheBuilder::getInstance()->reset($param);
		ProjectObjectTypeDefinitionCacheBuilder::getInstance()->reset($param);
		ProjectOptionCacheBuilder::getInstance()->reset($param);
		ProjectOptionCategoryCacheBuilder::getInstance()->reset($param);
		ProjectPageMenuItemCacheBuilder::getInstance()->reset($param);
		ProjectSitemapCacheBuilder::getInstance()->reset($param);
		ProjectSmileyCacheBuilder::getInstance()->reset($param);
		ProjectStyleCacheBuilder::getInstance()->reset($param);
		ProjectTemplateCacheBuilder::getInstance()->reset($param);
		ProjectTemplateListenerCacheBuilder::getInstance()->reset($param);
		ProjectUserGroupOptionCacheBuilder::getInstance()->reset($param);
		ProjectUserGroupOptionCategoryCacheBuilder::getInstance()->reset($param);
		ProjectUserProfileMenuItemCacheBuilder::getInstance()->reset($param);
		
		EventHandler::getInstance()->fireAction($this, 'clearCaches');
	}
	
	/**
	 * Returns all versions of this package.
	 * 
	 * @return array<\wcf\data\project\version\ProjectVersion>
	 */
	public function getVersions() {
		return $this->getCache(ProjectVersionCacheBuilder::getInstance(), 'versions');
	}
	
	/**
	 * Returns the version of the package which is currently active.
	 * 
	 * @return \wcf\data\project\version\ProjectVersion
	 */
	public function getCurrentVersion() {
		return $this->getCache(ProjectVersionCacheBuilder::getInstance(), 'current');
	}
	
	/**
	 * Returns the packages excluded from parallel installation with this package.
	 * 
	 * @return array<string>
	 */
	public function getExcludedPackages() {
		if(!isset($this->lists['excluded'])) {
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_package_exclusion
				WHERE	packageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->packageID));
			
			$this->lists['excluded'] = array();
			while($row = $statement->fetchArray()) {
				$this->lists['excluded'][] = $row;
			}
		}
		
		return $this->lists['excluded'];
	}
	
	/**
	 * Returns the packages required for this package.
	 * 
	 * @return array<string>
	 */
	public function getRequiredPackages() {
		if(!isset($this->lists['required'])) {
			$sql = "SELECT		requirement.version AS rPackageVersion, package.packageVersion, package.package, package.packageDate
				FROM		wcf".WCF_N."_package_requirement requirement
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(requirement.requirement = package.packageID)
				WHERE		requirement.packageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->packageID));
			
			$this->lists['required'] = array();
			while($row = $statement->fetchArray()) {
				if(!empty($row['rPackageVersion'])) $row['packageVersion'] = $row['rPackageVersion'];
				unset($row['rPackageVersion']);
				
				$this->lists['required'][] = $row;
			}
		}
	
		return $this->lists['required'];
	}
	
	/**
	 * Returns the optional packages delivered with this package.
	 * 
	 * @return array<mixed>
	 */
	public function getOptionalPackages() {
		if(!isset($this->lists['optionals'])) {
			$sql = "SELECT		package.packageVersion, package.package, package.packageDate
				FROM		wcf".WCF_N."_package_optional optional
				LEFT JOIN	wcf".WCF_N."_package package
				ON		(optional.optionalID = package.packageID)
				WHERE		optional.packageID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($this->packageID));
				
			$this->lists['optionals'] = array();
			while($row = $statement->fetchArray()) {
				$this->lists['optionals'][] = $row;
			}
		}
	
		return $this->lists['optionals'];
	}
	
	/**
	 * Returns the project's options.
	 * 
	 * @return array<\wcf\data\option\Option>
	 */
	public function getOptions() {
		return $this->getCache(ProjectOptionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of options.
	 * 
	 * @return integer
	 */
	public function getOptionCount() {
		return $this->getCache(ProjectOptionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted options.
	 * 
	 * @return array<\wcf\data\option\Option>
	 */
	public function getDeletedOptions() {
		return $this->getCache(ProjectOptionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged options.
	 *
	 * @return array<\wcf\data\option\Option>
	 */
	public function getLoggedOptions() {
		return $this->getCache(ProjectOptionCacheBuilder::getInstance(), 'log');
	}
	
	/**
	 * Returns the project's option categories.
	 * 
	 * @return array<\wcf\data\option\category\OptionCategory>
	 */
	public function getOptionCategories() {
		return $this->getCache(ProjectOptionCategoryCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of option categories.
	 * 
	 * @return integer
	 */
	public function getOptionCategoryCount() {
		return $this->getCache(ProjectOptionCategoryCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted option categories.
	 * 
	 * @return array<\wcf\data\option\category\OptionCategory>
	 */
	public function getDeletedOptionCategories() {
		return $this->getCache(ProjectOptionCategoryCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged options categories.
	 *
	 * @return array<\wcf\data\option\category\OptionCategory>
	 */
	public function getLoggedOptionCategories() {
		return $this->getCache(ProjectOptionCategoryCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user group options.
	 *
	 * @return array<\wcf\data\user\group\option\UserGroupOption>
	 */
	public function getUserGroupOptions() {
		return $this->getCache(ProjectUserGroupOptionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user group options.
	 * 
	 * @return integer
	 */
	public function getUserGroupOptionCount() {
		return $this->getCache(ProjectUserGroupOptionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted user group options.
	 * 
	 * @return array<\wcf\data\user\group\option\UserGroupOption>
	 */
	public function getDeletedUserGroupOptions() {
		return $this->getCache(ProjectUserGroupOptionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged user group options.
	 *
	 * @return array<\wcf\data\user\group\option\UserGroupOption>
	 */
	public function getLoggedUserGroupOptions() {
		return $this->getCache(ProjectUserGroupOptionCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user group option categories.
	 *
	 * @return array<\wcf\data\user\group\option\category\UserGroupOptionCategory>
	 */
	public function getUserGroupOptionCategories() {
		return $this->getCache(ProjectUserGroupOptionCategoryCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user group option categories.
	 * 
	 * @return integer
	 */
	public function getUserGroupOptionCategoryCount() {
		return $this->getCache(ProjectUserGroupOptionCategoryCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted user group option categories.
	 * 
	 * @return array<\wcf\data\user\group\option\category\UserGroupOptionCategory>
	 */
	public function getDeletedUserGroupOptionCategories() {
		return $this->getCache(ProjectUserGroupOptionCategoryCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged user group option categories.
	 *
	 * @return array<\wcf\data\user\group\option\category\UserGroupOptionCategory>
	 */
	public function getLoggedUserGroupOptionCategories() {
		return $this->getCache(ProjectUserGroupOptionCategoryCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user options.
	 *
	 * @return array<\wcf\data\user\option\UserOption>
	 */
	public function getUserOptions() {
		return $this->getCache(ProjectUserOptionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user options.
	 * 
	 * @return integer
	 */
	public function getUserOptionCount() {
		return $this->getCache(ProjectUserOptionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted user options.
	 * 
	 * @return array<\wcf\data\user\option\UserOption>
	 */
	public function getDeletedUserOptions() {
		return $this->getCache(ProjectUserOptionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged user options.
	 *
	 * @return array<\wcf\data\user\option\UserOption>
	 */
	public function getLoggedUserOptions() {
		return $this->getCache(ProjectUserOptionCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user option categories.
	 *
	 * @return array<\wcf\data\user\option\category\UserOptionCategory>
	 */
	public function getUserOptionCategories() {
		return $this->getCache(ProjectUserOptionCategoryCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user option categories.
	 * 
	 * @return integer
	 */
	public function getUserOptionCategoryCount() {
		return $this->getCache(ProjectUserOptionCategoryCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted user option categories.
	 * 
	 * @return array<\wcf\data\user\option\category\UserOptionCategory>
	 */
	public function getDeletedUserOptionCategories() {
		return $this->getCache(ProjectUserOptionCategoryCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged user option categories.
	 *
	 * @return array<\wcf\data\user\option\category\UserOptionCategory>
	 */
	public function getLoggedUserOptionCategories() {
		return $this->getCache(ProjectUserOptionCategoryCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's acl options.
	 *
	 * @return array<\wcf\data\acl\option\ACLOption>
	 */
	public function getACLOptions() {
		return $this->getCache(ProjectACLOptionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of acl options.
	 * 
	 * @return integer
	 */
	public function getACLOptionCount() {
		return $this->getCache(ProjectACLOptionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted acl options.
	 * 
	 * @return array<\wcf\data\acl\option\ACLOption>
	 */
	public function getDeletedACLOptions() {
		return $this->getCache(ProjectACLOptionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged acl options.
	 *
	 * @return array<\wcf\data\acl\option\ACLOption>
	 */
	public function getLoggedACLOptions() {
		return $this->getCache(ProjectACLOptionCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's acl option categories.
	 *
	 * @return array<\wcf\data\acl\option\category\ACLOptionCategoryList>
	 */
	public function getACLOptionCategories() {
		return $this->getCache(ProjectACLOptionCategoryCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of acl option categories.
	 * 
	 * @return integer
	 */
	public function getACLOptionCategoryCount() {
		return $this->getCache(ProjectACLOptionCategoryCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted acl option categories.
	 * 
	 * @return array<\wcf\data\acl\option\category\ACLOptionCategoryList>
	 */
	public function getDeletedACLOptionCategories() {
		return $this->getCache(ProjectACLOptionCategoryCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged acl option categories.
	 *
	 * @return array<\wcf\data\acl\option\category\ACLOptionCategoryList>
	 */
	public function getLoggedACLOptionCategories() {
		return $this->getCache(ProjectACLOptionCategoryCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's acp menu items.
	 *
	 * @return array<\wcf\data\acp\menu\item\ACPMenuItem>
	 */
	public function getACPMenuItems() {
		return $this->getCache(ProjectACPMenuItemCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of acp menu items.
	 * 
	 * @return integer
	 */
	public function getACPMenuItemCount() {
		return $this->getCache(ProjectACPMenuItemCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted acp menu items.
	 * 
	 * @return array<\wcf\data\acp\menu\item\ACPMenuItem>
	 */
	public function getDeletedACPMenuItems() {
		return $this->getCache(ProjectACPMenuItemCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged acp menu items.
	 *
	 * @return array<\wcf\data\acp\menu\item\ACPMenuItem>
	 */
	public function getLoggedACPMenuItems() {
		return $this->getCache(ProjectACPMenuItemCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's page menu items.
	 *
	 * @return array<\wcf\data\page\menu\item\PageMenuItem>
	 */
	public function getPageMenuItems() {
		return $this->getCache(ProjectPageMenuItemCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of page menu items.
	 * 
	 * @return integer
	 */
	public function getPageMenuItemCount() {
		return $this->getCache(ProjectPageMenuItemCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted page menu items.
	 * 
	 * @return array<\wcf\data\page\menu\item\PageMenuItem>
	 */
	public function getDeletedPageMenuItems() {
		return $this->getCache(ProjectPageMenuItemCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged page menu items.
	 *
	 * @return array<\wcf\data\page\menu\item\PageMenuItem>
	 */
	public function getLoggedPageMenuItems() {
		return $this->getCache(ProjectPageMenuItemCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user menu items.
	 *
	 * @return array<\wcf\data\user\menu\item\UserMenuItem>
	 */
	public function getUserMenuItems() {
		return $this->getCache(ProjectUserMenuItemCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user menu items.
	 * 
	 * @return integer
	 */
	public function getUserMenuItemCount() {
		return $this->getCache(ProjectUserMenuItemCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted user menu items.
	 * 
	 * @return array<\wcf\data\user\menu\item\UserMenuItem>
	 */
	public function getDeletedUserMenuItems() {
		return $this->getCache(ProjectUserMenuItemCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged user menu items.
	 *
	 * @return array<\wcf\data\user\menu\item\UserMenuItem>
	 */
	public function getLoggedUserMenuItems() {
		return $this->getCache(ProjectUserMenuItemCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user profile menu items.
	 *
	 * @return array<\wcf\data\user\profile\menu\item\UserProfileMenuItem>
	 */
	public function getUserProfileMenuItems() {
		return $this->getCache(ProjectUserProfileMenuItemCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user profile menu items.
	 * 
	 * @return integer
	 */
	public function getUserProfileMenuItemCount() {
		return $this->getCache(ProjectUserProfileMenuItemCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted user profile menu items.
	 * 
	 * @return array<\wcf\data\user\profile\menu\item\UserProfileMenuItem>
	 */
	public function getDeletedUserProfileMenuItems() {
		return $this->getCache(ProjectUserProfileMenuItemCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged user profile menu items.
	 *
	 * @return array<\wcf\data\user\profile\menu\item\UserProfileMenuItem>
	 */
	public function getLoggedUserProfileMenuItems() {
		return $this->getCache(ProjectUserProfileMenuItemCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's event listeners.
	 *
	 * @return array<\wcf\data\event\listener\EventListener>
	 */
	public function getEventListeners() {
		return $this->getCache(ProjectEventListenerCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of event listeners.
	 * 
	 * @return integer
	 */
	public function getEventListenerCount() {
		return $this->getCache(ProjectEventListenerCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted event listeners.
	 * 
	 * @return array<\wcf\data\event\listener\EventListener>
	 */
	public function getDeletedEventListeners() {
		return $this->getCache(ProjectEventListenerCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged event listeners.
	 *
	 * @return array<\wcf\data\event\listener\EventListener>
	 */
	public function getLoggedEventListeners() {
		return $this->getCache(ProjectEventListenerCacheBuilder::getInstance(), 'log');
	}
	
	/**
	 * Returns whether the event listener with the given ID was created in the current version.
	 * 
	 * @param integer $objectID
	 */
	public function isNewEventListener($objectID) {
		$logs = $this->getLoggedEventListeners();
		$versionID = $this->getCurrentVersion()->getVersionID() - 1;
		
		return !isset($logs[$objectID][$versionID]);
	}

	/**
	 * Returns the project's template listeners.
	 *
	 * @return array<\wcf\data\template\listener\TemplateListener>
	 */
	public function getTemplateListeners() {
		return $this->getCache(ProjectTemplateListenerCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of template listeners.
	 * 
	 * @return integer
	 */
	public function getTemplateListenerCount() {
		return $this->getCache(ProjectTemplateListenerCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted template listeners.
	 * 
	 * @return array<\wcf\data\template\listener\TemplateListener>
	 */
	public function getDeletedTemplateListeners() {
		return $this->getCache(ProjectTemplateListenerCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged template listeners.
	 *
	 * @return array<\wcf\data\template\listener\TemplateListener>
	 */
	public function getLoggedTemplateListeners() {
		return $this->getCache(ProjectTemplateListenerCacheBuilder::getInstance(), 'log');
	}
	
	/**
	 * Returns the project's cronjobs.
	 *
	 * @return array<\wcf\data\cronjob\Cronjob>
	 */
	public function getCronjobs() {
		return $this->getCache(ProjectCronjobCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of cronjobs.
	 * 
	 * @return integer
	 */
	public function getCronjobCount() {
		return $this->getCache(ProjectCronjobCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted cronjobs.
	 * 
	 * @return array<\wcf\data\cronjob\Cronjob>
	 */
	public function getDeletedCronjobs() {
		return $this->getCache(ProjectCronjobCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged cronjobs.
	 *
	 * @return array<\wcf\data\cronjob\Cronjob>
	 */
	public function getLoggedCronjobs() {
		return $this->getCache(ProjectCronjobCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's language variables.
	 * 
	 * @return array<\wcf\data\language\item\LanguageItem>
	 */
	public function getLanguageVariables() {
		return $this->getCache(ProjectLanguageItemCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of language variables.
	 * 
	 * @return integer
	 */
	public function getLanguageVariableCount() {
		return $this->getCache(ProjectLanguageItemCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted .
	 * 
	 * @return array<\wcf\data\language\item\LanguageItem>
	 */
	public function getDeletedLanguageVariables() {
		return $this->getCache(ProjectLanguageItemCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged .
	 *
	 * @return array<mixed>
	 */
	public function getLoggedLanguageVariables() {
		return $this->getCache(ProjectLanguageItemCacheBuilder::getInstance(), 'log');
	}
	
	/**
	 * Returns the flag icon for the given language.
	 * // TODO remove this method
	 * @param integer
	 * @return string
	 */
	public function getLanguageIcon($languageID) {
		$language = LanguageFactory::getInstance()->getLanguage($languageID);
		
		return '<img src="'.$language->getIconPath().'" alt="" title="'.$language.'" class="jsTooltip iconFlag" />';
	}

	/**
	 * Returns the project's files.
	 *
	 * @return array<mixed>
	 */
	public function getFiles() {
		return $this->getCache(ProjectFileCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of files.
	 * 
	 * @return integer
	 */
	public function getFileCount() {
		return $this->getCache(ProjectFileCacheBuilder::getInstance(), 'count');
	}

	/**
	 * Returns the project's templates.
	 *
	 * @return array<\wcf\data\template\Template>
	 */
	public function getTemplates() {
		return $this->getCache(ProjectTemplateCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of templates.
	 * 
	 * @return integer
	 */
	public function getTemplateCount() {
		return $this->getCache(ProjectTemplateCacheBuilder::getInstance(), 'count');
	}

	/**
	 * Returns the project's acp templates.
	 *
	 * @return array<\wcf\data\acp\template\ACPTemplate>
	 */
	public function getACPTemplates() {
		return $this->getCache(ProjectACPTemplateCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of ACP templates.
	 * 
	 * @return integer
	 */
	public function getACPTemplateCount() {
		return $this->getCache(ProjectACPTemplateCacheBuilder::getInstance(), 'count');
	}

	/**
	 * Returns the project's database tables.
	 *
	 * @return array<\wcf\data\project\database\ProjectDatabaseTable>
	 */
	public function getDatabaseTables() {
		return $this->getCache(ProjectDatabaseTableCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of database tables.
	 * 
	 * @return integer
	 */
	public function getDatabaseTableCount() {
		return $this->getCache(ProjectDatabaseTableCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted tables.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getDeletedDatabaseTables() {
		return $this->getCache(ProjectDatabaseTableCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged tables.
	 *
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getLoggedDatabaseTables() {
		return $this->getCache(ProjectDatabaseTableCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's database columns.
	 *
	 * @return array<\wcf\data\project\database\ProjectDatabaseColumn>
	 */
	public function getDatabaseColumns() {
		return $this->getCache(ProjectDatabaseColumnCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of database columns.
	 * 
	 * @return integer
	 */
	public function getDatabaseColumnCount() {
		return $this->getCache(ProjectDatabaseColumnCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted columns.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getDeletedDatabaseColumns() {
		return $this->getCache(ProjectDatabaseColumnCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged columns.
	 *
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getLoggedDatabaseColumns() {
		return $this->getCache(ProjectDatabaseColumnCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's database indices.
	 *
	 * @return array<\wcf\data\project\database\ProjectDatabaseIndex>
	 */
	public function getDatabaseIndices() {
		return $this->getCache(ProjectDatabaseIndexCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of database indices.
	 * 
	 * @return integer
	 */
	public function getDatabaseIndexCount() {
		return $this->getCache(ProjectDatabaseIndexCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted indices.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getDeletedDatabaseIndices() {
		return $this->getCache(ProjectDatabaseIndexCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged indices.
	 *
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getLoggedDatabaseIndices() {
		return $this->getCache(ProjectDatabaseIndexCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's database foreign keys.
	 *
	 * @return array<\wcf\data\project\database\ProjectDatabaseForeignKey>
	 */
	public function getDatabaseForeignKeys() {
		return $this->getCache(ProjectDatabaseForeignKeyCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of database foreign keys.
	 * 
	 * @return integer
	 */
	public function getDatabaseForeignKeyCount() {
		return $this->getCache(ProjectDatabaseForeignKeyCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted foreign keys.
	 * 
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getDeletedDatabaseForeignKeys() {
		return $this->getCache(ProjectDatabaseForeignKeyCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged foreign keys.
	 *
	 * @return array<\wcf\data\project\database\log\DatabaseLog>
	 */
	public function getLoggedDatabaseForeignKeys() {
		return $this->getCache(ProjectDatabaseForeignKeyCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's object types.
	 *
	 * @return array<\wcf\data\object\type\ObjectType>
	 */
	public function getObjectTypes() {
		return $this->getCache(ProjectObjectTypeCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of object types.
	 * 
	 * @return integer
	 */
	public function getObjectTypeCount() {
		return $this->getCache(ProjectObjectTypeCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted object types.
	 * 
	 * @return array<\wcf\data\object\type\ObjectType>
	 */
	public function getDeletedObjectTypes() {
		return $this->getCache(ProjectObjectTypeCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged object types.
	 *
	 * @return array<\wcf\data\object\type\ObjectType>
	 */
	public function getLoggedObjectTypes() {
		return $this->getCache(ProjectObjectTypeCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's object type definitions.
	 *
	 * @return array<\wcf\data\object\type\definition\ObjectTypeDefinition>
	 */
	public function getObjectTypeDefinitions() {
		return $this->getCache(ProjectObjectTypeDefinitionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of object type definitions.
	 * 
	 * @return integer
	 */
	public function getObjectTypeDefinitionCount() {
		return $this->getCache(ProjectObjectTypeDefinitionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted object type definitions.
	 * 
	 * @return array<\wcf\data\object\type\definition\ObjectTypeDefinition>
	 */
	public function getDeletedObjectTypeDefinitions() {
		return $this->getCache(ProjectObjectTypeDefinitionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged object type definitions.
	 *
	 * @return array<\wcf\data\object\type\definition\ObjectTypeDefinition>
	 */
	public function getLoggedObjectTypeDefinitions() {
		return $this->getCache(ProjectObjectTypeDefinitionCacheBuilder::getInstance(), 'log');
	}
	
	/**
	 * // TODO remove this method
	 * 
	 * @param int $definitionID
	 * @return array
	 */
	public function getObjectTypeDefinition($definitionID) {
		return ObjectTypeCache::getInstance()->getDefinition($definitionID);
	}

	/**
	 * Returns the project's core objects.
	 *
	 * @return array<\wcf\data\core\object\CoreObject>
	 */
	public function getCoreObjects() {
		return $this->getCache(ProjectCoreObjectCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of core objects.
	 * 
	 * @return integer
	 */
	public function getCoreObjectCount() {
		return $this->getCache(ProjectCoreObjectCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted core objects.
	 * 
	 * @return array<\wcf\data\core\object\CoreObject>
	 */
	public function getDeletedCoreObjects() {
		return $this->getCache(ProjectCoreObjectCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged core objects.
	 *
	 * @return array<\wcf\data\core\object\CoreObject>
	 */
	public function getLoggedCoreObjects() {
		return $this->getCache(ProjectCoreObjectCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's clipboard actions.
	 *
	 * @return array<\wcf\data\clipboard\ClipboardAction>
	 */
	public function getClipboardActions() {
		return $this->getCache(ProjectClipboardActionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of clipboard actions.
	 * 
	 * @return integer
	 */
	public function getClipboardActionCount() {
		return $this->getCache(ProjectClipboardActionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted clipboard actions.
	 * 
	 * @return array<\wcf\data\clipboard\ClipboardAction>
	 */
	public function getDeletedClipboardActions() {
		return $this->getCache(ProjectClipboardActionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged clipboard actions.
	 *
	 * @return array<\wcf\data\clipboard\ClipboardAction>
	 */
	public function getLoggedClipboardActions() {
		return $this->getCache(ProjectClipboardActionCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's user notification events.
	 *
	 * @return array<\wcf\data\user\notification\event\UserNotificationEvent>
	 */
	public function getUserNotificationEvents() {
		return $this->getCache(ProjectUserNotificationEventCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of user notification events.
	 * 
	 * @return integer
	 */
	public function getUserNotificationEventCount() {
		return $this->getCache(ProjectUserNotificationEventCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted notification events.
	 * 
	 * @return array<\wcf\data\user\notification\event\UserNotificationEvent>
	 */
	public function getDeletedUserNotificationEvents() {
		return $this->getCache(ProjectUserNotificationEventCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged notification events.
	 *
	 * @return array<\wcf\data\user\notification\event\UserNotificationEvent>
	 */
	public function getLoggedUserNotificationEvents() {
		return $this->getCache(ProjectUserNotificationEventCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's acp search providers.
	 *
	 * @return array<\wcf\data\acp\search\provider\ACPSearchProvider>
	 */
	public function getACPSearchProviders() {
		return $this->getCache(ProjectACPSearchProviderCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of ACP search providers.
	 * 
	 * @return integer
	 */
	public function getACPSearchProviderCount() {
		return $this->getCache(ProjectACPSearchProviderCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted ACP search providers.
	 * 
	 * @return array<\wcf\data\acp\search\provider\ACPSearchProvider>
	 */
	public function getDeletedACPSearchProviders() {
		return $this->getCache(ProjectACPSearchProviderCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged ACP search providers.
	 *
	 * @return array<\wcf\data\acp\search\provider\ACPSearchProvider>
	 */
	public function getLoggedACPSearchProviders() {
		return $this->getCache(ProjectACPSearchProviderCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's styles.
	 *
	 * @return array<\wcf\data\style\Style>
	 */
	public function getStyles() {
		return $this->getCache(ProjectStyleCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of styles.
	 * 
	 * @return integer
	 */
	public function getStyleCount() {
		return $this->getCache(ProjectStyleCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted styles.
	 * 
	 * @return array<\wcf\data\style\Style>
	 */
	public function getDeletedStyles() {
		return $this->getCache(ProjectStyleCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged styles.
	 *
	 * @return array<\wcf\data\style\Style>
	 */
	public function getLoggedStyles() {
		return $this->getCache(ProjectStyleCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's BBCodes.
	 *
	 * @return array<\wcf\data\bbcode\BBCode>
	 */
	public function getBBCodes() {
		return $this->getCache(ProjectBBCodeCacheBuilder::getInstance(), 'active');
	}

	/**
	 * Returns the number of BBCodes.
	 * 
	 * @return integer
	 */
	public function getBBCodeCount() {
		return $this->getCache(ProjectBBCodeCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted BBCodes.
	 * 
	 * @return array<\wcf\data\bbcode\BBCode>
	 */
	public function getDeletedBBCodes() {
		return $this->getCache(ProjectBBCodeCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged BBCodes.
	 *
	 * @return array<\wcf\data\bbcode\BBCode>
	 */
	public function getLoggedBBCodes() {
		return $this->getCache(ProjectBBCodeCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's smilies.
	 *
	 * @return array<\wcf\data\smiley\Smiley>
	 */
	public function getSmilies() {
		return $this->getCache(ProjectSmileyCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of smilies.
	 * 
	 * @return integer
	 */
	public function getSmileyCount() {
		return $this->getCache(ProjectSmileyCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted smilies.
	 * 
	 * @return array<\wcf\data\smiley\Smiley>
	 */
	public function getDeletedSmilies() {
		return $this->getCache(ProjectSmileyCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged smilies.
	 *
	 * @return array<\wcf\data\smiley\Smiley>
	 */
	public function getLoggedSmilies() {
		return $this->getCache(ProjectSmileyCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's dashboard boxes.
	 *
	 * @return array<\wcf\data\dashboard\box\DashboardBox>
	 */
	public function getDashboardBoxes() {
		return $this->getCache(ProjectDashboardBoxCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of dashboard boxes.
	 * 
	 * @return integer
	 */
	public function getDashboardBoxCount() {
		return $this->getCache(ProjectDashboardBoxCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted dashboard boxes.
	 * 
	 * @return array<\wcf\data\dashboard\box\DashboardBox>
	 */
	public function getDeletedDashboardBoxes() {
		return $this->getCache(ProjectDashboardBoxCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged dashboard boxes.
	 *
	 * @return array<\wcf\data\dashboard\box\DashboardBox>
	 */
	public function getLoggedDashboardBoxes() {
		return $this->getCache(ProjectDashboardBoxCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's sitemaps.
	 *
	 * @return array<\wcf\data\dashboard\sitemap\Sitemap>
	 */
	public function getSitemaps() {
		return $this->getCache(ProjectSitemapCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of sitemaps.
	 * 
	 * @return integer
	 */
	public function getSitemapCount() {
		return $this->getCache(ProjectSitemapCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted sitemaps.
	 * 
	 * @return array<\wcf\data\dashboard\sitemap\Sitemap>
	 */
	public function getDeletedSitemaps() {
		return $this->getCache(ProjectSitemapCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged sitemaps.
	 *
	 * @return array<\wcf\data\dashboard\sitemap\Sitemap>
	 */
	public function getLoggedSitemaps() {
		return $this->getCache(ProjectSitemapCacheBuilder::getInstance(), 'log');
	}

	/**
	 * Returns the project's Instructions.
	 *
	 * @return array<\wcf\data\project\instruction\Instruction>
	 */
	public function getInstructions() {
		return $this->getCache(ProjectInstructionCacheBuilder::getInstance(), 'active');
	}
	
	/**
	 * Returns the number of Instructions.
	 * 
	 * @return integer
	 */
	public function getInstructionCount() {
		return $this->getCache(ProjectInstructionCacheBuilder::getInstance(), 'count');
	}
	
	/**
	 * Returns the project's deleted Instructions.
	 * 
	 * @return array<\wcf\data\project\instruction\Instruction>
	 */
	public function getDeletedInstructions() {
		return $this->getCache(ProjectInstructionCacheBuilder::getInstance(), 'deleted');
	}

	/**
	 * Returns the project's logged Instructions.
	 *
	 * @return array<\wcf\data\project\instruction\Instruction>
	 */
	public function getLoggedInstructions() {
		return $this->getCache(ProjectInstructionCacheBuilder::getInstance(), 'log');
	}
}