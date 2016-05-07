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
namespace wcf\data\project\version\merge;

/**
 * Implementation of the project version conflict for file conflicts.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class ProjectVersionFileConflict extends AbstractProjectVersionConflict {
	/**
	 * @var string
	 */
	protected $filename;
	
	/**
	 * @var string
	 */
	protected $sourceContent;
	
	/**
	 * @var string
	 */
	protected $targetContent;
	
	/**
	 * Creates a new ProjectVersionFileConflict instance.
	 * 
	 * @param string $filename
	 * @param string $sourceContent
	 * @param string $targetContent
	 */
	public function __construct($filename, $sourceContent, $targetContent = null) {
		$this->filename = $filename;
		$this->sourceContent = $sourceContent;
		$this->targetContent = $targetContent;
	}
	
	/**
	 * Returns the filename.
	 * 
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}
	
	/**
	 * Returns the content of the file in the source version.
	 * 
	 * @return string
	 */
	public function getSourceContent() {
		return $this->sourceContent;
	}
	
	/**
	 * Returns the content of the file in the target version.
	 * 
	 * @return string
	 */
	public function getTargetContent() {
		return $this->targetContent;
	}
	
	/**
	 * Returns whether the file exists in the target version.
	 * 
	 * @return boolean
	 */
	public function targetExists() {
		return $this->getTargetContent() !== null;
	}
}