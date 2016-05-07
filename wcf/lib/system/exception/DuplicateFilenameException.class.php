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
namespace wcf\system\exception;

use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Implementation of the logged exception for duplicate filenames.
 *
 * @author	Stefan Haas
 * @copyright	2016 Haas Webdesign
 * @license	The MIT License (MIT) <https://opensource.org/licenses/MIT>
 * @package	de.wcoding.projectmanager
 * @category	Community Framework
 */
class DuplicateFilenameException extends LoggedException implements IPrintableException {
	/**
	 * Package containing the duplicate file.
	 * 
	 * @var \wcf\data\package\Package
	 */
	protected $duplicatePackage = null;
	
	/**
	 * File already logged by this package.
	 * 
	 * @var \wcf\data\package\Package
	 */
	protected $loggedPackage = null;
	
	/**
	 * @var string
	 */
	protected $source = '';
	
	/**
	 * @var string
	 */
	protected $target = '';
	
	/**
	 * Creates a new DuplicateFilenameException.
	 * 
	 * @param \wcf\data\package\Package $duplicatePackage
	 * @param \wcf\data\package\Package $loggedPackage
	 * @param string $source
	 * @param string $target
	 */
	public function __construct(\wcf\data\package\Package $duplicatePackage, \wcf\data\package\Package $loggedPackage, $source, $target) {
		$this->duplicatePackage = $duplicatePackage;
		$this->loggedPackage = $loggedPackage;
		$this->source = $source;
		$this->target = $target;
	}
	
	/**
	 * @see \wcf\system\exception\IPrintableException::show()
	 */
	public function show() {
		// send status code
		@header('HTTP/1.1 503 Service Unavailable');
				
		// print report
		?><!DOCTYPE html>
		<html>
			<head>
				<title>Duplicate file: <?php echo StringUtil::encodeHTML($this->source); ?></title>
				<meta charset="utf-8" />
				<style>
					.systemException {
						font-family: 'Trebuchet MS', Arial, sans-serif !important;
						font-size: 80% !important;
						text-align: left !important;
						border: 1px solid #036;
						border-radius: 7px;
						background-color: #eee !important;
						overflow: auto !important;
					}
					.systemException h1 {
						font-size: 130% !important;
						font-weight: bold !important;
						line-height: 1.1 !important;
						text-decoration: none !important;
						text-shadow: 0 -1px 0 #003 !important;
						color: #fff !important;
						word-wrap: break-word !important;
						border-bottom: 1px solid #036;
						border-top-right-radius: 6px;
						border-top-left-radius: 6px;
						background-color: #369 !important;
						margin: 0 !important;
						padding: 5px 10px !important;
					}
					.systemException div {
						border-top: 1px solid #fff;
						border-bottom-right-radius: 6px;
						border-bottom-left-radius: 6px;
						padding: 0 10px !important;
					}
					.systemException h2 {
						font-size: 130% !important;
						font-weight: bold !important;
						color: #369 !important;
						text-shadow: 0 1px 0 #fff !important;
						margin: 5px 0 !important;
					}
					.systemException pre, .systemException p {
						text-shadow: none !important;
						color: #555 !important;
						margin: 0 !important;
					}
					.systemException pre {
						font-size: .85em !important;
						font-family: "Courier New" !important;
						text-overflow: ellipsis;
						padding-bottom: 1px;
						overflow: hidden !important;
					}
					.systemException pre:hover{
						text-overflow: clip;
						overflow: auto !important;
					}
				</style>
			</head>
			<body>
				<div class="systemException">
					<h1>Duplicate file: <?php echo StringUtil::encodeHTML($this->source); ?></h1>
					
					<div>
						<h2>Information:</h2>
						<p>
							<b>Source:</b> <?php echo $this->source; ?><br>
							<b>Project:</b> <?php echo $this->duplicatePackage->package; ?> (Package ID: <?php echo $this->duplicatePackage->packageID; ?>)<br>
							<b>Target:</b> <?php echo $this->target; ?><br>
							<b>Logged by Package:</b> <?php echo $this->loggedPackage->package; ?> (Package ID: <?php echo $this->loggedPackage->packageID; ?>)<br>
						</p>
							
						<h2>Stacktrace:</h2>
						<pre><?php echo StringUtil::encodeHTML($this->__getTraceAsString()); ?></pre>
					</div>
				</div>
			</body>
		</html>
		
		<?php
	}
}
