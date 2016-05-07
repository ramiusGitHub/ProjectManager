<script data-relocate="true">
	//<![CDATA[
	$('#type').change(function() {
		// Get selected type value
		$type = $(this).val();
		
		// Show type info
		if($type !== null) $('#databaseTypeInfo').text($(this).find('option:selected').attr('title')).show();
		
		// Display 'default' fields if type with default value is selected
		var noDefaultTypes = [
			'tinytext',
			'text',
			'mediumtext',
			'longtext',
			'tinyblob',
			'mediumblob',
			'blob',
			'longblob'
		];
		if($.inArray($type, noDefaultTypes) > -1) {
			$('#defaultContainer').hide();
		} else {
			$('#defaultContainer').show();
			
			// timestamp as default
			if($type == 'timestamp') {
				$('#defaultCurrentTimestampContainer').show();
			} else {
				$('#defaultCurrentTimestampContainer').hide();
			}
		}
		
		// Display 'values' field if SET or ENUM is selected
		if($type == 'enum' || $type == 'set') {
			$('#valuesContainer').show();
		} else {
			$('#valuesContainer').hide();
		}
		
		// Display 'charLength' field if CHAR is selected
		if($type == 'char') {
			$('#charLengthContainer').show();
		} else {
			$('#charLengthContainer').hide();
		}
		
		// Display 'varcharLength' field if VARCHAR is selected
		if($type == 'varchar') {
			$('#varcharLengthContainer').show();
		} else {
			$('#varcharLengthContainer').hide();
		}
		
		// Display 'decimalLength' and 'decimalDecimals' field if DECIMAL is selected
		if($type == 'decimal') {
			$('#decimalLengthContainer').show();
			$('#decimalDecimalsContainer').show();
		} else {
			$('#decimalLengthContainer').hide();
			$('#decimalDecimalsContainer').hide();
		}
		
		// Define postgresql unsupported types
		// If in array, display warning, else hide warning
		var postgreSqlUnsupported = [
			// numberic
			'serial',
			
			// string
		        'set',
		           	
		        // data and time
		        'date', 'datetime', 'timestamp', 'time', 'year',
		           	
		        // spatial
		        'geometry',	'point', 'linestring', 'polygon', 'multipoint',
			'multilinestring', 'multipolygon', 'geometrycollection',
		];
		if($.inArray($type, postgreSqlUnsupported) > -1) {
			$('#postgreSqlUnsupportedWarning').show();
		} else {
			$('#postgreSqlUnsupportedWarning').hide();
		}
	});
	
	// Display 'defaultNull' if NULL is allowed
	$('#notNull').change(function() {
		if($(this).prop('checked')) {
			$('#defaultNullContainer').hide();
		} else {
			$('#defaultNullContainer').show();
		}
	});
	
	// Display 'autoIncrement' if any integer type and an UNIQUE or PRIMARY index is selected
	$('#type').add($('#key')).change(function() {
		// Get selected values
		$type = $('#type').val();
		$key = $('#key').val();
		
		var integerTypes = ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'];
		var aiIndices = ['PRIMARY', 'UNIQUE'];
		if($.inArray($type, integerTypes) > -1 && $.inArray($key, aiIndices) > -1) {
			$('#autoIncrementContainer').show();
		} else {
			$('#autoIncrementContainer').hide();
		}
	});
	
	// Disable default value inputs if no default is checked
	$('#noDefault').change(function() {
		$('#default').prop('disabled', this.checked);
		$('#defaultNull').prop('disabled', this.checked);
		$('#defaultCurrentTimestamp').prop('disabled', this.checked);
	});
	
	$('#type').change();
	$('#notNull').change();
	$('#noDefault').change();
	//]]>
</script>

<fieldset>
	<legend>{lang}wcf.project.general{/lang}</legend>
	
	{@$tableField}
	
	<dl{if $errorType.name|isset} class="formError"{/if}>
		<dt>
			<label for="sqlColumn">
				{lang}wcf.project.database.column.sqlColumn{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="text"
				id="sqlColumn"
				name="sqlColumn"
				value="{$sqlColumn}"
				required="required"
				autofocus="autofocus"
				pattern="^[a-zA-Z_][a-zA-Z0-9_]*$"
				maxlength="64"
				class="long"
			/>
			
			{if $errorType.sqlColumn|isset}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.sqlColumn.{$errorType.sqlColumn}{/lang}
					{/if}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.sqlColumn.description{/lang}</small>
		</dd>
	</dl>
	
	{event name='general'}
</fieldset>

<fieldset>
	<legend>{lang}wcf.project.database.column.data{/lang}</legend>
	
	<dl{if $errorType.type|isset} class="formError"{/if}>
		<dt>
			<label for="type">
				{lang}wcf.project.database.column.type{/lang}
			</label>
		</dt>
		<dd>
			<select name="type" id="type">
				<option value="" disabled="disabled"{if $type|empty} selected="selected"{/if}>{lang}wcf.project.database.column.type.placeholder{/lang}</option>
				
				{foreach from=$types key=name item=t1}
					{if $t1|is_array}
						<optgroup label="{$name}">
							{foreach from=$t1 item=t2}
								{if $t2 == 'disabled'}
									<option disabled="disabled">-</option>
								{else}
									<option value="{$t2}" title="{lang}wcf.project.database.column.types.{$t2}{/lang}"{if $type == $t2} selected="selected"{/if}>{$t2}</option>
								{/if}
							{/foreach}
						</optgroup>
					{else}
						{if $t1 == 'disabled'}
							<option disabled="disabled">-</option>
						{else}
							<option value="{$t1}" title="{lang}wcf.project.database.column.types.{$t1}{/lang}"{if $type == $t1} selected="selected"{/if}>{$t1}</option>
						{/if}
					{/if}
				{/foreach}
			</select>
			
			{if $errorType.type|isset}
				<small class="innerError">
					{if $errorType.type == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.type.{@$errorType.type}{/lang}
					{/if}
				</small>
			{/if}
			
			<small id="databaseTypeInfo" class="info" style="display: none; color: #fff"></small>
			
			<small>{lang}wcf.project.database.column.type.description{/lang}</small>
			
			<small id="postgreSqlUnsupportedWarning" class="warning" style="display: none; color: #000; text-shadow: 0 1px 0 rgba(255,255,255,0.4);">
				{lang}wcf.project.database.column.type.postgreSqlUnsupportedWarning{/lang}
			</small>
		</dd>
	</dl>
	
	<dl id="charLengthContainer"{if $errorType.charLength|isset} class="formError"{/if}>
		<dt>
			<label for="charLength">
				{lang}wcf.project.database.column.charLength{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="number"
				min="0"
				max="255"
				id="charLength"
				name="charLength"
				value="{if $type == 'char'}{$length}{/if}"
				class="short"
			/>
			
			{if $errorField == 'charLength'}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.charLength.{$errorType}{/lang}
					{/if}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.charLength.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="varcharLengthContainer"{if $errorType.varcharLength|isset} class="formError"{/if}>
		<dt>
			<label for="varcharLength">
				{lang}wcf.project.database.column.varcharLength{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="number"
				min="0"
				max="65535"
				id="varcharLength"
				name="varcharLength"
				value="{if $type == 'varchar'}{$length}{/if}"
				class="short"
			/>
			
			{if $errorType.varcharLength|isset}
				<small class="innerError">
					{if $errorType == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.varcharLength.{$errorType}{/lang}
					{/if}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.varcharLength.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="decimalLengthContainer"{if $errorType.decimalLength|isset} class="formError"{/if}>
		<dt>
			<label for="decimalLength">
				{lang}wcf.project.database.column.decimalLength{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="number"
				min="1"
				max="65"
				id="decimalLength"
				name="decimalLength"
				value="{if $type == 'decimal'}{$length}{/if}"
				class="short"
			/>
			
			{if $errorType.decimalLength|isset}
				<small class="innerError">
					{if $errorType.decimalLength == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.decimalLength.{$errorType.decimalLength}{/lang}
					{/if}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.decimalLength.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="decimalDecimalsContainer"{if $errorType.decimalDecimals|isset} class="formError"{/if}>
		<dt>
			<label for="decimalDecimals">
				{lang}wcf.project.database.column.decimalDecimals{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="number"
				min="0"
				max="30"
				id="decimalDecimals"
				name="decimalDecimals"
				value="{$decimals}"
				class="short"
			/>
			
			{if $errorType.decimalDecimals|isset}
				<small class="innerError">
					{if $errorType.decimalDecimals == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.decimalDecimals.{$errorType.decimalDecimals}{/lang}
					{/if}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.decimalDecimals.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="valuesContainer" {if $errorType.values|isset} class="formError"{/if}>
		<dt>
			<label for="values">
				{lang}wcf.project.database.column.values{/lang}
			</label>
		</dt>
		<dd>
			<textarea
				name="values"
				id="values"
				rows="5"
				cols="40"
			>{if $values|is_array}{"\n"|implode:$values}{/if}</textarea>
			
			{if $errorType.values|isset}
				<small class="innerError">
					{if $errorType.values == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.column.values.{@$errorType.values}{/lang}
					{/if}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.values.description{/lang}</small>
		</dd>
	</dl>
	
	<dl>
		<dt class="reversed">
			<label for="notNull">
				{lang}wcf.project.database.column.notNull{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="checkbox"
				id="notNull"
				name="notNull"
				value="1"
				{if $notNull} checked="checked"{/if}
			/>
			
			<small>{lang}wcf.project.database.column.notNull.description{/lang}</small>
		</dd>
	</dl>
			
	<dl{if $errorType.key|isset} class="formError"{/if}>
		<dt>
			<label for="key">
				{lang}wcf.project.database.index.key{/lang}
			</label>
		</dt>
		<dd>
			<select name="key" id="key">
				{foreach from=$keys item=k}
					<option value="{$k}"{if $key == $k} selected="selected"{/if}>{$k}</option>
				{/foreach}
			</select>
			
			{if $errorType.key|isset}
				<small class="innerError">
					{if $errorType.key == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.index.key.{@$errorType.key}{/lang}
					{/if}
				</small>
			{/if}
		</dd>
	</dl>
	
	<dl id="autoIncrementContainer">
		<dt class="reversed">
			<label for="autoIncrement">
				{lang}wcf.project.database.column.autoIncrement{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="checkbox"
				id="autoIncrement"
				name="autoIncrement"
				value="1"
				{if $autoIncrement} checked="checked"{/if}
			/>
			
			<small>{lang}wcf.project.database.column.autoIncrement.description{/lang}</small>
		</dd>
	</dl>
	
	{event name='data'}
</fieldset>

<fieldset id="defaultContainer">
	<legend>{lang}wcf.project.database.column.default{/lang}</legend>
	
	<dl id="noDefaulContainer">
		<dt class="reversed">
			<label for="noDefault">
				{lang}wcf.project.database.column.noDefault{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="checkbox"
				id="noDefault"
				name="noDefault"
				value="1"
				{if $noDefault} checked="checked"{/if}
			/>
			
			<small>{lang}wcf.project.database.column.noDefault.description{/lang}</small>
		</dd>
	</dl>
	
	<dl{if $errorType.default|isset} class="formError"{/if}>
		<dt>
			<label for="default">
				{lang}wcf.project.database.column.default{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="text"
				id="default"
				name="default"
				value="{$default}"
				class="medium"
			/>
			
			{if $errorType.default|isset}
				<small class="innerError">
					{lang}wcf.project.error.database.column.default.{$errorType.default}{/lang}
				</small>
			{/if}
			
			<small>{lang}wcf.project.database.column.default.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="defaultNullContainer">
		<dt class="reversed">
			<label for="defaultNull">
				{lang}wcf.project.database.column.defaultNull{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="checkbox"
				id="defaultNull"
				name="defaultNull"
				value="1"
				{if $defaultNull} checked="checked"{/if}
			/>
			
			<small>{lang}wcf.project.database.column.defaultNull.description{/lang}</small>
		</dd>
	</dl>
	
	<dl id="defaultCurrentTimestampContainer">
		<dt class="reversed">
			<label for="defaultCurrentTimestamp">
				{lang}wcf.project.database.column.defaultCurrentTimestamp{/lang}
			</label>
		</dt>
		<dd>
			<input
				type="checkbox"
				id="defaultCurrentTimestamp"
				name="defaultCurrentTimestamp"
				value="1"
				{if $defaultCurrentTimestamp} checked="checked"{/if}
			/>
			
			<small>{lang}wcf.project.database.column.defaultCurrentTimestamp.description{/lang}</small>
		</dd>
	</dl>
	
	{event name='default'}
</fieldset>