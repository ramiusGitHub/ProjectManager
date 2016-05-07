{include file='header' pageTitle='wcf.project.version.'|concat:$action:'.title'}

<div class="info">{lang}wcf.project.version.add.info{/lang}</div>

<header class="boxHeadline">
	<h1>{lang}wcf.project.version.{@$action}.title{/lang}</h1>
</header>

{if !$errorField|empty}
	{if $errorField == 'beta'}
		<p class="error">{$errorType}</p>
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
			<li><a href="{link controller='ProjectVersionList' id=$project->packageID}{/link}" class="button {if $action != 'edit'}buttonPrimary{/if}"><span class="icon icon16 fa-code-fork"></span> <span>{lang}wcf.project.button.versions{/lang}</span></a></li>

			{if $action != 'add'}
				<li><a href="{link controller='ProjectVersionAdd'}packageID={@$project->packageID}{/link}" class="button buttonPrimary"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.project.version.add{/lang}</span></a></li>
			{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectVersionAdd'}{/link}{else}{link controller='ProjectVersionEdit' id=$version->getObjectID()}{/link}{/if}">
	<div class="container containerPadding marginTop">		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorField == 'version'} class="formError"{/if}>
				<dt>
					<label for="environment">
						{lang}wcf.project.package.packageVersion{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						id="targetVersion"
						name="targetVersion"
						value="{$targetVersion}"
						autofocus="autofocus"
					/>
						
					{if $errorField == 'version'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.version.number.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
				
					<small>{lang}wcf.project.package.packageVersion.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		{event name='fieldsets'}
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@$project->packageID}" />
 		{@SECURITY_TOKEN_INPUT_TAG}
 	</div>
</form>

{include file='footer'}