<?php
namespace wcf\acp\form;

/**
 * Edit form for ACP menu items.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACPMenuItemEditForm extends ProjectACPMenuItemAddForm {
	/**
	 * @see \wcf\form\AbstractForm::$action
	 */
	public $action = 'edit';
}
