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
use wcf\system\language\I18nHandler;
use wcf\data\bbcode\attribute\BBCodeAttributeAction;
use wcf\data\bbcode\BBCodeEditor;
use wcf\system\Regex;
use wcf\data\bbcode\BBCode;
use wcf\system\exception\UserInputException;
use wcf\system\cache\builder\ProjectBBCodeCacheBuilder;
use wcf\system\language\I18nHandler;

/**
 * Implementation of the database object form for BBCodes.
 * 
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 * @see \wcf\acp\form\BBCodeAddForm
 */
class ProjectBBCodeAddForm extends AbstractProjectDatabaseObjectForm {
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::$className
	 */
	protected static $className = '\wcf\data\bbcode\BBCode';
	
	// parameters
	public $allowedChildren = 'all';
	public $attributes = array();
	public $bbcodeTag = '';
	public $buttonLabel = '';
	public $bbcodeClassName = '';
	public $htmlClose = '';
	public $htmlOpen = '';
	public $isSourceCode = false;
	public $showButton = false;
	public $wysiwygIcon = '';
	
	/**
	 * @see \wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// Register language variable
		I18nHandler::getInstance()->register('buttonLabel');
	}
	
	/**
	 * @see \wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		if(isset($_POST['allowedChildren'])) $this->allowedChildren = StringUtil::trim($_POST['allowedChildren']);
		if(isset($_POST['attributes'])) $this->attributes = $_POST['attributes'];
		if(isset($_POST['bbcodeTag'])) $this->bbcodeTag = mb_strtolower(StringUtil::trim($_POST['bbcodeTag']));
		if(isset($_POST['className'])) $this->bbcodeClassName = StringUtil::trim($_POST['className']);
		if(isset($_POST['htmlClose'])) $this->htmlClose = StringUtil::trim($_POST['htmlClose']);
		if(isset($_POST['htmlOpen'])) $this->htmlOpen = StringUtil::trim($_POST['htmlOpen']);
		if(isset($_POST['isSourceCode'])) $this->isSourceCode = true;
		if(isset($_POST['showButton'])) $this->showButton = true;
		if(isset($_POST['wysiwygIcon'])) $this->wysiwygIcon = StringUtil::trim($_POST['wysiwygIcon']);
		
		// The code below violates every implicit convention of value reading and type casting
		$attributeNo = 0;
		foreach($this->attributes as $key => $val) {
			$val['attributeNo'] = $attributeNo++;
			$val['required'] = (int) isset($val['required']);
			$val['useText'] = (int) isset($val['useText']);
			$this->attributes[$key] = (object) $val;
		}
		
		I18nHandler::getInstance()->readValues();
		$this->readButtonLabelFormParameter();
	}
	
	/**
	 * Reads the form parameter for the button label.
	 */
	protected function readButtonLabelFormParameter() {
		if(I18nHandler::getInstance()->isPlainValue('buttonLabel')) {
			$this->buttonLabel = I18nHandler::getInstance()->getValue('buttonLabel');
		}
	}
	
	/**
	 * @see \wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// tag name must not be empty
		if(empty($this->bbcodeTag)) $this->errorType['bbcodeTag'] = 'empty';
		
		// tag may only contain alphanumeric chars
		if(!Regex::compile('^[a-z0-9]+$', Regex::CASE_INSENSITIVE)->match($this->bbcodeTag))  $this->errorType['bbcodeTag'] = 'notValid';
		
		// disallow the Pseudo-BBCodes all and none
		if($this->bbcodeTag == 'all' || $this->bbcodeTag == 'none') $this->errorType['bbcodeTag'] = 'notValid';
		
		// check whether the tag is in use
		$bbcode = BBCode::getBBCodeByTag($this->bbcodeTag);
		if((!isset($this->bbcode) && $bbcode->bbcodeID) || (isset($this->bbcode) && $bbcode->bbcodeID != $this->bbcode->bbcodeID))   $this->errorType['bbcodeTag'] = 'inUse';
		
		// handle empty case first
		if(empty($this->allowedChildren)) $this->errorType['bbcodeTag'] = 'allowedChildren';
		
		// validate syntax of allowedChildren: Optional all|none^ followed by a comma-separated list of bbcodes
		if(!empty($this->allowedChildren) && !Regex::compile('^(?:(?:all|none)\^)?(?:[a-zA-Z0-9]+,)*[a-zA-Z0-9]+$')->match($this->allowedChildren))   $this->errorType['allowedChildren'] = 'notValid';
		
		// validate class
		if(!empty($this->bbcodeClassName) && !class_exists($this->bbcodeClassName))  $this->errorType['className'] = 'notFound';
		
		// validate attributes
		foreach($this->attributes as $attribute) {
			// Check whether the pattern is a valid regex
			if(!Regex::compile($attribute->validationPattern)->isValid())   $this->errorType['attributeValidationPattern'.$attribute->attributeNo] = 'notValid';
		}
		
		// button
		if($this->showButton) {
			// validate label
			if(!I18nHandler::getInstance()->validateValue('buttonLabel')) {
				if(I18nHandler::getInstance()->isPlainValue('buttonLabel')) {
					$this->errorType['buttonLabel'] = 'empty';
				} else {
					$this->errorType['buttonLabel'] = 'multilingual';
				}
			}
			
			// validate image path
			if(empty($this->wysiwygIcon)) {
				$this->errorType['wysiwygIcon'] = 'empty';
			}
		} else {
			$this->buttonLabel = '';
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::validateDuplicate()
	 */
	public function validateDuplicate() {
		parent::validateDuplicate();
		
		$sql = "SELECT	bbcodeID
			FROM	wcf".WCF_N."_bbcode
			WHERE	bbcodeTag COLLATE utf8_bin = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->bbcodeTag));
		
		if($statement->getAffectedRows() > 0) $this->errorType['duplicate'] = true;
	}
	
	/**
	 * @see \wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		$bbcodeID = $this->object->bbcodeID;

		// save bbcode attributes
		foreach($this->attributes as $attribute) {
			$attributeAction = new BBCodeAttributeAction(array(), 'create', array('data' => array(
				'bbcodeID' => $bbcodeID,
				'attributeNo' => $attribute->attributeNo,
				'attributeHtml' => $attribute->attributeHtml,
				'validationPattern' => $attribute->validationPattern,
				'required' => $attribute->required,
				'useText' => $attribute->useText,
			)));
			$attributeAction->executeAction();
		}
		
		if($this->showButton && !I18nHandler::getInstance()->isPlainValue('buttonLabel')) {
			I18nHandler::getInstance()->compareAndSave('buttonLabel', 'wcf.bbcode.buttonLabel'.$bbcodeID, 'wcf.bbcode', $this->projects[$this->packageID]);
			
			// update button label
			$bbcodeEditor = new BBCodeEditor($this->object);
			$bbcodeEditor->update(array(
				'buttonLabel' => 'wcf.bbcode.buttonLabel'.$bbcodeID
			));
		}
	}
	
	/**
	 * @see \wcf\acp\form\AbstractProjectDataForm::getActionData()
	 */
	public function getActionData() {
		$result = array_merge(parent::getActionData(), array(
			'allowedChildren' => $this->allowedChildren,
			'bbcodeTag' => $this->bbcodeTag,
			'buttonLabel' => $this->buttonLabel,
			'className' => $this->bbcodeClassName,
			'htmlOpen' => $this->htmlOpen,
			'htmlClose' => $this->htmlClose,
			'isSourceCode' => ($this->isSourceCode ? 1 : 0),
			'packageID' => $this->packageID,
			'showButton' => ($this->showButton ? 1 : 0),
			'wysiwygIcon' => $this->wysiwygIcon
		));
		
		return $result;
	}
	
	/**
	 * @see \wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'allowedChildren' => $this->allowedChildren,
			'attributes' => $this->attributes,
			'bbcodeTag' => $this->bbcodeTag,
			'buttonLabel' => $this->buttonLabel,
			'className' => $this->bbcodeClassName,
			'htmlOpen' => $this->htmlOpen,
			'htmlClose' => $this->htmlClose,
			'isSourceCode' => $this->isSourceCode,
			'showButton' => $this->showButton,
			'wysiwygIcon' => $this->wysiwygIcon
		));
	}
}