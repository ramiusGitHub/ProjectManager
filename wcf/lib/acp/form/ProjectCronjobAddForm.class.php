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

use wcf\system\application\ApplicationHandler;
use wcf\data\package\Package;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\data\project\ProjectEditor;
use wcf\data\cronjob\CronjobAction;
use wcf\system\exception\IllegalLinkException;
use wcf\util\CronjobUtil;
use wcf\system\exception\SystemException;
use wcf\util\ProjectUtil;
use wcf\system\language\I18nHandler;

/**
 * Implementation of the database object form for cronjobs.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectCronjobAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\cronjob\ProjectCronjobAction';
	
	/**
	 * @var string
	 */
	public $cronjobClassName = '\wcf\system\cronjob\\';
	
	/**
	 * @var string
	 */
	public $description = '';
	
	/**
	 * @var string
	 */
	public $startMinute = '*';
	
	/**
	 * @var string
	 */
	public $startHour = '*';
	
	/**
	 * @var string
	 */
	public $startDom = '*';
	
	/**
	 * @var string
	 */
	public $startMonth = '*';
	
	/**
	 * @var string
	 */
	public $startDow = '*';
	
	/**
	 * @var boolean
	 */
	public $canBeEdited = true;
	
	/**
	 * @var boolean
	 */
	public $canBeDisabled = true;
	
	/**
	 * @var boolean
	 */
	public $defaultIsDisabled = false;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Register language variable
		I18nHandler::getInstance()->register('description');
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		// Cronjob's description
		I18nHandler::getInstance()->readValues();
		
		// Handle i18n plain input
		if(I18nHandler::getInstance()->isPlainValue('description')) {
			$this->description = I18nHandler::getInstance()->getValue('description');
		} else {
			$this->additionalFields['languageItems'] = array(
				'description' => array(
					'languageItemValues' => I18nHandler::getInstance()->getValues('description')
				)
			);
		}
		
		// Cronjob className
		if(isset($_POST['className'])) {
			$this->cronjobClassName = StringUtil::trim($_POST['className']);
		}
		
		// Interval
		if(isset($_POST['startMinute'])) {
			$this->startMinute = StringUtil::trim($_POST['startMinute']);
		}
		
		if(isset($_POST['startHour'])) {
			$this->startHour = StringUtil::trim($_POST['startHour']);
		}
		
		if(isset($_POST['startDom'])) {
			$this->startDom = StringUtil::trim($_POST['startDom']);
		}
		
		if(isset($_POST['startMonth'])) {
			$this->startMonth = StringUtil::trim($_POST['startMonth']);
		}
		
		if(isset($_POST['startDow'])) {
			$this->startDow = StringUtil::trim($_POST['startDow']);
		}

		// Additional options
		$this->canBeEdited = isset($_POST['canBeEdited']);
		$this->canBeDisabled = isset($_POST['canBeDisabled']);
		$this->defaultIsDisabled = isset($_POST['defaultIsDisabled']);
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Validate className
		if(empty($this->cronjobClassName)) {
			$this->errorType['className'] = 'empty';
		} elseif(!class_exists($this->cronjobClassName)) {
			$this->errorType['className'] = 'notFound';
		} else {
			$class = new \ReflectionClass($this->cronjobClassName);
			if(!$class->implementsInterface("\wcf\system\cronjob\ICronjob")) {
				$this->errorType['cronjobClassName'] = 'interface';
			}
		}
		
		// Validate interval
		$this->validateInterval($this->startMinute, '*', '*', '*', '*');
		$this->validateInterval('*', $this->startHour, '*', '*', '*');
		$this->validateInterval('*', '*', $this->startDom, '*', '*');
		$this->validateInterval('*', '*', '*', $this->startMonth, '*');
		$this->validateInterval('*', '*', '*', '*', $this->startDow);
	}
	
	/**
	 * Validates the time interval.
	 * 
	 * @param string $startMinute
	 * @param string $startHour
	 * @param string $startDom
	 * @param string $startMonth
	 * @param string $startDow
	 */
	protected function validateInterval($startMinute, $startHour, $startDom, $startMonth, $startDow) {
		try {
			CronjobUtil::validate($startMinute, $startHour, $startDom, $startMonth, $startDow);
		} catch(SystemException $e) {
			$description = $e->getMessage();
				
			$start = mb_strrpos($description, "'", -2) + 1;
			$name = mb_substr($description, $start, -1);
			
			$this->errorType[$name] = 'invalid';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		// Cronjobs are deleted by their classNames only.
		// That is why we assume unique classNames, even
		// though the cronjob table does not define the
		// className column as unique.
		// @see wcf\system\package\plugin\CronjobPackageInstallationPlugin::handleDelete()
		
		$sql = "SELECT	cronjobID
			FROM	wcf".WCF_N."_cronjob
			WHERE	className COLLATE utf8_bin = ?
			AND	packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array(
			$this->cronjobClassName,
			$this->packageID
		));
		
		if($statement->getAffectedRows() > 0) {
			$this->errorType['duplicate'] = true;
		}
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		if(!I18nHandler::getInstance()->isPlainValue('description')) {
			// Update object
			$action = new static::$className(
					array($this->object),
					'update',
					array(
						'data' => array(
							'description' => 'wcf.acp.cronjob.description.cronjob' . $this->object->getObjectID()
						)
					)
			);
			$action->executeAction();
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'className' => $this->cronjobClassName,
				'description' => $this->description,
				'startMinute' => $this->startMinute,
				'startHour' => $this->startHour,
				'startDom' => $this->startDom,
				'startMonth' => $this->startMonth,
				'startDow' => $this->startDow,
				'canBeEdited' => (int) $this->canBeEdited,
				'canBeDisabled' => (int) $this->canBeDisabled,
				'defaultIsDisabled' => (int) $this->defaultIsDisabled
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
			'description',
			$this->packageID,
			$this->description,
			'wcf.acp.cronjob.description.cronjob\d+'
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
			'cronjobID' => $this->objectID,
			'className' => $this->cronjobClassName,
			'description' => $this->description,
			'startMinute' => $this->startMinute,
			'startHour' => $this->startHour,
			'startDom' => $this->startDom,
			'startMonth' => $this->startMonth,
			'startDow' => $this->startDow,
			'canBeEdited' => $this->canBeEdited,
			'canBeDisabled' => $this->canBeDisabled,
			'defaultIsDisabled' => $this->defaultIsDisabled
		));
	}
}