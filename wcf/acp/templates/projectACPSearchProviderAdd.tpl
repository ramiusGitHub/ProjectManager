{include file='header' pageTitle='wcf.project.acpSearchProvider.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.acpSearchProvider.{@$action}{/lang}</h1>
</header>

{if $errorType.projectVersion|isset}
	<p class="error">{lang}wcf.project.error.projectVersion.{@$errorType.projectVersion}{/lang}</p>
{else}
	{if $errorType.duplicate|isset}
		<p class="error">{lang}wcf.project.error.duplicate{/lang}</p>
	{else}
		{include file='formError'}
	{/if}
{/if}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#acpSearchProvider{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectACPSearchProviderAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.acpSearchProvider.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.acpSearchProvider.add{/lang}</span></a></li>{/if}
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectACPSearchProviderAdd'}{/link}{else}{link controller='ProjectACPSearchProviderEdit' id=$providerID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.providerName|isset} class="formError"{/if}>
				<dt><label for="providerName">{lang}wcf.project.acpSearchProvider.providerName{/lang}</label></dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="providerName"
						id="providerName"
						value="{$providerName}"
						{if $action == 'edit' && $project->getCurrentVersion->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.providerName|isset}
						<small class="innerError">
							{if $errorType.providerName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.providerName.{@$errorType.providerName}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.acpSearchProvider.providerName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.displayedName|isset} class="formError"{/if}>
				<dt><label for="displayedName">{lang}wcf.project.acpSearchProvider.displayedName{/lang}</label></dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="displayedName"
						id="displayedName"
						value="{$i18nPlainValues['displayedName']}"
					/>
					
					{if $errorType.displayedName|isset}
						<p class="innerError">
							{if $errorType.displayedName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.displayedName.{@$errorType.displayedName}{/lang}{/if}
						</p>
					{/if}
				
					<small>{lang}wcf.project.acpSearchProvider.displayedName.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='displayedName' forceSelection=true}
			
			<dl{if $errorType.className|isset} class="formError"{/if}>
				<dt><label for="className">{lang}wcf.project.acpSearchProvider.className{/lang}</label></dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="className"
						id="className"
						value="{$className}"
					/>
					
					{if $errorType.className|isset}
						<small class="innerError">
							{if $errorType.className == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.className == 'notFound'}
									{lang}wcf.project.error.className.notFound{/lang}
								{else}
									{lang}wcf.project.error.className.{@$errorType.className}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.acpSearchProvider.className.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.acpSearchProvider.options{/lang}</legend>

			<dl>
				<dt>
					<label for="showOrder">{lang}wcf.project.acpSearchProvider.showOrder{/lang}</label>
				</dt>
				<dd>
					<input
						type="number"
						min="0"
						name="showOrder"
						id="showOrder"
						value="{$showOrder}"
					/>
					
					<small>{lang}wcf.project.acpSearchProvider.showOrder.description{/lang}</small>
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
			
			{event name='options'}
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