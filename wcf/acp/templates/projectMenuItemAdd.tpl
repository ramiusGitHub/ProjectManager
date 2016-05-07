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

<form method="post" action="{if $action == 'add'}{link controller='Project'|concat:$ucType:'Add'}{/link}{else}{link controller='Project'|concat:$ucType:'Edit' id=$itemID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.menuItem|isset} class="formError"{/if}>
				<dt>
					<label for="menuItem">
						{lang}wcf.project.menuItem.menuItem{/lang}
					</label>
				</dt>
				<dd>
					{if $type == 'acpMenuItem'}
						{assign var=menuItemPattern value="[a-z][a-z0-9]*\.acp\.menu\.link(\.[a-zA-Z][a-zA-Z0-9]*)+"}
					{else}
						{if $type == 'userMenuItem'}
							{assign var=menuItemPattern value="[a-z][a-z0-9]*\.user\.menu(\.[a-zA-Z][a-zA-Z0-9]*)+"}
						{else}
							{if $type == 'pageMenuItem'}
								{assign var=menuItemPattern value="[a-z][a-z0-9]*\.[a-zA-Z]+(\.[a-zA-Z][a-zA-Z0-9]*)+"}
							{else}
								{* userProfileMenuItem *}
								{assign var=menuItemPattern value=".+"}
							{/if}
						{/if}
					{/if}
					
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="menuItem"
						id="menuItem"
						value="{$menuItem}"
						pattern="{@$menuItemPattern}"
						required="required"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.menuItem|isset}
						<small class="innerError">
							{if $errorType.menuItem == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.menuItem.{@$errorType.menuItem}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.{@$type}.menuItem.description{/lang}</small>
				</dd>
			</dl>

			<dl{if $errorType.displayedName|isset} class="formError"{/if}>
				<dt>
					<label for="displayedName">
						{lang}wcf.project.menuItem.displayedName{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						id="displayedName"
						name="displayedName"
						value="{$i18nPlainValues['displayedName']}"
						class="long"
					/>
					
					{if $errorType.displayedName|isset}
						<small class="innerError">
							{if $errorType.displayedName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{elseif $errorType.displayedName == 'multilingual'}
								{lang}wcf.global.form.error.multilingual{/lang}
							{else}
								{lang}wcf.project.error.menuItem.displayedName.{@$errorType.displayedName}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.menuItem.displayedName.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='displayedName' forceSelection=true}
			
			{if $menuPosition|isset}
				<dl>
					<dt>
						<label for="menuPosition">
							{lang}wcf.project.menuItem.menuPosition{/lang}
						</label>
					</dt>
					<dd>
						<select name="menuPosition" id="menuPosition">
							<option value="header"{if $menuPosition == 'header'} selected="selected"{/if}>{lang}wcf.project.menuItem.menuPosition.header{/lang}</option>
							<option value="footer"{if $menuPosition == 'footer'} selected="selected"{/if}>{lang}wcf.project.menuItem.menuPosition.footer{/lang}</option>
						</select>
					</dd>
				</dl>
				
				<script data-relocate="true">
					//<![CDATA[
					$('#menuPosition').change(function() {
						$('#parentMenuItem').prop("disabled", $(this).val() == 'footer');
					});
					//]]>
				</script>
			{/if}
			
			{if $parentMenuItem|isset}
				<dl{if $errorType.parentMenuItemID|isset} class="formError"{/if}>
					<dt>
						<label for="parentMenuItem">
							{lang}wcf.project.menuItem.parentMenuItem{/lang}
						</label>
					</dt>
					<dd>
						{htmlOptions options=$parentMenuItems selected=$parentMenuItem name='parentMenuItem' id='parentMenuItem'}
						
						{if $type == 'acpMenuItem'}
							<script data-relocate="true">
								//<![CDATA[
								$(function() {
									$select = $('select[name="parentMenuItem"]');
									$select.change(function() {
										$selected = $(this).find('option:selected');
										$disabled = ($selected.parents('optgroup').length == 0);
										
										$('#menuItemController').prop("disabled", $disabled);
										$('#menuItemLink').prop("disabled", $disabled);
									});
									$select.change();
								});
								//]]>
							</script>
						{/if}
						
						{if $errorType.parentMenuItem|isset}
							<small class="innerError">{lang}wcf.project.error.$errorType.parentMenuItem.{@$errorType.parentMenuItemID}{/lang}</small>
						{/if}
						
						<small>{lang}wcf.project.menuItem.parentMenuItem.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $className|isset}
				<dl{if $errorType.className|isset} class="formError"{/if}>
					<dt>
						<label for="className">
							{lang}wcf.project.{@$type}.className{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							class="inputText long"
							name="className"
							id="className"
							value="{$className}"
						/>
						
						{if $errorType.className|isset}
							<small class="innerError">
								{if $errorType.className == 'allEmpty'}
									{lang}wcf.project.error.{@$type}.allEmpty{/lang}
								{else}
									{lang}wcf.project.error.className.{@$errorType.className}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.{@$type}.className.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $menuItemController|isset}
				<dl{if $errorType.menuItemController|isset} class="formError"{/if}>
					<dt>
						<label for="menuItemController">
							{lang}wcf.project.menuItem.menuItemController{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							class="inputText long"
							name="menuItemController"
							id="menuItemController"
							value="{$menuItemController}"
						/>
						
						{if $errorType.menuItemController|isset}
							<small class="innerError">
								{if $errorType.menuItemController == 'allEmpty'}
									{lang}wcf.project.error.{@$type}.allEmpty{/lang}
								{else}
									{if $errorType.menuItemController == 'nonExistent'}
										{lang}wcf.project.error.className.nonExistent{/lang}
									{else}
										{lang}wcf.project.error.menuItemController.{@$errorType.menuItemController}{/lang}
									{/if}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.menuItem.menuItemController.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $menuItemLink|isset}
				<dl{if $errorType.menuItemLink|isset} class="formError"{/if}>
					<dt>
						<label for="menuItemLink">
							{lang}wcf.project.menuItem.menuItemLink{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							class="inputText long"
							name="menuItemLink"
							id="menuItemLink"
							value="{$menuItemLink}"
						/>
						
						{if $errorType.menuItemLink|isset}
							<small class="innerError">
								{if $errorType.menuItemLink == 'allEmpty'}
									{lang}wcf.project.error.{@$type}.allEmpty{/lang}
								{else}
									{lang}wcf.project.error.menuItemLink.{@$errorType.menuItemLink}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.menuItem.menuItemLink.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $iconClassName|isset}
				<dl{if $errorType.iconClassName|isset} class="formError"{/if}>
					<dt>
						<label for="iconClassName">
							{lang}wcf.project.menuItem.iconClassName{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							class="inputText long"
							name="iconClassName"
							id="iconClassName"
							value="{$iconClassName}"
						/>
						
						{if $errorType.iconClassName|isset}
							<small class="innerError">{lang}wcf.project.error.menuItem.iconClassName.{@$errorType.iconClassName}{/lang}</small>
						{/if}
						
						<small>{lang}wcf.project.menuItem.iconClassName.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.menuItem.additional{/lang}</legend>

			<dl>
				<dt>
					<label for="showOrder">{lang}wcf.project.menuItem.showOrder{/lang}</label>
				</dt>
				<dd>
					<input
						type="number"
						min="0"
						name="showOrder"
						id="showOrder"
						value="{$showOrder}"
					/>
					
					<small>{lang}wcf.project.menuItem.showOrder.description{/lang}</small>
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
			
			<dl{if $errorType.permissions|isset} class="formError"{/if}>
				<dt>
					<label for="permissions">
						{lang}wcf.project.menuItem.permissions{/lang}
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
								{lang}wcf.project.error.menuItem.permissions.{@$errorType.permissions}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.menuItem.permissions.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.options|isset} class="formError"{/if}>
				<dt>
					<label for="options">
						{lang}wcf.project.menuItem.options{/lang}
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
								{lang}wcf.project.error.menuItem.options.{@$errorType.options}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.menuItem.options.description{/lang}</small>
				</dd>
			</dl>
			
			{if $isDisabled|isset}
				<dl>
					<dt class="reversed">
						<label for="isDisabled">
							{lang}wcf.project.menuItem.isDisabled{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="isDisabled"
							name="isDisabled"
							value="1"
							{if $isDisabled} checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.menuItem.isDisabled.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{if $isLandingPage|isset}
				<dl>
					<dt class="reversed">
						<label for="isLandingPage">
							{lang}wcf.project.menuItem.isLandingPage{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="isLandingPage"
							name="isLandingPage"
							value="1"
							{if $isLandingPage} checked="checked"{/if}
						/>
					
						<small>{lang}wcf.project.menuItem.isLandingPage.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
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