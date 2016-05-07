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

use wcf\system\SingletonFactory;
use wcf\system\event\EventHandler;
use wcf\util\StringUtil;

/**
 * Provides information about tables and their corresponding log tables. 
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDataTables extends SingletonFactory {
	/**
	 * Array structure: name of the original table => name of the log table
	 *
	 * @var array<string>
	 */
	protected $tables = array(
		// This table has to be the first in the array, because
		// of foreign key constraints referencing the 'package' or
		// 'project_package_log' table
	
		'package' => 'project_package_log',
	
		// These tables do not have to be in any specific order
		
		'acp_menu_item' => 'project_acp_menu_item_log',
		'acp_search_provider' => 'project_acp_search_provider_log',
		'acp_template' => 'project_acp_template_log',
		'core_object' => 'project_core_object_log',
		'cronjob' => 'project_cronjob_log',
		'dashboard_box' => 'project_dashboard_box_log',
		'event_listener' => 'project_event_listener_log',
		'language_item' => 'project_language_item_log',
		'package_exclusion' => 'project_exclusion_log',
		'package_installation_file_log' => 'project_file_log',
		'package_installation_sql_log' => 'project_sql_log',
		'package_optional' => 'project_optional_log',
		'page_menu_item' => 'project_page_menu_item_log',
		'sitemap' => 'project_sitemap_log',
		'smiley' => 'project_smiley_log',
		'template_listener' => 'project_template_listener_log',
		'template' => 'project_template_log',
		'user_menu_item' => 'project_user_menu_item_log',
		'user_notification_event' => 'project_user_notification_event_log',
		'user_profile_menu_item' => 'project_user_profile_menu_item_log',
		
		'package_instruction' => 'project_instruction_log',
		
		// The following tables have to be in this order
		// because of foreign key constraints between them
		
		'acl_option_category' => 'project_acl_option_category_log',
		'acl_option' => 'project_acl_option_log',
		
		'bbcode' => 'project_bbcode_log',
		'bbcode_attribute' => 'project_bbcode_attribute_log',
		
		'clipboard_action' => 'project_clipboard_action_log',
		'clipboard_page' => 'project_clipboard_page_log',
		
		'object_type_definition' => 'project_object_type_definition_log',
		'object_type' => 'project_object_type_log',
		
		'option_category' => 'project_option_category_log',
		'option' => 'project_option_log',
		
		'style' => 'project_style_log',
		'style_variable_value' => 'project_style_variable_value_log',
		
		'user_group_option_category' => 'project_user_group_option_category_log',
		'user_group_option' => 'project_user_group_option_log',
		
		'user_option_category' => 'project_user_option_category_log',
		'user_option' => 'project_user_option_log'
	);
	
	/**
	 * Returns all tables from the WCF and other basic packages
	 * which hold data of other packages. 
	 * 
	 * @return array<string>
	 */
	public function getTables() {
		EventHandler::getInstance()->fireAction($this, 'getTables', $this->tables);
		
		return $this->tables;
	}
	
	/**
	 * Returns the name of the log table which logs the data from the given table
	 * or null if the table with this name is not logged.
	 * 
	 * @param string $tableName
	 */
	public function getLogTableName($tableName) {
		$prefix = "wcf" . WCF_N . "_";
		if(StringUtil::startsWith($tableName, $prefix)) {
			$tableName = mb_substr($tableName, mb_strlen($prefix));
		}
		
		$tables = $this->getTables();
		
		if(isset($tables[$tableName])) {
			return $tables[$tableName];
		}

		return null;
	}
}
