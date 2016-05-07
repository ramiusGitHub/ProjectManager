{include file='header' pageTitle='wcf.project.templateListener.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.templateListener.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#templateListener{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectTemplateListenerAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.templateListener.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.templateListener.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectTemplateListenerAdd'}{/link}{else}{link controller='ProjectTemplateListenerEdit' id=$listenerID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl>
				<dt class="reversed">
					<label for="environment">
						{lang}wcf.project.templateListener.environment{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="environment"
						name="environment"
						value="1"
						{if $environment == 'admin'}checked="checked"{/if}
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
				
					<small>{lang}wcf.project.templateListener.environment.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.name|isset} class="formError"{/if}>
				<dt>
					<label for="name">
						{lang}wcf.project.templateListener.name{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="name"
						id="name"
						value="{$name}"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.name|isset}
						<small class="innerError">
							{if $errorType.name == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.name.{@$errorType.name}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.templateListener.name.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.listenerTemplateName|isset} class="formError"{/if}>
				<dt>
					<label for="listenerTemplateName">
						{lang}wcf.project.templateListener.listenerTemplateName{/lang}
					</label>
				</dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="listenerTemplateName"
						id="listenerTemplateName"
						value="{$listenerTemplateName}"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.listenerTemplateName|isset}
						<small class="innerError">
							{if $errorType.listenerTemplateName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.listenerTemplateName.{@$errorType.listenerTemplateName}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.templateListener.listenerTemplateName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.eventName|isset} class="formError"{/if}>
				<dt>
					<label for="eventName">
						{lang}wcf.project.templateListener.eventName{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="eventName"
						id="eventName"
						value="{$eventName}"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.eventName|isset}
						<small class="innerError">
							{if $errorType.eventName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.eventName.{@$errorType.eventName}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.templateListener.eventName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.templateCode|isset} class="formError"{/if}>
				<dt>
					<label for="templateCode">
						{lang}wcf.project.templateListener.templateCode{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="templateCode"
						id="templateCode"
						rows="5"
					>{$templateCode}</textarea>
					
					{if $errorType.templateCode|isset}
						<small class="innerError">
							{if $errorType.templateCode == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.templateCode.{@$errorType.templateCode}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.templateListener.templateCode.description{/lang}</small>
				</dd>
			</dl>
			<script data-relocate="true">
				//<![CDATA[
					var $__extendedCodeMirrorConfig = {
						lineWrapping: false,
					};
				// ]]>
			</script>
			{include file='codemirror' codemirrorMode='smarty' codemirrorSelector='#templateCode' __extendedCodeMirrorFullscreen=true __extendedCodeMirrorConfig=true}
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.templateListener.options{/lang}</legend>
			
			<dl{if $errorType.niceValue|isset} class="formError"{/if}>
				<dt>
					<label for="niceValue">
						{lang}wcf.project.templateListener.niceValue{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="number"
						min="-128"
						max="127"
						class="inputText"
						name="niceValue"
						id="niceValue"
						value="{$niceValue}"
					/>
					
					{if $errorType.niceValue|isset}
						<small class="innerError">{lang}wcf.project.templateListener.niceValue.{$errorType.niceValue}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.templateListener.niceValue.description{/lang}</small>
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