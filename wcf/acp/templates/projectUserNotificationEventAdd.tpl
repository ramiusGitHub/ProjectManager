{include file='header' pageTitle='wcf.project.userNotificationEvent.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.userNotificationEvent.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#userNotificationEvent{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectUserNotificationEventAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.userNotificationEvent.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.userNotificationEvent.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectUserNotificationEventAdd'}{/link}{else}{link controller='ProjectUserNotificationEventEdit' id=$listenerID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.eventName|isset} class="formError"{/if}>
				<dt>
					<label for="eventName">
						{lang}wcf.project.userNotificationEvent.eventName{/lang}
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
					
					<small>{lang}wcf.project.userNotificationEvent.eventName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.objectTypeID|isset} class="formError"{/if}>
				<dt>
					<label for="objectTypeID">
						{lang}wcf.project.userNotificationEvent.objectType{/lang}
					</label>
				</dt>
				<dd>
					{htmlOptions options=$objectTypes selected=$objectTypeID name='objectTypeID'}
					
					{if $errorType.objectTypeID|isset}
						<small class="innerError">{lang}wcf.project.error.objectTypeID.{@$errorType.objectTypeID}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.userNotificationEvent.objectType.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.className|isset} class="formError"{/if}>
				<dt>
					<label for="className">
						{lang}wcf.project.userNotificationEvent.className{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="className"
						id="className"
						value="{$className}"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.className|isset}
						<small class="innerError">
							{if $errorType.className == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.className.{@$errorType.className}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.userNotificationEvent.className.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.userNotificationEvent.options{/lang}</legend>
			
			<dl>
				<dt class="reversed">
					<label for="preset">
						{lang}wcf.project.userNotificationEvent.preset{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="preset"
						name="preset"
						value="1"
						{if $preset}checked="checked"{/if}
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
						
				
					<small>{lang}wcf.project.userNotificationEvent.preset.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.permissions|isset} class="formError"{/if}>
				<dt>
					<label for="permissions">
						{lang}wcf.project.userNotificationEvent.permissions{/lang}
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
								{lang}wcf.project.error.userNotificationEvent.permissions.{@$errorType.permissions}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.userNotificationEvent.permissions.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.options|isset} class="formError"{/if}>
				<dt>
					<label for="options">
						{lang}wcf.project.userNotificationEvent.options{/lang}
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
								{lang}wcf.project.error.userNotificationEvent.options.{@$errorType.options}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.userNotificationEvent.options.description{/lang}</small>
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