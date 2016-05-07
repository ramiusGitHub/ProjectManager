{include file='header' pageTitle='wcf.project.languageVariable.'|concat:$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.languageVariable.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#languageVariable{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectLanguageVariableAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.languageVariable.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.languageVariable.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectLanguageVariableAdd'}{/link}{else}{link controller='ProjectLanguageVariableEdit'}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}

		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>

			<dl{if $errorType.languageCategoryID|isset} class="formError"{/if}>
				<dt><label for="languageCategoryID">{lang}wcf.project.languageVariable.category{/lang}</label></dt>
				<dd>
					{htmlOptions id='languageCategoryID' name='languageCategoryID' options=$languageCategories selected=$languageCategoryID}
					
					{if $errorType.languageCategoryID|isset}
						<small class="innerError">
							{if $errorType.languageCategoryID == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.languageVariable.error.{$errorType.languageCategoryID}{/lang}
							{/if}
						</small>
					{/if}
				
					<small>{lang}wcf.project.languageVariable.category.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.newLanguageCategory|isset} class="formError"{/if}>
				<dt><label for="newLanguageCategory">{lang}wcf.project.languageVariable.newCategory{/lang}</label></dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						pattern="\s*[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+){literal}{1,2}{/literal}\s*"
						name="newLanguageCategory"
						id="newLanguageCategory"
						value="{$newLanguageCategory}"
					/>
					
					{if $errorType.newLanguageCategory|isset}
						<small class="innerError">
							{lang}wcf.project.languageVariable.error.{$errorType.newLanguageCategory}{/lang}
						</small>
					{/if}
				
					<small>{lang}wcf.project.languageVariable.newCategory.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.languageItemName|isset} class="formError"{/if}>
				<dt><label for="languageItemName">{lang}wcf.project.languageVariable.name{/lang}</label></dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						pattern="\s*[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)*\s*"
						name="languageItemName"
						id="languageItemName"
						value="{$languageItemName}"
						required="required"
						autofocus="autofocus"
					/>
					
					{if $errorType.languageItemName|isset}
						<small class="innerError">
							{if $errorType.languageItemName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.languageVariable.error.{$errorType.languageItemName}{/lang}
							{/if}
						</small>
					{/if}
				
					<small>{lang}wcf.project.languageVariable.name.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
	
		<fieldset>
			<legend>{lang}wcf.project.languageVariable.value{/lang}</legend>
			
			{foreach from=$languages key=$languageID item=language}
				<dl>
					<dt><label for="language-{@$languageID}"><img src="{@$language->getIconPath()}" alt="" /> <span>{$language}</span></label></dt>
					<dd>
						<textarea
							rows="4"
							cols="40"
							name="languageItemValues[{@$languageID}]"
							id="language-{@$languageID}"
						>{if $languageItemValues.$languageID|isset}{$languageItemValues.$languageID}{/if}</textarea>
						
						{if $errorType.languageItemValues.$languageID|isset}
							<small class="innerError">
								{lang}wcf.project.languageVariable.error.{@$errorType.languageItemValues.$languageID}{/lang}
							</small>
						{/if}
						
						{assign var=languageDescriptionVariable value="wcf.project.languageVariable.description."|concat:$language->languageCode}
						{capture assign=languageDescription}{lang}{@$languageDescriptionVariable}{/lang}{/capture}
						{if $languageDescription != $languageDescriptionVariable}
							<small>{@$languageDescription}</small>
						{/if}
					</dd>
				</dl>
			{/foreach}
			
			{event name='value'}
		</fieldset>
		
		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@$packageID}" />
		{if $action == 'edit'}<input type="hidden" value="{$refLanguageItemName}" name="refLanguageItemName" />{/if}
		<input type="hidden" value="{$languageCategoryID}" name="refLanguageCategoryID" />
 		{@SECURITY_TOKEN_INPUT_TAG}
 	</div>
</form>

{include file='footer'}