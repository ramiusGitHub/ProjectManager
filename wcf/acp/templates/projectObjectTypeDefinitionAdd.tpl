{include file='header' pageTitle='wcf.project.objectTypeDefinition.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.objectTypeDefinition.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#objectTypeDefinition{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectObjectTypeDefinitionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.objectTypeDefinition.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.objectTypeDefinition.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectObjectTypeDefinitionAdd'}{/link}{else}{link controller='ProjectObjectTypeDefinitionEdit' id=$definitionID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.definitionName|isset} class="formError"{/if}>
				<dt><label for="definitionName">{lang}wcf.project.objectTypeDefinition.definitionName{/lang}</label></dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="definitionName"
						id="definitionName"
						value="{$definitionName}" 
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					{if $errorType.definitionName|isset}
						<small class="innerError">
							{if $errorType.definitionName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.definitionName.{@$errorType.definitionName}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.objectTypeDefinition.definitionName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.interfaceName|isset} class="formError"{/if}>
				<dt><label for="interfaceName">{lang}wcf.project.objectTypeDefinition.interfaceName{/lang}</label></dt>
				<dd>
					<input type="text" class="inputText long" name="interfaceName" id="interfaceName" value="{$interfaceName}" />
					{if $errorType.interfaceName|isset}
						<small class="innerError">
							{if $errorType.interfaceName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.interfaceName.{@$errorType.interfaceName}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.objectTypeDefinition.interfaceName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.categoryName|isset} class="formError"{/if}>
				<dt><label for="categoryName">{lang}wcf.project.objectTypeDefinition.categoryName{/lang}</label></dt>
				<dd>
					<input type="text" class="inputText" name="categoryName" id="categoryName" value="{$categoryName}" />
					{if $errorType.categoryName|isset}
						<small class="innerError">
							{if $errorType.categoryName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.categoryName.{@$errorType.categoryName}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.objectTypeDefinition.categoryName.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
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