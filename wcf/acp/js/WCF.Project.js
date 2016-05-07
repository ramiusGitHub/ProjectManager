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
WCF.Project = { };

WCF.Project.Version = { };

WCF.Project.Version.Switch = Class.extend({
	/**
	 * Proxy object
	 * 
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Initializes a new Switch object.
	 */
	init: function() {
		$('.jsSwitchButton').click($.proxy(this._switchVersion, this));
		
		// Proxy object for saving new showOrder
		this._proxy = new WCF.Action.Proxy({
			showLoadingOverlay: true,
			success: $.proxy(this._success, this)
		});
	},
	
	_switchVersion: function(event) {
		var $button = $(event.target);
		
		this._proxy.setOption('data', {
			actionName: 'switchToVersion',
			className: 'wcf\\data\\project\\version\\ProjectVersionAction',
			objectIDs: [ $button.data('objectID') ]
		});
		
		this._proxy.sendRequest();
	},
	
	/**
	 * Handles successful AJAX requests.
	 * 
	 * @param object data
	 * @param string textStatus
	 * @param jQuery jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		location.reload();
	}
});

WCF.Project.Activate = Class.extend({
	
});

WCF.Project.Deactivate = Class.extend({
	
});

WCF.Project.Table = { };

WCF.Project.Table.Filter = Class.extend({
	/**
	 * Input field
	 * 
	 * @var jQuery
	 */
	_input: null,
	
	/**
	 * All rows
	 * 
	 * @var jQuery
	 */
	_rows: null,
	
	/**
	 * Data/Content rows to be filtered
	 * 
	 * @var jQuery
	 */
	_dataRows: null,
	
	/**
	 * Category rows
	 * 
	 * @var jQuery
	 */
	_categoryRows: null,
	
	/**
	 * Selector for category rows
	 * 
	 * @var string
	 */
	_categorySelector: '',
	
	/**
	 * Name of the data field for row spanned rows
	 * The rowspanDataField is used to identify rows which belong together
	 * 
	 * @var string
	 */
	_rowspanDataField: '',
	
	/**
	 * Filter against each word in the query or the whole query
	 * 
	 * @var boolean
	 */
	_disjunctiveFiltering: false,
	
	/**
	 * Caches the value in the input field
	 * 
	 * @var string
	 */
	_cachedQuery: '',
	
	/**
	 * Timer
	 * 
	 * @var Timer
	 */
	_typingTimer: null,
	
	/**
	 * Time in ms that has to pass until the filter function is executed
	 * 
	 * @var int
	 */
	_doneTypingInterval: 50,
	
	/**
	 * Is set to true after filter index was create
	 * 
	 * @var boolean
	 */
	_isFilterIndexCreated: false,
	
	/**
	 * Initializes a new Filter object for the given project table.
	 * 
	 * @param string input
	 * @param string table
	 * @param string categorySelector
	 * @param string rowspanDataField
	 * @param boolean disjunctiveFiltering
	 */
	init: function(input, table, categorySelector, rowspanDataField, disjunctiveFiltering) {
		// Read parameters
		this._input = $(input);
		this._categorySelector = categorySelector;
		if(rowspanDataField != null) this._rowspanDataField = rowspanDataField;
		if(disjunctiveFiltering) this._disjunctiveFiltering = true;
		
		// Fetch all rows
		this._rows = $(table).find("tbody tr");
		
		// Fetch data and category rows
		if(this._categorySelector != null) {
			this._dataRows = this._rows.filter(':not('+this._categorySelector+')');
			this._categoryRows = this._rows.filter(this._categorySelector);
		} else {
			this._dataRows = this._rows;
		}
		
		// Apply timer function
		this._input.keyup($.proxy(this._startTimer, this));
		this._input.keydown($.proxy(this._abortTimer, this));
		
		// Initial filtering if input is not empty
		// (e.g. when using the browsers "back"-function)
		if((typeof this._input.val()) != "undefined" && this._input.val().length != 0) {
			if(!this._isFilterIndexCreated) {
				this._createIndex();
			}
			
			this.filter();
		}
	},

	/**
	 * Starts the timer.
	 */
	_startTimer: function() {
		// Abort old timer
		this._abortTimer();
	    
		// Create new timer
		this._typingTimer = setTimeout($.proxy(this.filter, this), this._doneTypingInterval);
	    
		// Create index
		if(!this._isFilterIndexCreated) this._createIndex();
	},

	/**
	 * Aborts the timer.
	 */
	_abortTimer: function() {
		clearTimeout(this._typingTimer);
	},
	
	/**
	 * Case insensitive filtering of dataRows based on inputs value.
	 */
	filter: function() {
		// Init
		var self = this;
		var $time = $.now();

		// If no filter is entered show all rows
		if(this._input.val().length == 0) {
		    this._rows.show();
		    return;
		}
		
		// Current value of input
		var query = this._input.val().toLowerCase();
		var queryParts = query.split(" ");
		
		// Check query against cache
		if(this._cachedQuery == query) return;
		else this._cachedQuery = query;
		
		// Hide all data rows, then show rows matching the filter
		this._rows.hide();
		
		// Show data rows matching the filter
		this._dataRows.filter(function(i, v) {
			// Init
			var $row = $(this);
			
			// Check row
			if(typeof $row.data('filterIndex') === 'undefined') {
				return;
			}
			
			// Get text
			var text = $row.data('filterIndex');
			
			// Iterate over query parts
			for(var d = 0; d < queryParts.length; d++) {
				// Skip empty strings
				if(queryParts[d].length == 0) continue;
				
				// Check if the query part can be found in the row
				if(text.indexOf(queryParts[d]) >= 0) {
					// When filtering disjunctively, it is enough
					// to find one query part in the row -> return true
					if(self._disjunctiveFiltering) return true;
				} else {
					// When filtering conjunctively, not finding at
					// least one part of the query filters the row
					if(!self._disjunctiveFiltering) return false;
				}
			}
		    
			// If all query parts were found and we filter conjunctively this returns true
			// If no query part was found and we filter disjunctively this returns false
			return !self._disjunctiveFiltering;
		}).each(function(i, v) {
			// Init
			var $row = $(this); 
			$row.show();
			
			// Show category
			if(self._categoryRows.length > 0) {
				var $category = $row.prev(self._categorySelector);
				if($category.length == 0) $category = $row.prevUntil(self._categorySelector).last().prev();
				if($category.length == 1) $category.show();
			}
			
			// Display each row which has the same value in the data field
			if(self._rowspanDataField.length > 0) {
				var rowspanDataFieldValue = $row.data(self._rowspanDataField);
				var $next = $row;
				
				while(($next = $next.next()).data(self._rowspanDataField) == rowspanDataFieldValue) {
					$next.show();
				}
			}
		});
		
		// Set new typing interval
		this._doneTypingInterval = $.now() - $time;
	},
	
	/**
	 * Creates a data field with the searched text.
	 */
	_createIndex: function() {
		// Set indexCreated to true
		this._isFilterIndexCreated = true;
		
		// Init
		var self = this;
		var prevRowspanDataFieldValue = '';
		var currentCategoryText = '';
		var text = '';
		
		// Iterate over the rows
		this._rows.each(function() {
			var $row = $(this);
			
			if(self._categorySelector != null && $row.is(self._categorySelector)) {
				currentCategoryText = $.trim($row.text());
			} else {
				// Get text (of spanned rows)
				if(self._rowspanDataField.length > 0) {
					var rowspanDataFieldValue = $row.data(self._rowspanDataField);
					if(prevRowspanDataFieldValue != rowspanDataFieldValue) {
						// Set variables
						prevRowspanDataFieldValue = rowspanDataFieldValue;
						
						// Aggregate text
						text = $.trim($row.add($row.nextUntil(':not([data-'+self._rowspanDataField+'="'+rowspanDataFieldValue+'"])')).text());
					} else {
						// Do not create index cell for spanned rows multiple times
						return;
					}
				} else {
					text = $.trim($row.text());
				}
				
				// Add category text
				text = (currentCategoryText + text).toLowerCase();
				
				// Add index
				$row.data('filterIndex', text);
			}
		});
	}
});

WCF.Project.Table.SpannedRowHighlight = Class.extend({
	/**
	 * Data identifier
	 * 
	 * @var string
	 */
	_id: 'object-id',
	
	/**
	 * CSS class name
	 * 
	 * @var string
	 */	
	_className: 'hover',
	
	/**
	 * Initializes the WCF.Project.Table.SpannedRowHighlight class.
	 * 
	 * @param string table id
	 * @param string data identifier
	 * @param string css class name
	 */
	init: function(table, id, className) {
		if(id != null) this._id = id;
		if(className != null) this._className = className;
		
		var $self = this;
		$(table+" td").hover(function() {
			var $row = $(this).parent();
			var $rows = $row.parent().find('[data-'+$self._id+'="'+$row.data($self._id)+'"]');
			$rows.addClass($self._className);
		}, function() {
			var $row = $(this).parent();
			var $rows = $row.parent().find('[data-'+$self._id+'="'+$row.data($self._id)+'"]');
			$rows.removeClass($self._className);
		});
	},
});

WCF.Project.Table.Sortable = Class.extend({
	/**
	 * Action class name
	 * 
	 * @var string
	 */
	_className: '',
	
	/**
	 * Container id
	 * 
	 * @var string
	 */
	_containerID: '',
	
	/**
	 * Container object
	 * 
	 * @var jQuery
	 */
	_container: null,
	
	/**
	 * Category data field name
	 * 
	 * @var string
	 */
	_categoryDataField: '',
	
	/**
	 * Category css selector
	 * 
	 * @var string
	 */
	_categorySelector: '',
	
	/**
	 * Blocks dragging elements above the first category
	 * 
	 * @var boolean
	 */
	_blockAboveFirstCategory: '',
	
	/**
	 * Name of the order column
	 * 
	 * @var string
	 */
	_orderName: '',
	
	/**
	 * Proxy object
	 * 
	 * @var WCF.Action.Proxy
	 */
	_proxy: null,
	
	/**
	 * Creates a new sortable list.
	 * 
	 * @param object data
	 */
	init: function(data) {
		// Parameters
		this._containerID = $.wcfEscapeID(data.containerID);
		this._container = $('#' + this._containerID + ' .sortableList');
		this._className = data.className;
		this._categoryDataField = (data.categoryDataField ? data.categoryDataField : '');
		this._categorySelector = (data.categorySelector ? data.categorySelector : '');
		this._blockAboveFirstCategory = (this._categorySelector.length > 0 && data.blockAboveFirstCategory);
		this._orderName = (data.orderName ? data.orderName : 'showOrder');
		
		// Proxy object for saving new showOrder
		this._proxy = new WCF.Action.Proxy();
		
		// Init sortable
		this._container.sortable({
			// Options
			axis: 'y',
			cancel: '.sortableNoSorting',
			connectWith: this._container,
			doNotClear: true,
			errorClass: 'sortableInvalidTarget',
			forcePlaceholderSize: true,
			helper: $.proxy(this._helper, this),
			opacity: .6,
			placeholder: 'sortablePlaceholder',
			tolerance: 'pointer',
			items: 'tr'+(this._blockAboveFirstCategory ? ':not('+this._categorySelector+':first-child)' : ''),

			// Events - see description of the _update function
			update: $.proxy(this._update, this)
		});
	},
	
	/**
	 * Adds a width to all cells of the dragged row and the table head to prevent collapsing.
	 */
	_helper: function(event, ui) {
		// Fix width of table columns
		$('#'+this._containerID+' thead th').each(function() {
			$(this).width($(this).width());
		});
		
		// Fix width of dragged row
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		
		return ui;
	},
	
	/**
	 * This function is called whenever the jQuery
	 * sortable fires its 'update' event. 
	 * This event is triggered when the user stopped
	 * sorting and the DOM position has changed.
	 */
	_update: function(event, ui) {
		var $row = $(ui.item.context);
		
		// Check deletion status
		if($row.hasClass('deleted')) return;
		
		// Init
		var objectID = $row.data('objectID');
		var oldCategoryName = $row.data(this._categoryDataField);
		var isHidden = $row.data('isHidden');
		
		// Get next category name
		var $prev = $row.prev();
		var $next = $row.next();
		var newCategoryName = '';
		
		if($prev.length > 0) {
			newCategoryName = $prev.data(this._categoryDataField);
		} else {
			if($next.filter(':not('+this._categorySelector+')').length > 0) {
				newCategoryName = $next.data(this._categoryDataField);
			}
		}
		
		// Update categoryName of dragged element
		$row.data(this._categoryDataField, newCategoryName);
		
		// Check if dragged element is hidden
		// Hidden elements do neither change the order
		// of other elements nor have a order on their own
		if(isHidden) {
			if(oldCategoryName != newCategoryName) {
				// update category of hidden element
				var data = {};
				data[this._categoryDataField] = newCategoryName;
				
				this._proxy.setOption('data', {
					actionName: 'update',
					className: this._className,
					objectIDs: [ objectID ],
					parameters: data
				});
				
				this._proxy.sendRequest();
			}
		} else {
			// if dragged element is _not_ hidden
			// update orders in old and new category
			// and gather the data for the proxy request
			var data = {};
			var counter = {};
			
			var categoryDataField = this._categoryDataField;
			$rows = this._container.find('tr'+(this._categorySelector.length > 0 ? ':not('+this._categorySelector+')' : '')).filter(function(index) {
				return	$(this).data(this._orderName) !== -1 && // no automatic order
						!$(this).hasClass('deleted') && // row is not deleted
						
						// row is in new or old category
						(	$(this).data(categoryDataField) === newCategoryName ||
							$(this).data(categoryDataField) === oldCategoryName
						);
			});
			
			counter[newCategoryName] = 0;
			counter[oldCategoryName] = 0;
			
			var orderName = this._orderName;
			$rows.each(function(i, v) {
				var $v = $(v);
				
				if(!$v.data('is-hidden')) {
					var vCategoryName = $v.data(categoryDataField);
					
					if(!$v.data('auto-' + orderName)) {
						$v.children('.column' + orderName.charAt(0).toUpperCase() + orderName.slice(1)).text(counter[vCategoryName]);
					}
					$v.data(orderName, counter[vCategoryName]);
					
					data[$v.data('objectID')] = {};
					data[$v.data('objectID')][orderName] = counter[vCategoryName];
					data[$v.data('objectID')][categoryDataField] = vCategoryName;
					
					counter[vCategoryName]++;
				}
			});
			
			// Send new orders and categories to server
			this._proxy.setOption('data', {
				actionName: 'update',
				className: this._className,
				objectIDs: Object.keys(data),
				parameters: {
					dataPerObject: true,
					data: data,
				}
			});
			
			this._proxy.sendRequest();	
		}
	}
});

WCF.Project.Action = { };

WCF.Project.Action.Delete = WCF.Action.Delete.extend({
	/**
	 * Name of the data field for row spanned rows.
	 * The rowspanDataField is used to identify rows which belong together.
	 * 
	 * @var array
	 */
	_rowspanDataField: [],
	
	/**
	 * @see WCF.Action.Delete.init()
	 */	
	init: function(className, containerSelector, buttonSelector, rowspanDataField) {
		this._super(className, containerSelector, buttonSelector);
		
		if(rowspanDataField != null) {
			this._rowspanDataField[buttonSelector] = rowspanDataField;
		}
	},
	
	/**
	 * @see WCF.Action.Delete._initElements()
	 */
	_initElements: function() {
		$(this._containerSelector).each((function(index, container) {
			var $container = $(container);
			var $containerID = $container.wcfIdentify();
			
			if(!WCF.inArray($containerID, this._containers[this._buttonSelector])) {
				var $deleteButton = $container.find(this._buttonSelector);
				
				if($deleteButton.length) {
					if(this._containers[this._buttonSelector] == undefined) {
						this._containers[this._buttonSelector] = [];
					}
					
					this._containers[this._buttonSelector].push($containerID);
					$deleteButton.click($.proxy(this._click, this));
				}
			}
		}).bind(this));
	},
	
	/**
	 * @see WCF.Action.Delete._sendRequest()
	 */
	_sendRequest: function(object) {
		var $object = $(object);
		var objectIDs = null;
		
		if($object.attr('data-object-ids')) {
			objectIDs = $object.data('object-ids');
		} else {
			objectIDs = [ $object.data('object-id') ];
		}
		
		this.proxy.setOption('data', {
			actionName: 'delete',
			className: this._className,
			objectIDs: objectIDs
		});
		
		this.proxy.sendRequest();
	},

	/**
	 * @see WCF.Action.Delete._success()
	 */
	_success: function(data, textStatus, jqXHR) {
		// data.objectIDs contains only the one objectID initially sent
		// by the request. data.returnValues contains the objectIDs of
		// all the objects affected by the action.
		this.triggerEffect(data.returnValues);
	},

	/**
	 * @see WCF.Action.Delete.triggerEffect()
	 */
	triggerEffect: function(objectIDs) {
		for(var pip in objectIDs) {
			var deleteButtonSelector = '.js' + pip + 'DeleteButton';
			
			for(var $index in this._containers[deleteButtonSelector]) {
				var $container = $('#' + this._containers[deleteButtonSelector][$index]);
				var $button = $container.find(deleteButtonSelector);
				
				if($button.length && WCF.inArray($button.data('objectID'), objectIDs[pip])) {
					// Reduce counter by one
					var $counter = $container.parents('.projectTabularBox').find('.badgeCounter');
					$counter.text($counter.text() - 1);
					
					// Mark row as deleted
					if(this._rowspanDataField[deleteButtonSelector] != undefined) {
						$id = $container.data(this._rowspanDataField[deleteButtonSelector]);
						$($.find('[data-' + this._rowspanDataField[deleteButtonSelector] + '="' + $id + '"]')).addClass('deleted');
					} else {
						$container.addClass('deleted');
					}
				}
			}
		}
	},
});

WCF.Project.Action.Delete.Database = WCF.Project.Action.Delete.extend({
	/**
	 * @see WCF.Action.Delete.init()
	 */	
	init: function() {
		containerSelector = '.jsDatabaseTableRow, .jsDatabaseColumnRow, .jsDatabaseIndexRow, .jsDatabaseForeignKeyRow';
		
		this._super('wcf\\data\\project\\database\\log\\ProjectDatabaseLogAction', containerSelector, '.jsDatabaseDeleteButton');
	}
});

WCF.Project.Database = { };

WCF.Project.Database.IndexAddTable = Class.extend({
	/**
	 * @var array
	 */
	_options: {
		axis: 'xy',
		helper: 'clone',
		opacity: .5,
		placeholder: 'sortablePlaceholder',
		tolerance: 'pointer',
		items: 'li'
	},
	
	/**
	 * Initializes a new IndexTable object.
	 */
	init: function() {
		// Extend sortable options
		this._options = $.extend(true, {
			receive: $.proxy(this._receive, this),
		}, this._options);
		
		// Select table
		$('#sqlTable').change($.proxy(function() {
			// Hide all
			$('#availableColumns ul').hide();
			$('#selectedColumns ul').hide();
			
			// Get columns of selected table
			var $tableName = $('#sqlTable').val();
			var $availableColumns = $('#availableColumns ul[data-table-name="'+$tableName+'"]');
			var $selectedColumns = $('#selectedColumns ul[data-table-name="'+$tableName+'"]');
			
			// Init sortable
			if(!$availableColumns.hasClass('ui-sortable')) {
				var $container = $availableColumns.add($selectedColumns);
				$container.sortable(this._options, {connectWith: $container});
			}
			
			// Show columns
			$availableColumns.show();
			$selectedColumns.show();
		}, this));
		$('#sqlTable').change();
	},
	
	/** 
	 * The receive event is triggered when the user dropped an item
	 * from a connected list into another list. The event
	 * is triggered by the target list.
	 */
	_receive: function(event, ui) {
		var disable = ($(event.target) == $('#availableColumns'));
		$(ui.item).find('input').prop("disabled", disable);
	}
});

WCF.Project.Database.ForeignKeyAddTable = WCF.Project.Database.IndexAddTable.extend({
	/**
	 * Initializes a new ForeignKeyTable object.
	 */
	init: function() {
		// Extend sortable options
		this._options = $.extend(true, {
			start: $.proxy(this._start, this),
			stop: $.proxy(this._stop, this)
		}, this._options);
		
		this._super();
		
		// Select table
		$('#sqlTable').change($.proxy(function() {
			// start&stop
			this._start();
			this._stop();
		}, this));
		
		// Select reference table
		$('#referencedSqlTable').change($.proxy(function() {
			// Hide all
			$('#availableReferencedColumns ul').hide();
			$('#selectedReferencedColumns ul').hide();

			// Get columns of selected table
			var $tableName = $('#referencedSqlTable').val();
			var $availableColumns = $('#availableReferencedColumns ul[data-table-name="'+$tableName+'"]');
			var $selectedColumns = $('#selectedReferencedColumns ul[data-table-name="'+$tableName+'"]');
			
			// Init sortable
			if(!$availableColumns.hasClass('ui-sortable')) {
				var $container = $availableColumns.add($selectedColumns);
				$container.sortable(this._options, {connectWith: $container});
			}

			// Show columns
			$availableColumns.show();
			$selectedColumns.show();
			
			// Start & stop
			this._start();
			this._stop();
		}, this));
		$('#referencedSqlTable').change();
	},
	
	/** 
	 * The receive event is triggered when the user dropped an item
	 * from a connected list into another list. The event
	 * is triggered by the target list.
	 */
	_receive: function(event, ui) {
		var $target = $(event.target);
		
		var disable = ($target.parent('#availableColumns').length || $target.parent('#availableReferencedColumns').length);
		$(ui.item).find('input').prop("disabled", disable);
	},
	
	/** 
	 * The start event is triggered when the user starts dragging
	 * an item. The event is triggered by the dragged item.
	 */
	_start: function(event, ui) {
		var $tableName = $('#sqlTable').val();
		var $referencedSqlTable = $('#referencedSqlTable').val();
		
		$('#selectedColumns ul[data-table-name="'+$tableName+'"] li').removeClass('matching notMatching');
		$('#selectedReferencedColumns ul[data-table-name="'+$referencedSqlTable+'"] li').removeClass('matching notMatching');
	},
	
	/** 
	 * The start event is triggered when the user stops dragging
	 * an item. The event is triggered by the dragged item.
	 */
	_stop: function(event, ui) {
		var $tableName = $('#sqlTable').val();
		var $referencedSqlTable = $('#referencedSqlTable').val();
		
		var $columns = $('#selectedColumns ul[data-table-name="'+$tableName+'"] li');
		var $referencedColumns = $('#selectedReferencedColumns ul[data-table-name="'+$referencedSqlTable+'"] li');
		
		$columns.each(function(index, item) {
			if($referencedColumns.get(index)) {
				var $column = $(item);
				var $referencedColumn = $($referencedColumns.get(index));
				
				if($column.find('input').data('type') == $referencedColumn.find('input').data('type')) {
					$column.addClass('matching');
					$referencedColumn.addClass('matching');
				} else {
					$column.addClass('notMatching');
					$referencedColumn.addClass('notMatching');
				}
			}
		});
	},
});