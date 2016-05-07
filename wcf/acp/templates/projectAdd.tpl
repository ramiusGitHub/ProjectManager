{include file='header' pageTitle='wcf.project.'|concat:$action}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		WCF.TabMenu.init();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.project.{@$action}{/lang}</h1>
</header>

{include file='formError'}

{if $success|isset}<p class="success">{lang}wcf.global.success.{@$success}{/lang}</p>{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			{if $action == 'edit'}
				<li><a href="{link controller=Project id=$project->packageID}{/link}" title="{lang}wcf.project.package.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{lang}wcf.project.button.view{/lang}</span></a></li>
			{/if}
			
			<li><a href="{link controller='ProjectList'}{/link}" class="button buttonPrimary"><span class="icon icon16 fa-cubes"></span> <span>{lang}wcf.project.button.list{/lang}</span></a></li>
						
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'edit'}{link controller='ProjectEdit' id=$packageID}{/link}{else}{link controller='ProjectAdd'}{/link}{/if}">
	<div class="tabMenuContainer">
		<nav class="tabMenu">
			<ul>
				<li><a href="{@$__wcf->getAnchor('__essentials')}">{lang}wcf.global.form.data{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('__requirements')}">{lang}wcf.project.package.requirements{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('__optionals')}">{lang}wcf.project.package.optionals{/lang}</a></li>
				<li><a href="{@$__wcf->getAnchor('__excluded')}">{lang}wcf.project.package.excluded{/lang}</a></li>
				
				{event name='tabMenuTabs'}
			</ul>
		</nav>
		
		<div id="__essentials" class="container containerPadding tabMenuContent hidden">
			<fieldset>
				<legend>{lang}wcf.project.package.naming{/lang}</legend>
				
				<dl{if $errorType.identifier|isset} class="formError"{/if}>
					<dt>
						<label for="identifier">
							{lang}wcf.project.package.identifier{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="identifier"
							name="identifier"
							value="{$identifier}"
							class="long"
							autofocus="autofocus"
							required="required"
							pattern="\s*\w+(?:\.\w+){literal}{2,}{/literal}\s*"
						/>
						
						{if $errorType.identifier|isset}
							<small class="innerError">
								{if $errorType.identifier == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.identifier.error.{@$errorType.identifier}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.identifier.description{/lang}</small>
					</dd>
				</dl>

				<dl{if $errorType.packageName|isset} class="formError"{/if}>
					<dt>
						<label for="packageName">
							{lang}wcf.project.package.packageName{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="packageName"
							name="packageName"
							value="{$i18nPlainValues['packageName']}"
							class="long"
						/>
						
						{if $errorType.packageName|isset}
							<small class="innerError">
								{if $errorType.packageName == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{* {elseif $errorType.packageName == 'multilingual'}
									{lang}wcf.global.form.error.multilingual{/lang} *}
								{else}
									{lang}wcf.project.package.packageName.error.{@$errorType.packageName}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.packageName.description{/lang}</small>
					</dd>
				</dl>
				{include file='multipleLanguageInputJavascript' elementIdentifier='packageName' forceSelection=false}
				
				<dl{if $errorType.packageDescription|isset} class="formError"{/if}>
					<dt>
						<label for="packageDescription">
							{lang}wcf.project.package.packageDescription{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="packageDescription"
							name="packageDescription"
							value="{$i18nPlainValues['packageDescription']}"
							class="long"
						/>
						
						{if $errorType.packageDescription|isset}
							<small class="innerError">
								{if $errorType.packageDescription == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{* {elseif $errorType.packageDescription == 'multilingual'}
									{lang}wcf.global.form.error.multilingual{/lang} *}
								{else}
									{lang}wcf.project.package.packageDescription.error.{@$errorType.packageDescription}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.packageDescription.description{/lang}</small>
					</dd>
				</dl>
				{include file='multipleLanguageInputJavascript' elementIdentifier='packageDescription' forceSelection=false}
				
				{event name='naming'}
			</fieldset>

			<fieldset>
				<legend>{lang}wcf.project.general{/lang}</legend>
				
				<dl>
					<dt>
						<label for="projectDirectory">
							{lang}wcf.project.package.projectDirectory{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="projectDirectory"
							name="projectDirectory"
							value="{$projectDirectory}"
							class="long"
						/>
						
						<small>{lang}wcf.project.package.projectDirectory.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.packageVersion|isset} class="formError"{/if}>
					<dt>
						<label for="packageVersion">
							{lang}wcf.project.package.packageVersion{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="packageVersion"
							name="packageVersion"
							value="{$packageVersion}"
							class="long"
							{if $action != 'add'}disabled="disabled"{/if}
						/>
						
						{if $errorType.packageVersion|isset}
							<small class="innerError">
								{if $errorType.packageVersion == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.packageVersion.error.{@$errorType.packageVersion}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.packageVersion.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.packageDate|isset} class="formError"{/if}>
					<dt>
						<label for="packageDate">
							{lang}wcf.project.package.packageDate{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="date"
							name="packageDate"
							id="packageDate"
							value="{$packageDate|date}"
							class="small"
						/>
						
						{if $errorType.packageDate|isset}
							<small class="innerError">
								{if $errorType.packageDate == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.packageDate.error.{$errorType.packageDate}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.packageDate.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.packageURL|isset} class="formError"{/if}>
					<dt>
						<label for="packageURL">
							{lang}wcf.project.package.packageURL{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="url"
							id="packageURL"
							name="packageURL"
							value="{$packageURL}"
							class="long"
						/>
						
						{if $errorType.packageURL|isset}
							<small class="innerError">
								{if $errorType.packageURL == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.packageURL.error.{@$errorType.packageURL}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.packageURL.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.author|isset} class="formError"{/if}>
					<dt>
						<label for="author">
							{lang}wcf.project.package.author{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="author"
							name="author"
							value="{$author}"
							class="long"
						/>
						
						{if $errorType.author|isset}
							<small class="innerError">
								{if $errorType.author == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.author.error.{@$errorType.author}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.author.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.authorURL|isset} class="formError"{/if}>
					<dt>
						<label for="authorURL">
							{lang}wcf.project.package.authorURL{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="url"
							id="authorURL"
							name="authorURL"
							value="{$authorURL}"
							class="long"
						/>
						
						{if $errorType.authorURL|isset}
							<small class="innerError">
								{if $errorType.authorURL == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.authorURL.error.{@$errorType.authorURL}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.authorURL.description{/lang}</small>
					</dd>
				</dl>
				
				{event name='general'}
			</fieldset>
			
			{*
			<fieldset>
				<legend>{lang}wcf.project.package.add.additional{/lang}</legend>
				
				<dl{if $errorType.copyright|isset} class="formError"{/if}>
					<dt>
						<label for="copyright">
							{lang}wcf.project.package.copyright{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="copyright"
							name="copyright"
							value="{$copyright}"
							class="long"
						/>
						
						{if $errorType.copyright|isset}
							<small class="innerError">
								{if $errorType.copyright == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.copyright.error.{@$errorType.copyright}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.copyright.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.copyrightURL|isset} class="formError"{/if}>
					<dt>
						<label for="copyrightURL">
							{lang}wcf.project.package.copyrightURL{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="url"
							id="copyrightURL"
							name="copyrightURL"
							value="{$copyrightURL}"
							class="long"
						/>
						
						{if $errorType.copyrightURL|isset}
							<small class="innerError">
								{if $errorType.copyrightURL == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.copyrightURL.error.{@$errorType.copyrightURL}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.copyrightURL.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.license|isset} class="formError"{/if}>
					<dt>
						<label for="license">
							{lang}wcf.project.package.license{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="license"
							name="license"
							value="{$license}"
							class="long"
						/>
						
						{if $errorType.license|isset}
							<small class="innerError">
								{if $errorType.license == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.license.error.{@$errorType.license}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.license.description{/lang}</small>
					</dd>
				</dl>
				
				<dl{if $errorType.licenseURL|isset} class="formError"{/if}>
					<dt>
						<label for="licenseURL">
							{lang}wcf.project.package.licenseURL{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="url"
							id="licenseURL"
							name="licenseURL"
							value="{$licenseURL}"
							class="long"
						/>
						
						{if $errorType.licenseURL|isset}
							<small class="innerError">
								{if $errorType.licenseURL == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.licenseURL.error.{@$errorType.licenseURL}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.licenseURL.description{/lang}</small>
					</dd>
				</dl>
				
				{event name='additional'}
			</fieldset>
			*}
			
			{if false && $action == 'add'}
				<fieldset>
					<legend>{lang}wcf.project.package.application{/lang}</legend>
					
					<dl>
						<dt class="reversed">
							<label for="isApplication">
								{lang}wcf.project.package.isApplication{/lang}
							</label>
						</dt>
						<dd>
							<input
								type="checkbox"
								id="isApplication"
								name="isApplication"
								value="1"
								{if $isApplication == 1}checked="checked" {/if}
							/>
							
							<small>{lang}wcf.project.package.isApplication.description{/lang}</small>
						</dd>
					</dl>
					
					<dl{if $errorType.packageDir|isset} class="formError"{/if}>
						<dt><label for="packageDir">{lang}wcf.project.package.packageDir{/lang}</label></dt>
						<dd>
							<input
								type="text"
								id="packageDir"
								name="packageDir"
								value="{$packageDir}"
								class="long"
							/>
							
							{if $errorType.packageDir|isset}
								<small class="innerError">
									{if $errorType.packageDir == 'empty'}
										{lang}wcf.global.form.error.empty{/lang}
									{else}
										{lang}wcf.project.package.packageDir.error.{@$errorType.packageDir}{/lang}
									{/if}
								</small>
							{/if}
							
							<small>{lang}wcf.project.package.packageDir.description{/lang}</small>
						</dd>
					</dl>
					
					<script data-relocate="true">
						//<![CDATA[
						$('#isApplication').change(function (event) {
							if($('#isApplication').is(':checked')) {
								$('#packageDir').attr('readonly', false);
							} else {
								$('#packageDir').attr('readonly', true);
							}
						});
						$('#isApplication').change();
						//]]>
					</script>
					
					{event name='isApplication'}
				</fieldset>
			{/if}
			
			{event name='__essentials'}
		</div>
		
		<div id="__requirements" class="container containerPadding tabMenuContent hidden">
			<fieldset>
				<legend>{lang}wcf.project.package.requirements{/lang}</legend>
				
				{foreach from=$packages item=$p}
					{if $p->packageID != $packageID}
						{assign var=requirementVersionID value='requirementVersion'|concat:$p->packageID}
						
						<dl{if $errorType.$requirementVersionID|isset} class="formError"{/if}>
							<dt class="reversed">
								<label for="requirements-{@$p->package}">
									{lang}{$p->packageName}{/lang} ({$p->packageVersion})
								</label>
							</dt>
							<dd>
								{assign var=tmpPackageID value=$p->packageID}
								<input
									type="checkbox"
									id="requirements-{@$p->packageID}"
									name="requirements[]"
									value="{@$p->packageID}"
									{if $requirements.$tmpPackageID|isset} checked="checked"{/if}
								/>
								
								<small>
									<a href="{link controller=PackageView id=$p->packageID}{/link}">{$p->package}</a>
									<input
										type="text"
										id="requirementsVersion-{@$p->packageID}"
										name="requirementsVersion[{@$p->packageID}]"
										value="{if $requirements.$tmpPackageID|isset}{$requirements.$tmpPackageID}{else}{$p->packageVersion}{/if}"
									/>
									
									{if $errorType.$requirementVersionID|isset}
										<small class="innerError">
											{lang}wcf.project.package.requirementVersion.error.{@$errorType.$requirementVersionID}{/lang}
										</small>
									{/if}
									{if !$p->packageDescription|empty}<br />{lang}{$p->packageDescription}{/lang}{/if}
								</small>
							</dd>
						</dl>
				
						<script data-relocate="true">
							//<![CDATA[
							$('#requirements-{@$p->packageID}').change(function (event) {
								if($('#requirements-{@$p->packageID}').is(':checked')) {
									$('#requirementsVersion-{@$p->packageID}').attr('hidden', false);
								} else {
									$('#requirementsVersion-{@$p->packageID}').attr('hidden', true);
								}
							});
							$('#requirements-{@$p->packageID}').change();
							//]]>
						</script>
					{/if}
				{/foreach}
				
				{event name='requirements'}
			</fieldset>
			
			{event name='__requirements'}
		</div>
		
		<div id="__optionals" class="container containerPadding tabMenuContent hidden">
			{assign var='i' value=0}
			{capture assign='optionalCheckboxes'}
				{foreach from=$packages item=$p}
					{if $p->author != 'WoltLab GmbH' && $p->packageID != $packageID}
						{assign var='i' value=$i+1}
						<dl>
							<dt class="reversed">
								<label for="optionals-{@$p->package}">
									{lang}{$p->packageName}{/lang} ({$p->packageVersion})
								</label>
							</dt>
							<dd>
								<input
									type="checkbox"
									id="optionals-{@$p->package}"
									name="optionals[]"
									value="{@$p->packageID}"
									{if $p->packageID|in_array:$optionals} checked="checked"{/if}
								/> 
								
								<small>
									<a href="{link controller=PackageView id=$p->packageID}{/link}">{$p->package}</a>
									{if !$p->packageDescription|empty}<br />{lang}{$p->packageDescription}{/lang}{/if}
								</small>
							</dd>
						</dl>
					{/if}
				{/foreach}
			{/capture}
			
			{if $i > 0}
			<fieldset>
				<legend>{lang}wcf.project.package.optionals{/lang}</legend>
				
				{@$optionalCheckboxes}
				
				{event name='optionals'}
			</fieldset>
			{else}
				<p>{lang}wcf.project.package.optionals.empty{/lang}</p>
			{/if}
			
			{event name='__optionals'}
		</div>
		
		<div id="__excluded" class="container containerPadding tabMenuContent hidden">
			<fieldset>
				<legend>{lang}wcf.project.package.excluded{/lang}</legend>
				
				<dl class="wide{if $errorType.excluded|isset} formError{/if}">
					<dt>
						<label for="text">
							{lang}wcf.project.package.excluded{/lang}
						</label>
					</dt>
					<dd>
						<textarea
							id="excluded"
							name="excluded"
							rows="10"
							cols="40"
						>{$excluded}</textarea>
						
						{if $errorType.excluded|isset}
							<small class="innerError">
								{if $errorType.excluded == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.package.packageDir.error.{@$errorType.excluded}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.package.excluded.description{/lang}</small>
					</dd>
				</dl>
				
				{event name='excluded'}
			</fieldset>
			
			{event name='__excluded'}
		</div>
	</div>

	<div class="formSubmit">
		<input type="submit" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}