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
WCF.ProjectList = Class.extend({
	/**
	 * action proxy
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Initializes the WCF.ProjectList object.
	 */
	init: function() {
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		
		$('.jsProjectChangeStatusButton').each($.proxy(function(index, element) {
			var $button = $(element);
			var $packageID = $button.data('packageID');
			
			$button.click($.proxy(function(event) {
				this._changeStatus($packageID);
			}, this));
		}, this));
	},
	
	/**
	 * (De-)Activates a package.
	 */
	_changeStatus: function(packageID) {
		// switch icon
		$a = $('.jsProjectChangeStatusButton[data-package-id="'+packageID+'"]');
		$icon =  $a.find('span:eq(0)');
		
		$icon.addClass('icon-spinner');
		if($a.hasClass('isProject')) {
			$icon.removeClass('icon-circle');
		} else {
			$icon.removeClass('icon-circle-blank');
		}
		
		// send request
		this._proxy.setOption('data', {
			actionName: 'changeStatus',
			className: 'wcf\\data\\project\\ProjectAction',
			objectIDs: [ packageID ]
		});
		this._proxy.sendRequest();
	},
	
	/**
	 * Switches the package's icon.
	 */
	_success: function (data, textStatus, jqXHR) {
		$(data.objectIDs).each(function(key, id) {
			$a = $('.jsProjectChangeStatusButton[data-package-id="'+id+'"]');
			$icon = $a.find('span:eq(0)');
			
			$icon.removeClass('icon-spinner');
			if($a.hasClass('isProject')) {
				$a.removeClass('isProject');
				$icon.addClass('icon-circle-blank');
			} else {
				$a.addClass('isProject');
				$icon.addClass('icon-circle');
			}
		});
	}
});