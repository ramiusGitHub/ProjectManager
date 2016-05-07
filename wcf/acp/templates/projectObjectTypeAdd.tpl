{include file='header' pageTitle='wcf.project.objectType.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.objectType.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#objectType{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectObjectTypeAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.objectType.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.objectType.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectObjectTypeAdd'}{/link}{else}{link controller='ProjectobjectTypeEdit' id=$objectID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.objectType|isset} class="formError"{/if}>
				<dt><label for="objectType">{lang}wcf.project.objectType.objectType{/lang}</label></dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="long"
						name="objectType"
						id="objectType"
						value="{$objectType}"
						required="required"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					{if $errorType.objectType|isset}
						<small class="innerError">
							{if $errorType.objectType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.objectType.objectType.{@$errorType.objectType}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.objectType.objectType.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.definitionID|isset} class="formError"{/if}>
				<dt>
					<label for="definitionID">{lang}wcf.project.objectType.definition{/lang}</label>
				</dt>
				<dd>
					<select id="definitionID" name="definitionID"{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID} disabled="disabled"{/if}>
						{foreach from=$definitions item=$definition}
							<option{if $definition->definitionID == $definitionID} selected="selected"{/if} value="{@$definition->definitionID}">{$definition->definitionName}</option>
						{/foreach}
					</select>
					
					{if $errorType.definitionID|isset}
						<small class="innerError">{lang}wcf.project.error.objectType.definitionID.{@$errorType.definitionID}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.objectType.definition.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.className|isset} class="formError"{/if}>
				<dt><label for="className">{lang}wcf.project.objectType.className{/lang}</label></dt>
				<dd>
					<input
						type="text"
						class="long"
						name="className"
						id="className"
						value="{$className}"
					/>
					
					{if $errorType.className|isset}
						<small class="innerError">
							{if $errorType.className == 'nonExistent'}
								{lang}wcf.project.error.className.nonExistent{/lang}
							{else}
								{lang}wcf.project.error.objectType.className.{@$errorType.className}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.objectType.className.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.additionalData|isset} class="formError"{/if}>
				<dt>
					<label for="additionalData">{lang}wcf.project.objectType.additionalData{/lang}</label>
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
							{lang}wcf.project.error.objectType.additionalData.{@$errorType.additionalData}{/lang}
						</small>
					{/if}
					
					<small>{lang}wcf.project.objectType.additionalData.description{/lang}</small>
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