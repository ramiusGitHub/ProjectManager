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
use wcf\data\project\ProjectEditor;
use wcf\data\project\ProjectAction;
use wcf\system\cache\builder\ProjectACPSearchProviderCacheBuilder;
use wcf\util\ProjectUtil;
use wcf\system\language\I18nHandler;

/**
 * Implementation of the database object form for ACP search providers.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectACPSearchProviderAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\acp\search\provider\ProjectACPSearchProviderAction';
	
	/**
	 * @var string
	 */
	public $providerName = '';
	
	/**
	 * @var string
	 */
	public $providerClassName = '\wcf\system\search\acp\\';
	
	/**
	 * @var integer
	 */
	public $showOrder = 0;
	
	/**
	 * @var boolean
	 */
	public $autoShowOrder = false;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
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
		
		// Provider's displayed name
		I18nHandler::getInstance()->readValues();
		$this->additionalFields['languageItems'] = array(
			'name' => array(
				'languageItemValues' => I18nHandler::getInstance()->getValues('displayedName')
			)
		);
		
		// Varchar parameters
		if(isset($_POST['providerName']) && is_string($_POST['providerName'])) {
			$this->providerName = StringUtil::trim($_POST['providerName']);
		}
		
		if(isset($_POST['className']) && is_string($_POST['className'])) {
			$this->providerClassName = trim(StringUtil::trim($_POST['className']), '\\');
		}
		
		// Integer parameters
		if(isset($_POST['showOrder'])) {
			$this->showOrder = intval($_POST['showOrder']);
		}
		
		// Boolean parameters
		$this->autoShowOrder = intval(isset($_POST['autoShowOrder']));
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// Validate className
		if(empty($this->providerClassName)) {
			$this->errorType['className'] = 'empty';
		} elseif(!class_exists($this->providerClassName)) {
			$this->errorType['className'] = 'notFound';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	providerID
			FROM	wcf".WCF_N."_acp_search_provider
			WHERE	packageID = ?
			AND	providerName COLLATE utf8_bin = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->packageID, $this->providerName));
		
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
				'providerName' => $this->providerName,
				'className' => $this->providerClassName,
				'showOrder' => $this->showOrder,
				'autoShowOrder' => (int) $this->autoShowOrder
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
			(empty($this->providerName) ? '' : 'wcf.acp.search.provider.'.$this->providerName),
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
			'providerID' => $this->objectID,
			'providerName' => $this->providerName,
			'className' => $this->providerClassName,
			'showOrder' => $this->showOrder,
			'autoShowOrder' => $this->autoShowOrder
		));
	}
}
