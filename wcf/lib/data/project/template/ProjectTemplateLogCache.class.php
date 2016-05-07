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
namespace wcf\data\project\template;

use wcf\system\WCF;
use wcf\system\SingletonFactory;
use wcf\data\template\TemplateList;

/**
 * Manages the template log cache.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectTemplateLogCache extends SingletonFactory {
	/**
	 * @var string
	 */
	protected static $cacheBuilder = '\wcf\system\cache\builder\ProjectTemplateLogCacheBuilder';
	
	/**
	 * @var array<mixed>
	 */
	protected $templateLogCache;
	
	/**
	 * @see \wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->templateLogCache = call_user_func(array(static::$cacheBuilder, 'getInstance'))->getData();
	}
	
	/**
	 * Returns the template which is logged with the given template ID
	 * or null if no entry with this ID exists.
	 * 
	 * @param int $templateID
	 * @return \wcf\data\template\Template
	 */
	public function getTemplateLog($templateID) {
		if(isset($this->templateLogCache['list'][$templateID])) {
			return $this->templateLogCache['list'][$templateID];
		}
		
		return null;
	}
		
	/**
	 * Returns all templates which belong to the package ID.
	 * 
	 * @param int $packageID
	 * @return array<\wcf\data\template\Template>
	 */
	public function getPackageTemplateLogs($packageID) {
		if(isset($this->templateLogCache['packageTemplateLogs'][$packageID])) {
			return $this->templateLogCache['packageTemplateLogs'][$packageID];
		}
		
		return array();
	}
	
	/**
	 * Returns all templates which belong to the application.
	 * 
	 * @param string $application
	 * @return array<\wcf\data\template\Template>
	 */
	public function getApplicationTemplateLogs($application = 'wcf') {
		if(isset($this->templateLogCache['applicationTemplateLogs'][$application])) {
			return $this->templateLogCache['applicationTemplateLogs'][$application];
		}
		
		return array();
	}
	
	/**
	 * Returns the template which belongs to the application and has the given filename.
	 * 
	 * @param string $application
	 * @param string $templateName
	 * @return \wcf\data\template\Template
	 */
	public function getApplicationTemplateLog($application, $templateName) {
		if(isset($this->templateLogCache['applicationTemplateLogs'][$application][$templateName])) {
			return $this->templateLogCache['applicationTemplateLogs'][$application][$templateName];
		}
		
		return null;
	}
}
