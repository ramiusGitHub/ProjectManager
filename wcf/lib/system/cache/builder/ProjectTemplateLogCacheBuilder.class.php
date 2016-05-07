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
namespace wcf\system\cache\builder;

use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\data\template\TemplateList;

/**
 * Cache builder for template logs.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectTemplateLogCacheBuilder extends AbstractCacheBuilder {	
	/**
	 * @see \wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		// Get all log entries
		$templateLogs = $this->getTemplateLogs();
		
		// Structure data
		$packageTemplates = array();
		$applicationTemplates = array();
		
		foreach($templateLogs as $templateLog) {
			// Group by package
			$packageTemplates[$templateLog->packageID][$templateLog->templateID] = $templateLog;
			
			// Group by application
			$applicationTemplates[$templateLog->application][$templateLog->templateName] = $templateLog;
		}
		
		// Return structured data
		return array(
			'list' => $templateLogs,
			'packageTemplateLogs' => $packageTemplates,
			'applicationTemplateLogs' => $applicationTemplates
		);
	}
	
	/**
	 * Returns the complete list of project template logs.
	 * 
	 * @return array<\wcf\data\template\Template>
	 */
	protected function getTemplateLogs() {
		$list = new TemplateList();
		$list->readObjects();
		
		return $list->getObjects();
	}
}
