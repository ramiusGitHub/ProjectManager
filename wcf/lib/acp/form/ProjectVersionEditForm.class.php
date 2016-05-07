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

use wcf\data\project\version\ProjectVersion;
use wcf\form\AbstractForm;
use wcf\data\project\version\ProjectVersionAction;
use wcf\system\WCF;

/**
 * Edit form for project versions.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionEditForm extends ProjectVersionAddForm {
	/**
	 * @see \wcf\page\AbstractPage::$action
	 */
	public $action = 'edit';
	
	/**
	 * @var \wcf\data\project\version\ProjectVersion
	 */
	public $version;
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		// Get logID
		if(isset($_REQUEST['id'])) {
			$this->version = new ProjectVersion(intval($_REQUEST['id']));
		}
		
		// Validate logID
		if($this->version === null || $this->version->getObjectID() === null) {
			throw new IllegalLinkException();
		}
		
		// Get project
		$this->project = $this->version->getProject();
		
		// Call parent method
		parent::readParameters();
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		// Validate that version number is between lower and higher version
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$action = new ProjectVersionAction(
			array($this->version),
			'update',
			array(
				'data' => array(
					'packageVersion' => $this->targetVersion
				)
			)
		);
		$action->executeAction();
		
		// Call saved method
		$this->saved();
	}

	/**
	 * @see \wcf\form\AbstractForm::saved()
	 */
	public function saved() {
		AbstractForm::saved();
		
		WCF::getTPL()->assign('success', 'edit');
	}

	/**
	 * @see \wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if(empty($_POST)) {
			$this->targetVersion = $this->version->getVersionString();
		}
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'version' => $this->version
		));
	}
}
