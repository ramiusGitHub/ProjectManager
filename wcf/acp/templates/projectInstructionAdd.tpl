{include file='header' pageTitle='wcf.project.instruction.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.instruction.{@$action}{/lang}</h1>
</header>

{if $action == 'add'}
	<div class="info">{lang}wcf.project.instruction.add.description{/lang}</div>
{/if}

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#instruction{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectInstructionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.script.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.instruction.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectInstructionAdd'}{/link}{else}{link controller='ProjectInstructionEdit' id=$scriptID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl>
				<dt>
					<label for="name">
						{lang}wcf.project.instruction.name{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="name"
						id="name"
						value="{$name}"
					/>
					
					<small>{lang}wcf.project.instruction.name.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.pip|isset} class="formError"{/if}>
				<dt>
					<label for="pip">{lang}wcf.project.instruction.pip{/lang}</label>
				</dt>
				<dd>
					<select id="pip" name="pip">
						{foreach from=$pips key=pipName item=pipObject}
							<option{if $pipName == $pip} selected="selected"{/if} value="{$pipName}">{$pipName}</option>
						{/foreach}
					</select>
					
					{if $errorType.pip|isset}
						<small class="innerError">{lang}wcf.project.error.instruction.pip.{@$errorType.pip}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.instruction.pip.description{/lang}</small>
				</dd>
			</dl>
			<script data-relocate="true">
				//<![CDATA[
					$('#pip').change(function() {
						var pip = $(this).val();
						
						if(pip == 'script') {
							$('#phpFilename').show();
							$('#sqlCode').hide();
							$('#xmlCode').hide();
						} else if(pip == 'sql') {
							$('#phpFilename').hide();
							$('#sqlCode').show();
							$('#xmlCode').hide();
						} else {
							$('#phpFilename').hide();
							$('#sqlCode').hide();
							$('#xmlCode').show();
						}
						

					});
					
					$('#pip').change();
				// ]]>
			</script>
			
			<dl>
				<dt>
					<label for="position">
						{lang}wcf.project.instruction.position{/lang}
					</label>
				</dt>
				<dd>
					<select name="position" id="position">
						<option value="start"{if $position == 'start'} selected="selected"{/if}>{lang}wcf.project.instruction.position.start{/lang}</option>
						<option value="end"{if $position == 'end'} selected="selected"{/if}>{lang}wcf.project.instruction.position.end{/lang}</option>
					</select>
					
					<small>{lang}wcf.project.instruction.position.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.executionOrder|isset} class="formError"{/if}>
				<dt>
					<label for="executionOrder">
						{lang}wcf.project.instruction.executionOrder{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="number"
						min="-128"
						max="127"
						class="inputText"
						name="executionOrder"
						id="executionOrder"
						value="{$executionOrder}"
					/>
					
					{if $errorType.executionOrder|isset}
						<small class="innerError">{lang}wcf.project.error.instruction.executionOrder.{$errorType.executionOrder}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.instruction.executionOrder.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed">
					<label for="atInstall">
						{lang}wcf.project.instruction.atInstall{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="atInstall"
						name="atInstall"
						value="1"
						{if $atInstall} checked="checked"{/if}
					/>
					
					<small>{lang}wcf.project.instruction.atInstall.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.versions|isset} class="formError"{/if}>
				<dt>
					<label for="versions">
						{lang}wcf.project.instruction.versions{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="versions"
						id="versions"
						rows="5"
					>{$versions}</textarea>
					
					{if $errorType.versions|isset}
						<small class="innerError">
							{if $errorType.versions == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.instruction.versions.{$errorType.versions}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.instruction.versions.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.instruction.content{/lang}</legend>
			
			<dl id="xmlCode"{if $errorType.xml|isset} class="formError"{/if}>
				<dt>
					<label for="xml">
						{lang}wcf.project.instruction.xml{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="xml"
						id="xml"
						rows="5"
					>{if $xml|isset}{$xml}{/if}</textarea>
					
					{if $errorType.xml|isset}
						<small class="innerError">
							{if $errorType.xml == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.xml.{@$errorType.xml}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.instruction.xml.description{/lang}</small>
				</dd>
			</dl>
			<script data-relocate="true">
				//<![CDATA[
					var $__extendedCodeMirrorConfig = {
						lineWrapping: false,
					};
				// ]]>
			</script>
			{include file='codemirror' codemirrorMode='xml' codemirrorSelector='#xml' __extendedCodeMirrorFullscreen=true __extendedCodeMirrorConfig=true}
			
			<dl id="sqlCode"{if $errorType.sql|isset} class="formError"{/if}>
				<dt>
					<label for="sql">
						{lang}wcf.project.instruction.sql{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="sql"
						id="sql"
						rows="5"
					>{if $sql|isset}{$sql}{/if}</textarea>
					
					{if $errorType.sql|isset}
						<small class="innerError">
							{if $errorType.sql == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.sql.{@$errorType.sql}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.instruction.sql.description{/lang}</small>
				</dd>
			</dl>
			<script data-relocate="true">
				//<![CDATA[
					var $__extendedCodeMirrorConfig = {
						lineWrapping: false,
					};
				// ]]>
			</script>
			{include file='codemirror' codemirrorMode='sql' codemirrorSelector='#sql' __extendedCodeMirrorFullscreen=true __extendedCodeMirrorConfig=true}
			
			<dl id="phpFilename">
				<dt>
					<label for="filename">
						{lang}wcf.project.instruction.filename{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="filename"
						id="filename"
						value="{if $filename|isset}{$filename}{/if}"
					/>
					
					<small>{lang}wcf.project.instruction.filename.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='content'}
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