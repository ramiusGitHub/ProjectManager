{include file='header' pageTitle='wcf.project.eventListener.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.eventListener.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#eventListener{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectEventListenerAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.eventListener.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.eventListener.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectEventListenerAdd'}{/link}{else}{link controller='ProjectEventListenerEdit' id=$listenerID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl>
				<dt class="reversed">
					<label for="environment">
						{lang}wcf.project.eventListener.environment{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="environment"
						name="environment"
						value="1"
						{if $environment == 'admin'}checked="checked"{/if}
						{if $action == 'edit' && !$project->isNewEventListener($object->getObjectID())}disabled="disabled"{/if}
					/>
				
					<small>{lang}wcf.project.eventListener.environment.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.eventClassName|isset} class="formError"{/if}>
				<dt>
					<label for="eventClassName">
						{lang}wcf.project.eventListener.eventClassName{/lang}
					</label>
				</dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="eventClassName"
						id="eventClassName"
						value="{$eventClassName}"
						{if $action == 'edit' && !$project->isNewEventListener($object->getObjectID())}disabled="disabled"{/if}
					/>
					
					{if $errorType.eventClassName|isset}
						<small class="innerError">
							{if $errorType.eventClassName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.eventClassName == 'notFound'}
									{lang}wcf.project.error.className.notFound{/lang}
								{else}
									{lang}wcf.project.error.eventClassName.{@$errorType.eventClassName}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.eventListener.eventClassName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.eventName|isset} class="formError"{/if}>
				<dt>
					<label for="eventName">
						{lang}wcf.project.eventListener.eventName{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="eventName"
						id="eventName"
						value="{$eventName}"
						{if $action == 'edit' && !$project->isNewEventListener($object->getObjectID())}disabled="disabled"{/if}
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
					
					<small>{lang}wcf.project.eventListener.eventName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.listenerClassName|isset} class="formError"{/if}>
				<dt>
					<label for="listenerClassName">
						{lang}wcf.project.eventListener.listenerClassName{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="listenerClassName"
						id="listenerClassName"
						value="{$listenerClassName}"
						{if $action == 'edit' && !$project->isNewEventListener($object->getObjectID())}disabled="disabled"{/if}
					/>
					
					{if $errorType.listenerClassName|isset}
						<small class="innerError">
							{if $errorType.listenerClassName == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.listenerClassName == 'notFound'}
									{lang}wcf.project.error.className.notFound{/lang}
								{else}
									{lang}wcf.project.error.listenerClassName.{@$errorType.listenerClassName}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.eventListener.listenerClassName.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.eventListener.options{/lang}</legend>
			
			<dl>
				<dt class="reversed">
					<label for="inherit">
						{lang}wcf.project.eventListener.inherit{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="inherit"
						name="inherit"
						value="1"
						{if $inherit}checked="checked"{/if}
					/>
				
					<small>{lang}wcf.project.eventListener.inherit.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.niceValue|isset} class="formError"{/if}>
				<dt>
					<label for="niceValue">
						{lang}wcf.project.eventListener.niceValue{/lang}
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
						<small class="innerError">{lang}wcf.project.eventListener.niceValue.{$errorType.niceValue}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.eventListener.niceValue.description{/lang}</small>
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