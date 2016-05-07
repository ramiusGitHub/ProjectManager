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
use wcf\data\project\ProjectAction;
use wcf\system\language\I18nHandler;
use wcf\system\dashboard\box\IDashboardBox;

/**
 * Implementation of the database object form for dashboard boxes.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectDashboardBoxAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\dashboard\box\ProjectDashboardBoxAction';
	
	/**
	 * @var string
	 */
	public $boxName = '';
	
	/**
	 * @var string
	 */
	public $boxType = 'content';
	
	/**
	 * @var string
	 */
	public $boxClassName = '';
	
	/**
	 * @see \wcf\page\AbstractPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Register language variable
		I18nHandler::getInstance()->register('displayedName');
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Displayed name
		I18nHandler::getInstance()->readValues();
		$this->additionalFields['languageItems'] = array(
			'name' => array(
				'languageItemValues' => I18nHandler::getInstance()->getValues('displayedName')
			)
		);
		
		// Internal box name
		if(isset($_POST['boxName']) && is_string($_POST['boxName'])) {
			$this->boxName = StringUtil::trim($_POST['boxName']);
			
			// Add prefix
			$package = $this->projects[$this->packageID]->package;
			if(!StringUtil::startsWith($this->boxName, $package)) {
				$this->boxName = $package . '.' . $this->boxName;
			}
		}
		
		// Box type
		if(isset($_POST['boxType']) && $_POST['boxType'] == 'sidebar') {
			$this->boxType = 'sidebar';
		} else {
			$this->boxType = 'content';
		}
		
		// Class name
		if(isset($_POST['className']) && is_string($_POST['className'])) {
			$this->boxClassName = StringUtil::trim($_POST['className']);
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Validate internal box name
		if(empty($this->boxName)) {
			$this->errorType['boxName'] = 'empty';
		}
		
		// Validate displayed name
		if(!I18nHandler::getInstance()->validateValue('displayedName', true, false)) {
			$this->errorType['displayedName'] = 'multilingual';
		}
		
		// Validate class name
		if(empty($this->boxClassName)) {
			$this->errorType['className'] = 'empty';
		} elseif(!class_exists($this->boxClassName)) {
			$this->errorType['className'] = 'nonExistent';
		} else {
			$class = new \ReflectionClass($this->boxClassName);
			if(!$class->implementsInterface("\wcf\system\dashboard\box\IDashboardBox")) {
				$this->errorType['className'] = 'interface';
			}
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	boxID
			FROM	wcf".WCF_N."_dashboard_box
			WHERE	boxName COLLATE utf8_bin = ?
			AND	boxType = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->boxName,
			$this->boxType
		));
		
		if($statement->getAffectedRows() > 0) {
			$this->errorType['duplicate'] = true;
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'boxName' => $this->boxName,
				'boxType' => $this->boxType,
				'className' => $this->boxClassName
			)
		);
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();

		// Init values of I18nHandler
		I18nHandler::getInstance()->setOptions(
			'displayedName',
			$this->packageID,
			'wcf.dashboard.box.' . $this->boxName,
			''
		);
	}

	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		// Assign language variables
		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		
		WCF::getTPL()->assign(array(
			'boxID' => $this->objectID,
			'boxName' => $this->boxName,
			'boxType' => $this->boxType,
			'className' => $this->boxClassName
		));
	}
}
