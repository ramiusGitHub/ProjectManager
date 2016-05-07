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
use wcf\data\package\Package;
use wcf\data\package\installation\plugin\PackageInstallationPluginList;

/**
 * Implementation of the database object form for instructions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectInstructionAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\project\instruction\InstructionAction';
	
	/**
	 * List of available PIPs.
	 * 
	 * @var array<string>
	 */
	public $pips = array();
	
	/**
	 * Internal name of the PIP execution.
	 * 
	 * @var string
	 */
	public $name = '';
	
	/**
	 * Name of the PIP which should be executed.
	 * 
	 * @var string
	 */
	public $pip = '';
	
	/**
	 * Whether the PIP should be executed at the beginning or the end of the instructions.
	 * 
	 * @var string
	 */
	public $position = 'start';
	
	/**
	 * Order of the PIP executions.
	 * 
	 * @var int
	 */
	public $executionOrder = 0;
	
	/**
	 * Should the PIP be executed if the package is installed?
	 * 
	 * @var boolean
	 */
	public $atInstall = false;
	
	/**
	 * When updating from one of the version numbers the PIP will be executed.
	 * 
	 * @var string
	 */
	public $versions = '';
	
	/**
	 * The content for the PIP execution.
	 * 
	 * @var string
	 */
	public $content = '';
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Load available PIPs
		$list = new PackageInstallationPluginList();
		$list->readObjects();
		
		foreach($list->getObjects() as $pip) {
			switch($pip->pluginName) {
				case 'file':
				case 'template':
				case 'acpTemplate':
					break;
				default:
					$this->pips[$pip->pluginName] = $pip;
					break;
			}
		}
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// Internal name
		if(isset($_POST['name']) && is_string($_POST['name'])) {
			$this->name = StringUtil::trim($_POST['name']);
		}
		
		// PIP
		if(isset($_POST['pip']) && is_string($_POST['pip'])) {
			$this->pip = StringUtil::trim($_POST['pip']);
		}
		
		// Position (start or end)
		if(isset($_POST['position']) && $_POST['position'] == 'end') {
			$this->position = 'end';
		} else {
			$this->position = 'start';
		}
		
		// Order
		if(isset($_POST['executionOrder'])) {
			$this->executionOrder = intval($_POST['executionOrder']);
		}
		
		// At install
		$this->atInstall = isset($_POST['atInstall']);
		
		// Versions
		if(isset($_POST['versions']) && is_string($_POST['versions'])) {
			$this->versions = StringUtil::unifyNewlines(StringUtil::trim($_POST['versions']));
		}
		
		// Content
		switch($this->pip) {
			case 'script':
				if(isset($_POST['filename']) && is_string($_POST['filename'])) {
					$this->content = StringUtil::trim($_POST['filename']);
				}
				break;
			case 'sql':
				if(isset($_POST['sql']) && is_string($_POST['sql'])) {
					$this->content = StringUtil::trim($_POST['sql']);
				}
				break;
			default:
				if(isset($_POST['xml']) && is_string($_POST['xml'])) {
					$this->content = StringUtil::trim($_POST['xml']);
				}
				break;
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// PIP
		if(!isset($this->pips[$this->pip])) {
			$this->errorType['pip'] = 'invalid';
		}
		
		// Versions
		if(!empty($this->versions)) {
			$versions = explode("\n", $this->versions);
			foreach($versions as $version) {
				if(!Package::isValidVersion($version)) {
					$this->errorType['versions'] = 'invalid';
					break;
				}
			}
		}
		
		// Content
		if(empty($this->content)) {
			switch($this->pip) {
				case 'script':
					$this->errorType['filename'] = 'empty';
					break;
				case 'sql':
					$this->errorType['sql'] = 'empty';
					break;
				default:
					$this->errorType['xml'] = 'empty';
					break;
			}
		}
		
		// TODO Validate content based on the selected PIP?
		// Check SQL with the SQL Parser
		// Validate if the PHP file exists
		// Check XML syntax
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(
			parent::getActionData(),
			array(
				'name' => $this->name,
				'pip' => $this->pip,
				'position' => $this->position,
				'executionOrder' => $this->executionOrder,
				'atInstall' => (int) $this->atInstall,
				'versions' => $this->versions,
				'content' => $this->content
			)
		);
		
		return $result;
	}

	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'pips' => $this->pips,
			'scriptID' => $this->objectID,
			'name' => $this->name,
			'pip' => $this->pip,
			'position' => $this->position,
			'executionOrder' => $this->executionOrder,
			'atInstall' => $this->atInstall,
			'versions' => $this->versions
		));
		
		switch($this->pip) {
			case 'script':
				WCF::getTPL()->assign('filename', $this->content);
				break;
			case 'sql':
				WCF::getTPL()->assign('sql', $this->content);
				break;
			default:
				WCF::getTPL()->assign('xml', $this->content);
				break;
		}
		
		
	}
}
