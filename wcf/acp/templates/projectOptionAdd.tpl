{include file='header' pageTitle='wcf.project.'|concat:$type:'.':$action}

{assign var=ucType value=$type|ucfirst}
{assign var=ucAction value=$action|ucfirst}

<header class="boxHeadline">
	<h1>{lang}wcf.project.{@$type}.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#{@$type}{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='Project'|concat:$ucType:'Add'}packageID={@$packageID}{/link}" title="{lang}wcf.project.{@$type}.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.{@$type}.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='Project'|concat:$ucType:'Add'}{/link}{else}{link controller='Project'|concat:$ucType:'Edit' id=$optionID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.optionName|isset} class="formError"{/if}>
				<dt>
					<label for="optionName">
						{lang}wcf.project.option.optionName{/lang}
					</label>
				</dt>
				<dd>
					{if $type == 'option'}
						{assign var=optionNamePattern value="[a-zA-Z_][a-zA-Z0-9_]*"}
					{else}
						{if $type == 'userOption'}
							{assign var=optionNamePattern value="[a-zA-Z0-9]+"}
						{else}
							{* userGroupOption *}
							{assign var=optionNamePattern value="[a-zA-Z0-9\.]+"}
						{/if}
					{/if}
					
					<input
						autofocus="autofocus"
						type="text"
						class="long"
						name="optionName"
						id="optionName"
						value="{$optionName}"
						required="required"
						pattern="{@$optionNamePattern}"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.optionName|isset}
						<small class="innerError">
							{if $errorType.optionName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.optionName.{@$errorType.optionName}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.option.{@$type}Name.description{/lang}</small>
				</dd>
			</dl>

			<dl{if $errorType.displayedName|isset} class="formError"{/if}>
				<dt><label for="displayedName">{lang}wcf.project.option.displayedName{/lang}</label></dt>
				<dd>
					<input
						type="text"
						id="displayedName"
						name="displayedName"
						value=""
						class="long"
					/>
					
					{if $errorType.displayedName|isset}
						<small class="innerError">
							{if $errorType.displayedName == 'multilingual'}
								{lang}wcf.global.form.error.multilingual{/lang}
							{else}
								{lang}wcf.project.option.displayedName.error.{@$errorType.displayedName}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.option.displayedName.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='displayedName' forceSelection=true}

			<dl{if $errorType.displayedDescription|isset} class="formError"{/if}>
				<dt>
					<label for="displayedDescription">
						{lang}wcf.project.option.displayedDescription{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						id="displayedDescription"
						name="displayedDescription"
						cols="40"
						rows="10">
					</textarea>
					
					{if $errorType.displayedDescription|isset}
						<small class="innerError">
							{if $errorType.displayedDescription == 'multilingual'}
								{lang}wcf.global.form.error.multilingual{/lang}
							{else}
								{lang}wcf.project.option.displayedDescription.error.{@$errorType.displayedDescription}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}wcf.project.option.displayedDescription.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='displayedDescription' forceSelection=true}
			
			<dl{if $errorType.optionCategoryID|isset} class="formError"{/if}>
				<dt>
					<label for="optionCategoryID">
						{lang}wcf.project.option.category{/lang}
					</label>
				</dt>
				<dd>
					{htmlOptions options=$optionCategories selected=$optionCategoryID name='optionCategoryID'}
					
					{if $errorType.optionCategoryID|isset}
						<small class="innerError">
							{lang}wcf.project.error.optionName.{@$errorType.optionCategoryID}{/lang}
						</small>	
					{/if}
					
					<small>{lang}wcf.project.option.category.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.showOrder|isset} class="formError"{/if}>
				<dt><label for="showOrder">{lang}wcf.project.option.showOrder{/lang}</label></dt>
				<dd>
					<input
						type="number"
						min="0"
						name="showOrder"
						id="showOrder"
						{if $hidden|isset && $hidden}
							value="0"
							disabled="disabled"
						{else}
							value="{$showOrder}"
						{/if}
					/>
					
					{if $errorType.showOrder|isset}
						<small class="innerError">{lang}wcf.project.error.showOrder.{@$errorType.showOrder}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.option.showOrder.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed">
					<label for="autoShowOrder">
						{lang}wcf.project.autoShowOrder{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="autoShowOrder"
						name="autoShowOrder"
						value="1"
						{if $autoShowOrder}checked="checked"{/if}
					/>
				
					<small>{lang}wcf.project.autoShowOrder.description{/lang}</small>
				</dd>
			</dl>
			
			{if $hidden|isset}
				<dl>
					<dt class="reversed">
						<label for="hidden">
							{lang}wcf.project.option.hidden{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="hidden"
							name="hidden"
							value="1"
							{if $hidden}checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.option.hidden.description{/lang}</small>
					</dd>
				</dl>
				
				<script data-relocate="true">
					//<![CDATA[
					$('#hidden').change(function() {
						$('#showOrder').prop("disabled", this.checked);
						$('#autoShowOrder').prop("disabled", this.checked);
						$('#displayedName').prop("disabled", this.checked);
						$('#displayedDescription').prop("disabled", this.checked);
					});
					$('#hidden').change();
					//]]>
				</script>
			{/if}
			
			<dl{if $errorType.optionType|isset} class="formError"{/if}>
				<dt>
					<label for="optionType">
						{lang}wcf.project.option.optionType{/lang}
					</label>
				</dt>
				<dd>
					{htmlOptions options=$optionTypes selected=$optionType name='optionType'}
					
					{if $errorType.optionType|isset}
						<small class="innerError">{lang}wcf.project.error.optionType.{@$errorType.optionType}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.option.optionType.description{/lang}</small>
				</dd>
			</dl>
			
			{if $outputClass|isset}
				<dl>
					<dt>
						<label for="outputClass">
							{lang}wcf.project.option.outputClass{/lang}
						</label>
					</dt>
					<dd>
						{htmlOptions options=$outputClasses selected=$outputClass name='outputClass'}
					
						{if $errorType.outputClass|isset}
							<small class="innerError">{lang}wcf.project.error.outputClass.{@$errorType.outputClass}{/lang}</small>
						{/if}
					
						<small>{lang}wcf.project.option.outputClass.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $usersOnly|isset}
				<dl>
					<dt class="reversed">
						<label for="usersOnly">
							{lang}wcf.project.option.usersOnly{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="usersOnly"
							name="usersOnly"
							value="1"
							{if $usersOnly}checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.option.usersOnly.description{/lang}</small>
					</dd>
				</dl>
			{/if}
		
			{event name='general'}
		</fieldset>
		
		
		<fieldset>
			<legend>{lang}wcf.project.option.defaultValue{/lang}</legend>
		
			{if $optionValue|isset}
				<dl>
					<dt>
						<label for="optionValue">
							{lang}wcf.project.option.optionValue{/lang}
						</label>
					</dt>
					<dd>
						<textarea
							name="optionValue"
							id="optionValue"
							rows="5"
							cols="40"
						>{$optionValue}</textarea>
						
						<small>{lang}wcf.project.option.optionValue.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			<dl>
				<dt>
					<label for="defaultValue">
						{lang}wcf.project.option.defaultValue{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="defaultValue"
						id="defaultValue"
						rows="5"
						cols="40"
					>{$defaultValue}</textarea>
					
					{if $errorType.defaultValue|isset}
						<small class="innerError">{lang}wcf.project.error.option.defaultValue.{@$errorType.defaultValue}{/lang}</small>
					{/if}
				
					<small>{lang}wcf.project.option.defaultValue.description{/lang}</small>
				</dd>
			</dl>
			
			{if $useUserDefaultValue|isset}
				<dl>
					<dt class="reversed">
						<label for="useUserDefaultValue">
							{lang}wcf.project.option.useUserDefaultValue{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="useUserDefaultValue"
							name="useUserDefaultValue"
							value="1"
							{if $useUserDefaultValue}checked="checked"{/if}
						/>
						
						<small>{lang}wcf.project.option.useUserDefaultValue.description{/lang}</small>
					</dd>
				</dl>
				
				<script data-relocate="true">
					//<![CDATA[
					$('#useUserDefaultValue').change(function() {
						$('#userDefaultValue').prop("disabled", !this.checked);
					});
					
					$('#useUserDefaultValue').change();
					//]]>
				</script>
				
				<dl>
					<dd>
						<textarea
							name="userDefaultValue"
							id="userDefaultValue"
							rows="5"
							cols="40"
						>{$userDefaultValue}</textarea>
					
						<small>{lang}wcf.project.option.userDefaultValue.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $useModDefaultValue|isset}
				<dl>
					<dt class="reversed">
						<label for="useModDefaultValue">
							{lang}wcf.project.option.useModDefaultValue{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="useModDefaultValue"
							name="useModDefaultValue"
							value="1"
							{if $useModDefaultValue}checked="checked"{/if}
						/>
						
						<small>{lang}wcf.project.option.useModDefaultValue.description{/lang}</small>
					</dd>
				</dl>
				
				<script data-relocate="true">
					//<![CDATA[
					$('#useModDefaultValue').change(function() {
						$('#modDefaultValue').prop("disabled", !this.checked);
					});
					
					$('#useModDefaultValue').change();
					//]]>
				</script>
				
				<dl>
					<dd>
						<textarea
							name="modDefaultValue"
							id="modDefaultValue"
							rows="5"
							cols="40"
						>{$modDefaultValue}</textarea>
					
						<small>{lang}wcf.project.option.modDefaultValue.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $useAdminDefaultValue|isset}
				<dl>
					<dt class="reversed">
						<label for="useAdminDefaultValue">
							{lang}wcf.project.option.useAdminDefaultValue{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="useAdminDefaultValue"
							name="useAdminDefaultValue"
							value="1"
							{if $useAdminDefaultValue}checked="checked"{/if}
						/>
						
						<small>{lang}wcf.project.option.useAdminDefaultValue.description{/lang}</small>
					</dd>
				</dl>
				
				<script data-relocate="true">
					//<![CDATA[
					$('#useAdminDefaultValue').change(function() {
						$('#adminDefaultValue').prop("disabled", !this.checked);
					});
					
					$('#useAdminDefaultValue').change();
					//]]>
				</script>
				
				<dl>
					<dd>
						<textarea
							name="adminDefaultValue"
							id="adminDefaultValue"
							rows="5"
							cols="40"
						>{$adminDefaultValue}</textarea>
					
						<small>{lang}wcf.project.option.adminDefaultValue.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{event name='defaultValue'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.option.additional{/lang}</legend>
			
			<dl>
				<dt>
					<label for="validationPattern">
						{lang}wcf.project.option.validationPattern{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="validationPattern"
						id="validationPattern"
						value="{$validationPattern}"
					/>
				
					<small>{lang}wcf.project.option.validationPattern.description{/lang}</small>
				</dd>
			</dl>
			
			{if $selectOptions|isset}
				<dl>
					<dt>
						<label for="selectOptions">
							{lang}wcf.project.option.selectOptions{/lang}
						</label>
					</dt>
					<dd>
						<textarea
							name="selectOptions"
							id="selectOptions"
							rows="5"
							cols="40"
							pattern=""
						>{$selectOptions}</textarea>
					
						<small>{lang}wcf.project.option.selectOptions.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			<dl>
				<dt>
					<label for="enableOptions">
						{lang}wcf.project.option.enableOptions{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="enableOptions"
						id="enableOptions"
						rows="5"
						cols="40"
					>{$enableOptions}</textarea>
				
					<small>{lang}wcf.project.option.enableOptions.description{/lang}</small>
				</dd>
			</dl>
			
			{if $required|isset}
				<dl>
					<dt class="reversed">
						<label for="required">
							{lang}wcf.project.option.required{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="required"
							name="required"
							value="1"
							{if $required} checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.option.required.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $askDuringRegistration|isset}
				<dl>
					<dt class="reversed">
						<label for="askDuringRegistration">
							{lang}wcf.project.option.askDuringRegistration{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="askDuringRegistration"
							name="askDuringRegistration"
							value="1"
							{if $askDuringRegistration} checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.option.askDuringRegistration.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $editable|isset}
				<dl>
					<dt>
						<label for="editable">
							{lang}wcf.project.option.editable{/lang}
						</label>
					</dt>
					<dd>
						<select name="editable" id="editable">
							<option label="{lang}wcf.project.option.editable.none{/lang}" value="0"{if $editable == 0} selected="selected"{/if}>{lang}wcf.project.option.editable.none{/lang}</option>
							<option label="{lang}wcf.project.option.editable.owner{/lang}" value="1"{if $editable == 1} selected="selected"{/if}>{lang}wcf.project.option.editable.owner{/lang}</option>
							<option label="{lang}wcf.project.option.editable.admin{/lang}" value="2"{if $editable == 2} selected="selected"{/if}>{lang}wcf.project.option.editable.admin{/lang}</option>
							<option label="{lang}wcf.project.option.editable.ownerAndAdmin{/lang}" value="3"{if $editable == 3} selected="selected"{/if}>{lang}wcf.project.option.editable.ownerAndAdmin{/lang}</option>
						</select>
					
						{if $errorType.editable|isset}
							<small class="innerError">{lang}wcf.project.error.editable.{@$errorType.editable}{/lang}</small>
						{/if}
					
						<small>{lang}wcf.project.option.editable.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $visible|isset}
				<dl>
					<dt>
						<label for="visible">
							{lang}wcf.project.option.visible{/lang}
						</label>
					</dt>
					<dd>
						<select name="visible" id="visible">
							<option label="{lang}wcf.project.option.visible.none{/lang}" value="0"{if $visible == 0} selected="selected"{/if}>{lang}wcf.project.option.visible.none{/lang}</option>
							<option label="{lang}wcf.project.option.visible.owner{/lang}" value="1"{if $visible == 1} selected="selected"{/if}>{lang}wcf.project.option.visible.owner{/lang}</option>
							<option label="{lang}wcf.project.option.visible.admin{/lang}" value="2"{if $visible == 2} selected="selected"{/if}>{lang}wcf.project.option.visible.admin{/lang}</option>
							<option label="{lang}wcf.project.option.visible.ownerAndAdmin{/lang}" value="3"{if $visible == 3} selected="selected"{/if}>{lang}wcf.project.option.visible.ownerAndAdmin{/lang}</option>
							<option label="{lang}wcf.project.option.visible.registered{/lang}" value="4"{if $visible == 4} selected="selected"{/if}>{lang}wcf.project.option.visible.registered{/lang}</option>
							<option label="{lang}wcf.project.option.visible.all{/lang}" value="15"{if $visible == 15} selected="selected"{/if}>{lang}wcf.project.option.visible.all{/lang}</option>
						</select>
					
						{if $errorType.visible|isset}
							<small class="innerError">{lang}wcf.project.error.visible.{@$errorType.visible}{/lang}</small>
						{/if}
					
						<small>{lang}wcf.project.option.visible.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $searchable|isset}
				<dl>
					<dt class="reversed">
						<label for="searchable">
							{lang}wcf.project.option.searchable{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="searchable"
							name="searchable"
							value="1"
							{if $searchable} checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.option.searchable.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $defaultIsDisabled|isset}
				<dl>
					<dt class="reversed">
						<label for="defaultIsDisabled">
							{lang}wcf.project.option.defaultIsDisabled{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="defaultIsDisabled"
							name="defaultIsDisabled"
							value="1"
							{if $defaultIsDisabled} checked="checked"{/if}
						/>
						
						<small>{lang}wcf.project.option.defaultIsDisabled.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			<dl{if $errorType.permissions|isset} class="formError"{/if}>
				<dt>
					<label for="permissions">
						{lang}wcf.project.option.permissions{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="permissions"
						id="permissions"
						rows="5"
						cols="40"
					>{$permissions}</textarea>
					
					{if $errorType.permissions|isset}
						<small class="innerError">
							{if $errorType.permissions == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.option.permissions.{@$errorType.permissions}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.option.permissions.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.options|isset} class="formError"{/if}>
				<dt>
					<label for="options">
						{lang}wcf.project.option.options{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="options"
						id="options"
						rows="5"
						cols="40"
					>{$options}</textarea>
					
					{if $errorType.options|isset}
						<small class="innerError">
							{if $errorType.options == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.option.options.{@$errorType.options}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.option.options.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.additionalData|isset} class="formError"{/if}>
				<dt>
					<label for="additionalData">
						{lang}wcf.project.option.additionalData{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="additionalData"
						id="additionalData"
						rows="5"
						cols="40"
					>{$additionalData}</textarea>
					
					{if $errorType.additionalData|isset}
						<small class="innerError">
							{if $errorType.additionalData == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.option.additionalData.{@$errorType.additionalData}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.option.additionalData.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='additional'}
		</fieldset>
					
		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@$packageID}" />
 		{@SECURITY_TOKEN_INPUT_TAG}
 	</div>
</form>

{include file='footer'}