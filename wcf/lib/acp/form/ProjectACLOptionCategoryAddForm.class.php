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

use wcf\system\cache\builder\ACPMenuCacheBuilder;

/**
 * Implementation of the abstract option category form for ACL option categories.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACLOptionCategoryAddForm extends AbstractProjectOptionCategoryForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionCategoryForm::$type
	 */
	protected static $type = 'option';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionCategoryForm::$languageVariablePrefix
	 */
	public static $languageVariablePrefix = 'wcf.acp.option.category';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectOptionCategoryForm::$languageVariableCategory
	 */
	public static $languageVariableCategory = 'wcf.acp.option';
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\option\category\OptionCategory';
	
	/**
	 * @see \wcf\form\AbstractForm::saved()
	 */
	protected function saved() {
		// each top level option category is also an acp menu item
		if(empty($this->parentCategoryName)) ACPMenuCacheBuilder::getInstance()->reset();
		
		parent::saved();
	}
}