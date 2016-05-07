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

use wcf\data\AbstractDatabaseObjectAction;
use wcf\util\ClassUtil;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\data\language\item\LanguageItemAction;
use wcf\acp\form\ProjectOptionAddForm;
use wcf\data\object\type\ObjectTypeCache;
use wcf\util\ProjectUtil;
use wcf\util\StringUtil;
use wcf\data\package\Package;
use wcf\data\package\PackageCache;
use wcf\system\cache\builder\ProjectLanguageItemCacheBuilder;
use wcf\data\language\item\LanguageItemList;
use wcf\data\project\language\item\ProjectLanguageItemAction;
use wcf\system\cache\builder\LanguageCacheBuilder;

/**
 * Abstract implementation of the database object action for options.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
abstract class AbstractProjectOptionAction extends AbstractProjectDatabaseObjectAction {
	/**
	 * @var string
	 */
	public static $optionLanguageItemPrefix;
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getLanguageItemNames()
	 */
	protected function getLanguageItemNames(\wcf\data\DatabaseObject $object) {
		return array(
			'name' => static::$optionLanguageItemPrefix . '.' . $object->optionName,
			'description' => static::$optionLanguageItemPrefix . '.' . $object->optionName . '.description'
		);
	}
	
	/**
	 * @see \wcf\data\project\AbstractProjectDatabaseObjectAction::getNewLanguageItemNames()
	 */
	protected function getNewLanguageItemNames(array $parameters, array $types) {
		if(isset($parameters['optionName'])) {
			return array(
				'name' => static::$optionLanguageItemPrefix . '.' . $parameters['optionName'],
				'description' => static::$optionLanguageItemPrefix . '.' . $parameters['optionName'] . '.description'
			);
		}
		
		return array();
	}
}
