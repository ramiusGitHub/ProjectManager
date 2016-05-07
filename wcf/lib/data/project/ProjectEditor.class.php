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

use wcf\system\WCF;
use wcf\system\language\LanguageFactory;
use wcf\system\application\ApplicationHandler;
use wcf\data\package\Package;
use wcf\data\package\PackageCache;
use wcf\data\project\sql\ProjectSqlLogList;
use wcf\system\cache\builder\ApplicationCacheBuilder;
use wcf\util\StringUtil;
use wcf\util\FileUtil;
use wcf\util\ClassUtil;
use wcf\data\project\acp\template\ProjectACPTemplateLogCache;
use wcf\data\project\template\ProjectTemplateLogCache;
use wcf\data\application\Application;
use wcf\system\Regex;
use wcf\data\package\PackageEditor;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\project\database\ProjectDatabaseAccess;
use wcf\system\cache\builder\PackageCacheBuilder;
use wcf\data\project\version\ProjectVersionAction;
use wcf\util\DirectoryUtil;
use wcf\system\io\TarWriter;
use wcf\data\package\PackageAction;
use wcf\system\io\Tar;
use wcf\data\project\database\ProjectDatabaseCache;
use wcf\system\cache\builder\ProjectVersionCacheBuilder;
use wcf\util\ProjectUtil;
use wcf\data\project\database\ProjectDatabaseTable;
use wcf\data\project\database\log\ProjectDatabaseLogCache;
use wcf\data\project\version\ProjectVersionFileArchive;
use wcf\data\project\database\log\ProjectDatabaseLogAction;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit projects.
 * Decorates a PackageEditor object.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectEditor extends DatabaseObjectDecorator {
	/**
	 * @see \wcf\data\DatabaseObjectDecorator
	 */
	protected static $baseClass = '\wcf\data\package\PackageEditor';
	
	/**
	 * @var \wcf\data\project\Project
	 */
	protected $project;
	
	/**
	 * Creates a new instance of the ProjectEditor class.
	 * 
	 * @param \wcf\data\project\Project	$project
	 */
	public function __construct(\wcf\data\project\Project $project) {
		$this->project = $project;
		$this->object = new PackageEditor($project->getDecoratedObject());
	}
	
	/**
	 * Removes the package from the active environment like an uninstallation.
	 * Any version of the package can be activated afterwards.
	 * 
	 * This function performs two steps:
	 * 1. Create log of current version
	 * 2. Delete current version
	 */
	public function deactivateCurrentVersion() {
		// Create log of current version
		$this->createVersionLog($this->project->getCurrentVersion(), $this->project->getCurrentVersion()->getVersionString());
		
		// Delete current version
		$this->deleteCurrentVersion();
	}
	
	/**
	 * Activates a version of the package from the version log like an installation.
	 * 
	 * This function performs two steps:
	 * 1. Recreate target version from log
	 * 2. Delete target version from log
	 * 
	 * @param \wcf\data\project\version\ProjectVersion	$targetVersion
	 */
	public function activateVersion(\wcf\data\project\version\ProjectVersion $targetVersion) {
		// Recreate target version from log
		$this->recreateVersion($targetVersion);
		
		// Delete target version from log
		$this->deleteVersionLog($targetVersion);
	}
	
	/**
	 * Switches from the current to the target version.
	 * 
	 * This function performs four steps:
	 * 1. Create log of current version
	 * 2. Delete current version
	 * 3. Recreate target version from log
	 * 4. Delete target version from log
	 * 
	 * @param \wcf\data\project\version\ProjectVersion	$targetVersion
	 */
	public function switchVersion(\wcf\data\project\version\ProjectVersion $targetVersion) {
		// Deactivate current version
		$this->deactivateCurrentVersion(false);
		
		// Activate target version
		$this->activateVersion($targetVersion, false);
	}
	
	/**
	 * Creates a new log of a source version of this project and assignes
	 * the given target version as version number.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion $sourceVersion
	 * @param string $targetVersionString
	 */
	public function createVersionLog(\wcf\data\project\version\ProjectVersion $sourceVersion, $targetVersionString) {
		// Begin transaction
		WCF::getDB()->beginTransaction();
		
		// Check if target version already exists
		// otherweise find largest version which is smaller than the target version
		$smallestVersion = null;
		$exists = false;
		foreach($this->project->getVersions() as $version) {
			// Check if version already exists
			if($targetVersionString == $version->getVersionString()) {
				$versionID = $version->getVersionID();
				$targetVersion = $version;
				$exists = true;
				break;
			}
			
			// Find largest version smaller than the target version
			if(Package::compareVersion($targetVersionString, $version->getVersionString(), '>')) {
				if($smallestVersion === null || Package::compareVersion($version->getVersionString(), $smallestVersion->getVersionString(), '>')) {
					$smallestVersion = $version;
				}
			}
		}
		
		// If version with target version number does not exist, create new version
		if($exists === false) {
			// Increment all versionIDs larger than the versionID of the smallest version
			$sql = "UPDATE		wcf".WCF_N."_project_version_log
				SET		versionID = versionID + 1
				WHERE		packageID = ?
				AND		versionID > ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->packageID,
				$smallestVersion->versionID
			));
			
			// Version ID of the new version
			$versionID = $smallestVersion->versionID + 1;
			
			// Create entry in project_version_log table
			$action = new ProjectVersionAction(array(), 'create', array('data' => array(
				'packageID' => $this->packageID,
				'versionID' => $versionID,
				'packageVersion' => $targetVersionString
			)));
			$result = $action->executeAction();
			
			// Get target version object
			$targetVersion = $result['returnValues'];
		}
		
		// Copy database entries from source version
		foreach(ProjectDataTables::getInstance()->getTables() as $wcfTable => $logTable) {			
			// Source version is currently active version
			// -> load data from regular tables
			if($sourceVersion->isCurrentVersion()) {
				switch($wcfTable) {
					case 'bbcode_attribute':
						$sql = "SELECT		attribute.*
							FROM		wcf" . WCF_N . "_bbcode_attribute AS attribute
							LEFT JOIN	wcf" . WCF_N . "_bbcode AS bbcode
							ON		attribute.bbcodeID = bbcode.bbcodeID
							WHERE		bbcode.packageID = ?";
						break;
					case 'style_variable_value':
						$sql = "SELECT		value.*
							FROM		wcf" . WCF_N . "_style_variable_value AS value
							LEFT JOIN	wcf" . WCF_N . "_style AS style
							ON		value.styleID = style.styleID
							WHERE		style.packageID = ?";
						break;
					default:
						$sql = "SELECT	*
							FROM	wcf" . WCF_N . "_" . $wcfTable . "
							WHERE	packageID = ?";
				}

				$fetchStatement = WCF::getDB()->prepareStatement($sql);
				$fetchStatement->execute(array(
					$this->packageID
				));
			}
			// Source version is not active
			// -> load data from log tables
			else {
				$sql = "SELECT	*
					FROM	wcf" . WCF_N . "_" . $logTable . "
					WHERE	packageID = ?
					AND	versionID = ?";				
				$fetchStatement = WCF::getDB()->prepareStatement($sql);
				$fetchStatement->execute(array(
					$this->packageID,
					$sourceVersion->versionID
				));
			}

			// Get columns
			$columnNames = array_keys(ProjectDatabaseCache::getInstance()->getTable('wcf' . WCF_N . "_" . $wcfTable)->getColumns());
			
			// Prepare insert statement
			switch($wcfTable) {
				case 'bbcode_attribute':
				case 'style_variable_value':
					$sql = "INSERT INTO	wcf" . WCF_N . "_" . $logTable . "
								(" . implode(',', $columnNames) . ",packageID,versionID)
						VALUES		(:" . implode(',:', $columnNames) . ",:packageID,:versionID)";
					break;
				default:
					$sql = "INSERT INTO	wcf" . WCF_N . "_" . $logTable . "
								(" . implode(',', $columnNames) . ",versionID)
						VALUES		(:" . implode(',:', $columnNames) . ",:versionID)";
			}
			$insertStatement = WCF::getDB()->prepareStatement($sql);
			
			// Fetch and insert data
			while($row = $fetchStatement->fetchArray()) {
				// Prepare data
				switch($wcfTable) {
					case 'package':
						$row['packageVersion'] = $targetVersionString;
						break;
				}
				$row['packageID'] = $this->packageID;
				$row['versionID'] = $versionID;
				
				// Insert data
				$insertStatement->execute($row);
			}
		}
		
		// Set SQL data of database modifications from source version
		// Prepare update statement
		$sql = "UPDATE	wcf" . WCF_N . "_project_sql_log
			SET	sqlData = :sqlData
			WHERE	sqlLogID = :sqlLogID
			AND	versionID = :versionID";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		// Source version is currently active version
		// -> load modification data from active version
		if($sourceVersion->isCurrentVersion()) {
			// Iterate over project columns
			foreach(ProjectDatabaseCache::getInstance()->getAllPackageColumns($this->packageID) as $columns) {
				foreach($columns as $column) {
					$columnLog = ProjectDatabaseLogCache::getInstance()->getColumnLog($column->getTable()->getName(), $column->getName());
					
					$statement->execute(array(
						'sqlData' => serialize($column->toArray()),
						'sqlLogID' => $columnLog->sqlLogID,
						'versionID' => $versionID
					));
				}
			}
			
			// Iterate over project indices
			foreach(ProjectDatabaseCache::getInstance()->getAllPackageIndices($this->packageID) as $indices) {
				foreach($indices as $index) {
					$indexLog = ProjectDatabaseLogCache::getInstance()->getIndexLog($index->getTable()->getName(), $index->getName());
					
					$statement->execute(array(
						'sqlData' => serialize($index->toArray()),
						'sqlLogID' => $indexLog->sqlLogID,
						'versionID' => $versionID
					));
				}
			}
			
			// Iterate over project foreign keys
			foreach(ProjectDatabaseCache::getInstance()->getAllPackageForeignKeys($this->packageID) as $foreignKeys) {
				foreach($foreignKeys as $foreignKey) {
					$foreignKeyLog = ProjectDatabaseLogCache::getInstance()->getForeignKeyLog($foreignKey->getTable()->getName(), $foreignKey->getName());
					
					$statement->execute(array(
						'sqlData' => serialize($foreignKey->toArray()),
						'sqlLogID' => $foreignKeyLog->sqlLogID,
						'versionID' => $versionID
					));
				}
			}
		}
		// Source version is not active
		// -> load modification data from log table
		else {
			$sql = "SELECT	*
				FROM	wcf" . WCF_N . "_project_sql_log
				WHERE	packageID = ?
				AND	versionID = ?";				
			$fetchStatement = WCF::getDB()->prepareStatement($sql);
			$fetchStatement->execute(array(
				$this->packageID,
				$sourceVersion->versionID
			));
			
			while($row = $fetchStatement->fetchArray()) {
				$statement->execute(array(
					'sqlData' => $row['sqlData'],
					'sqlLogID' => $row['sqlLogID'],
					'versionID' => $versionID
				));
			}
		}
		
		// Create archive of files from source version
		ProjectVersionFileArchive::create($sourceVersion, $targetVersion);
		
		// Commit transaction
		WCF::getDB()->commitTransaction();
		
		// Clear caches
		ProjectVersionCacheBuilder::getInstance()->reset(array('packageID' => $this->packageID));
	}
	
	/**
	 * Deletes the currently active version.
	 * Note: This is more or less an uninstallation of the package.
	 */
	public function deleteCurrentVersion() {
		// Deactivate foreign key checks
		WCF::getDB()->prepareStatement("SET FOREIGN_KEY_CHECKS = 0")->execute();
		
		// Undo database modifications
		// Iterate over project tables
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageTables($this->packageID) as $table) {
			WCF::getDB()->getEditor()->dropTable($table->getName());
		}
		
		// Iterate over project columns
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageColumns($this->packageID) as $columns) {
			foreach($columns as $column) {
				// Check if column was added to table of other package
				if($column->getTable()->getPackageID() != $column->getPackageID()) {
					WCF::getDB()->getEditor()->dropColumn($column->getTable()->getName(), $column->getName());
				}
			}
		}
		
		// Iterate over project indices
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageIndices($this->packageID) as $indices) {
			foreach($indices as $index) {
				// Check if index was added to at least one column of this package
				foreach($index->getTable()->getColumns() as $column) {
					if($index->getPackageID() == $column->getPackageID()) {
						// Because the column of this package was
						// dropped beforehand, the index was dropped
						// implicitly.
						continue(2);
					}
				}
				
				// The index was completely defined on columns
				// of other packages. Drop it explicitly.
				WCF::getDB()->getEditor()->dropIndex($index->getTable()->getName(), $index->getName());
			}
		}
		
		// Iterate over project foreign keys
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageForeignKeys($this->packageID) as $foreignKeys) {
			foreach($foreignKeys as $foreignKey) {
				// Check if foreign key was added to table of other package
				if($foreignKey->getTable()->getPackageID() != $foreignKey->getPackageID()) {
					WCF::getDB()->getEditor()->dropForeignKey($foreignKey->getTable()->getName(), $foreignKey->getName());
				}
			}
		}
		
		// Delete database entries
		foreach(ProjectDataTables::getInstance()->getTables() as $wcfTable => $logTable) {
			// Prepare statement
			switch($wcfTable) {
				case 'bbcode_attribute':
					$sql = "DELETE		attribute
						FROM		wcf" . WCF_N . "_bbcode_attribute AS attribute
						LEFT JOIN	wcf" . WCF_N . "_bbcode AS bbcode
						ON		attribute.bbcodeID = bbcode.bbcodeID
						WHERE		bbcode.packageID = ?";
					break;
				case 'style_variable_value':
					$sql = "DELETE		value
						FROM		wcf" . WCF_N . "_style_variable_value AS value
						LEFT JOIN	wcf" . WCF_N . "_style AS style
						ON		value.styleID = style.styleID
						WHERE		style.packageID = ?";
					break;
				default:
					$sql = "DELETE FROM	wcf" . WCF_N . "_" . $wcfTable . "
						WHERE		packageID = ?";
			}
			
			$deleteStatement = WCF::getDB()->prepareStatement($sql);
			$deleteStatement->execute(array(
				$this->packageID
			));
		}
		
		// Reactivate foreign key checks
		WCF::getDB()->prepareStatement("SET FOREIGN_KEY_CHECKS = 1")->execute();
		
		// Delete files
		foreach($this->project->getFiles() as $fileLog) {
			// Delete file in project directory
			$fileLog->deleteProjectFile();
			
			// Delete file in application directory
			$fileLog->deleteApplicationFile();
		}
		
		// Delete templates
		foreach($this->project->getTemplates() as $template) {
			// Delete template in project directory
			unlink($this->project->getDirectory() . $template->application . '/templates/' . $template->templateGroupFolderName . $template->templateName . '.tpl');
			
			// Delete template in application directory
			unlink($template->getPath());
		}
		
		// Delete ACP templates
		foreach($this->project->getACPTemplates() as $template) {
			// Delete ACP template in project directory
			unlink($this->project->getDirectory() . $template->application . '/acp/templates/' . $template->templateGroupFolderName . $template->templateName . '.tpl');
			
			// Delete ACP template in application directory
			unlink(Application::getDirectory($template->application) . 'acp/templates/' . $template->templateName . '.tpl');
		}
				
		// Delete empty folders
		ProjectUtil::deleteEmptyFolders($this->project->getDirectory());
		
		// Clear package cache
		PackageCacheBuilder::getInstance()->reset();
		
		// Clear project data caches
		$this->project->clearCaches();
	}
	
	/**
	 * Recreates a version of the project from the log.
	 * Current version should be deleted beforehand with deleteCurrentVersion().
	 * Note: This is more or less an installation of the package.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion	$version
	 */
	public function recreateVersion(\wcf\data\project\version\ProjectVersion $version) {
		// Begin transaction
		WCF::getDB()->beginTransaction();
		
		// Create database entries
		foreach(ProjectDataTables::getInstance()->getTables() as $wcfTable => $logTable) {
			// Get column names
			$columnNames = ProjectDatabaseCache::getInstance()->getTable('wcf' . WCF_N . "_" . $wcfTable)->getColumnNames();
			$logColumnName = ProjectDatabaseCache::getInstance()->getTable('wcf' . WCF_N . "_" . $logTable)->getColumnNames();
			
			// Load data from log
			$data = array();
			$sql = "SELECT	*
				FROM	wcf" . WCF_N . "_" . $logTable . "
				WHERE	packageID = ?
				AND	versionID = ?";
			if(in_array("isDeleted", $logColumnName)) {
				$sql .= " AND isDeleted = 0";
			}
			
			$fetchStatement = WCF::getDB()->prepareStatement($sql);
			$fetchStatement->execute(array(
				$this->packageID,
				$version->getVersionID()
			));
			
			// Prepare insert statement
			$sql = "INSERT INTO	wcf" . WCF_N . "_" . $wcfTable . "
						(" . implode(',', $columnNames) . ")
				VALUES		(:" . implode(',:', $columnNames) . ")";
			$insertStatement = WCF::getDB()->prepareStatement($sql);
			
			// Fetch and insert data
			while($row = $fetchStatement->fetchArray()) {
				// Prepare data
				foreach($row as $index => $value) {
					if(!in_array($index, $columnNames)) {
						unset($row[$index]);
					}
				}
					
				// Insert data
				$insertStatement->execute($row);
			}
		}
		
		// Commit transaction
		WCF::getDB()->commitTransaction();
		
		// Redo database modifications
		$sql = "SELECT	*
			FROM	wcf" . WCF_N . "_project_sql_log
			WHERE	packageID = ?
			AND	versionID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->packageID,
			$version->getVersionID()
		));
		
		// Prepare insert statement
		$sql = "INSERT INTO	wcf" . WCF_N . "_package_installation_sql_log
					(sqlLogID, packageID, sqlTable, sqlColumn, sqlIndex)
			VALUES		(:sqlLogID, :packageID, :sqlTable, :sqlColumn, :sqlIndex)";
		$insertStatement = WCF::getDB()->prepareStatement($sql);
		
		// Group log entries
		$tables = array();
		$columns = array();
		$indices = array();
		$foreignKeys = array();
		while($entry = $statement->fetchObject("\wcf\data\project\database\log\DatabaseLog")) {
			if($entry->isTableLog()) {
				$tables[$entry->sqlTable] = $entry;
			} elseif($entry->isColumnLog()) {
				$columns[$entry->sqlTable][$entry->sqlColumn] = $entry;
			} elseif($entry->isIndexLog()) {
				$indices[] = $entry;
			} else {
				$foreignKeys[] = $entry;
			}
		}
		
		// Add tables
		foreach($tables as $tableName => $entry) {
			$columnData = array();
			foreach($columns[$tableName] as $columnName => $columnLog) {
				$columnData[] = array(
					'name' => $columnLog->sqlColumn,
					'data' => unserialize($columnLog->sqlData)
				);
			}
			
			WCF::getDB()->getEditor()->createTable($tableName, $columnData, array());
		}
		
		// Add columns
		foreach($columns as $tableName => $columnEntries) {
			if(!isset($tables[$tableName])) {
				foreach($columnEntries as $columnName => $entry) {
					$sqlData = unserialize($entry->sqlData);
					
					WCF::getDB()->getEditor()->addColumn($entry->sqlTable, $entry->sqlColumn, $sqlData);
				}
			}
		}
		
		// Add indices
		foreach($indices as $entry) {
			$sqlData = unserialize($entry->sqlData);
			
			// Skip primary/unique single column indices on own tables
			if(isset($tables[$entry->sqlTable])) {
				if($sqlData['data']['type'] == 'PRIMARY' || $sqlData['data']['type'] == 'UNIQUE') {
					if(mb_strpos($sqlData['data']['columns'], 0) !== false) {
						continue;
					}
				}
			}
			
			WCF::getDB()->getEditor()->addIndex($entry->sqlTable, $entry->sqlIndex, $sqlData['data']);
		}
		
		// Add foreign keys
		foreach($foreignKeys as $entry) {
			$sqlData = unserialize($entry->sqlData);
			
			WCF::getDB()->getEditor()->addForeignKey($entry->sqlTable, $entry->sqlIndex, $sqlData['data']);
		}
		
		// Recreate files, templates and ACP templates from Tar archive
		$archive = new ProjectVersionFileArchive($version);
		$archive->extract($this->project->getDirectory());
		
		// Clear package cache
		PackageCacheBuilder::getInstance()->reset();
		
		// Clear project data caches
		$this->project->clearCaches();
	}
	
	/**
	 * Deletes the given version from the log.
	 * 
	 * @param \wcf\data\project\version\ProjectVersion	$version
	 */
	public function deleteVersionLog(\wcf\data\project\version\ProjectVersion $version) {
		// Delete database entries (which implicitly deletes the database modifications
		// which were saved in the project_sql_log table's sqlData column)
		foreach(ProjectDataTables::getInstance()->getTables() as $wcfTable => $logTable) {
			$sql = "DELETE FROM	wcf" . WCF_N . "_" . $logTable . "
				WHERE		packageID = ?
				AND		versionID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->packageID,
				$version->getVersionID()
			));
		}
		
		// Delete archive (files, templates and ACP templates)
		$archive = new ProjectVersionFileArchive($version);
		$archive->delete();
		
		// Clear caches
		ProjectVersionCacheBuilder::getInstance()->reset(array('packageID' => $this->packageID));
	}
	
	public function changeStatus() {
		if($this->isActive()) {
			$this->disable();
		} else {
			$this->enable();
		}
	}
	
	/**
	 * Enables a package to become an active project.
	 * The following steps are performed:
	 * 
	 * 1. Missing entries in the sql_log table are added
	 * 2. Empty default values in the option table are filled with the current value
	 * 3. The 'isActiveProject' flag is set to true
	 */
	public function enable() {
		// Begin transaction
		WCF::getDB()->beginTransaction();
		
		// Add missing column, index and foreign key entries to the sql_log table
		// Iterate over package columns
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageColumns($this->packageID) as $tableName => $columns) {
			foreach($columns as $column) {
				// Check if log entry exists
				if(ProjectDatabaseLogCache::getInstance()->getColumnLog($tableName, $column->getName()) === null) {
					// Otherwise insert log entry
					$action = new DatabaseLogAction(array(), 'create', array('data' => array(
						'packageID' => $this->packageID,
						'sqlTable' => $tableName,
						'sqlColumn' => $column->getName(),
						'sqlIndex' => ''
					)));
					$action->executeAction();
				}
			}
		}
		
		// Iterate over package indices
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageIndices($this->packageID) as $tableName => $indices) {
			foreach($indices as $index) {
				// Check if log entry exists
				if(ProjectDatabaseLogCache::getInstance()->getIndexLog($tableName, $index->getName()) === null) {
					// Otherwise insert log entry
					$action = new DatabaseLogAction(array(), 'create', array('data' => array(
						'packageID' => $this->packageID,
						'sqlTable' => $tableName,
						'sqlColumn' => '',
						'sqlIndex' => $index->getName()
					)));
					$action->executeAction();
				}
			}
		}
		
		// Iterate over package foreign keys
		foreach(ProjectDatabaseCache::getInstance()->getAllPackageForeignKeys($this->packageID) as $tableName => $foreignKeys) {
			foreach($foreignKeys as $foreignKey) {
				// Check if log entry exists
				if(ProjectDatabaseLogCache::getInstance()->getForeignKeyLog($tableName, $foreignKey->getName()) === null) {
					// Otherwise insert log entry
					$action = new DatabaseLogAction(array(), 'create', array('data' => array(
						'packageID' => $this->packageID,
						'sqlTable' => $tableName,
						'sqlColumn' => '',
						'sqlIndex' => $foreignKey->getName()
					)));
					$action->executeAction();
				}
			}
		}
		
		// Update option default value column
		$sql = "UPDATE	wcf".WCF_N."_option
			SET	defaultValue = optionValue
			WHERE	defaultValue IS NULL
			AND	packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->packageID));
		
		// Update package status
		$sql = "UPDATE	wcf".WCF_N."_package
			SET	isActiveProject = 1
			WHERE	packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->packageID));
		
		// Commit transaction
		WCF::getDB()->commitTransaction();
		
		// Reset cache
		PackageCacheBuilder::getInstance()->reset();
	}
		
	/**
	 * Disables this active project by setting the 'isActiveProject' flag to false.
	 */
	public function disable() {
		// Update package status
		$sql = "UPDATE	wcf".WCF_N."_package
			SET	isActiveProject = 0
			WHERE	packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->packageID));
		
		// Reset cache
		PackageCacheBuilder::getInstance()->reset();
	}
}
