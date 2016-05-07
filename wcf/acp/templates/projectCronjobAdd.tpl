{include file='header' pageTitle='wcf.project.cronjob.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.cronjob.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#cronjob{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectCronjobAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.cronjob.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.cronjob.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectCronjobAdd'}{/link}{else}{link controller='ProjectCronjobEdit' id=$cronjobID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.className|isset} class="formError"{/if}>
				<dt>
					<label for="className">
						{lang}wcf.project.cronjob.className{/lang}
					</label>
				</dt>
				<dd class="formField">
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="className"
						id="className"
						value="{$className}"
					/>
					
					{if $errorType.className|isset}
						<p class="innerError">
							{if $errorType.className == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.className.{@$errorType.className}{/lang}{/if}
						</p>
					{/if}
					
					<small>{lang}wcf.project.cronjob.className.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.description|isset} class="formError"{/if}>
				<dt>
					<label for="description">
						{lang}wcf.project.cronjob.description{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="description"
						id="description"
						value="{$description}"
					/>
					
					{if $errorType.description|isset}
						<p class="innerError">
							{if $errorType.description == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.description.{@$errorType.description}{/lang}
							{/if}
						</p>
					{/if}
				
					<small>{lang}wcf.project.cronjob.description.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='description' forceSelection=false}
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.cronjob.schedule{/lang}</legend>
		
			<dl{if $errorType.startMinute|isset} class="formError"{/if}>
				<dt>
					<label for="startMinute">
						{lang}wcf.project.cronjob.startMinute{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="startMinute"
						id="startMinute"
						value="{$startMinute}"
					/>
					
					{if $errorType.startMinute|isset}
						<small class="innerError">
							{if $errorType.startMinute == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.startMinute == 'invalid'}
									{lang}wcf.project.error.invalid{/lang}
								{else}
									{lang}wcf.project.error.startMinute.{@$errorType.startMinute}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.cronjob.startMinute.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.startHour|isset} class="formError"{/if}>
				<dt>
					<label for="startHour">
						{lang}wcf.project.cronjob.startHour{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="startHour"
						id="startHour"
						value="{$startHour}"
					/>
					
					{if $errorType.startHour|isset}
						<small class="innerError">
							{if $errorType.startHour == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.startHour == 'invalid'}
									{lang}wcf.project.error.invalid{/lang}
								{else}
									{lang}wcf.project.error.startHour.{@$errorType.startHour}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
				
					<small>{lang}wcf.project.cronjob.startHour.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.startDom|isset} class="formError"{/if}>
				<dt>
					<label for="startDom">
						{lang}wcf.project.cronjob.startDom{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="startDom"
						id="startDom"
						value="{$startDom}"
					/>
					
					{if $errorType.startDom|isset}
						<small class="innerError">
							{if $errorType.startDom == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.startDom == 'invalid'}
									{lang}wcf.project.error.invalid{/lang}
								{else}
									{lang}wcf.project.error.startDom.{@$errorType.startDom}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.cronjob.startDom.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.startMonth|isset} class="formError"{/if}>
				<dt>
					<label for="startMonth">
						{lang}wcf.project.cronjob.startMonth{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="startMonth"
						id="startMonth"
						value="{$startMonth}"
					/>
					
					{if $errorType.startMonth|isset}
						<small class="innerError">
							{if $errorType.startMonth == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.startMonth == 'invalid'}
									{lang}wcf.project.error.invalid{/lang}
								{else}
									{lang}wcf.project.error.startMonth.{@$errorType.startMonth}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
				
					<small>{lang}wcf.project.cronjob.startMonth.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.startDow|isset} class="formError"{/if}>
				<dt>
					<label for="startDow">
						{lang}wcf.project.cronjob.startDow{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText"
						name="startDow"
						id="startDow"
						value="{$startDow}"
					/>
					
					{if $errorType.startDow|isset}
						<small class="innerError">
							{if $errorType.startDow == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{if $errorType.startDow == 'invalid'}
									{lang}wcf.project.error.invalid{/lang}
								{else}
									{lang}wcf.project.error.startDow.{@$errorType.startDow}{/lang}
								{/if}
							{/if}
						</small>
					{/if}
				
					<small>{lang}wcf.project.cronjob.startDow.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='schedule'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.cronjob.options{/lang}</legend>
			
			<dl>
				<dt class="reversed">
					<label for="canBeEdited">
						{lang}wcf.project.cronjob.canBeEdited{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="canBeEdited"
						name="canBeEdited"
						value="1"
						{if $canBeEdited} checked="checked"{/if}
					/>	
				
					<small>{lang}wcf.project.cronjob.canBeEdited.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed">
					<label for="canBeDisabled">
						{lang}wcf.project.cronjob.canBeDisabled{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="canBeDisabled"
						name="canBeDisabled"
						value="1"{if $canBeDisabled}
						checked="checked"{/if}
						onclick="$('#defaultIsDisabled').prop('disabled', !this.checked)"
					/>
				
					<small>{lang}wcf.project.cronjob.canBeDisabled.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed">
					<label for="defaultIsDisabled">
						{lang}wcf.project.cronjob.defaultIsDisabled{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="defaultIsDisabled"
						name="defaultIsDisabled"
						value="1"
						{if $defaultIsDisabled}checked="checked"{/if}
						{if !$canBeDisabled}disabled="disabled"{/if}
					/>
				
					<small>{lang}wcf.project.cronjob.defaultIsDisabled.description{/lang}</small>
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