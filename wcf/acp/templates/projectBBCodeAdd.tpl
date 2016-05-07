{include file='header' pageTitle='wcf.project.bbcode.'|concat::$action}

{capture assign='attributeTemplate'}
	<fieldset>
		<legend><span class="icon icon16 icon-remove pointer jsDeleteButton jsTooltip" title="{lang}wcf.global.button.delete{/lang}"></span> <span>{lang}wcf.acp.bbcode.attribute{/lang} {ldelim}#$attributeNo}</span></legend>
		<dl>
			<dt>
				<label for="attributes[{ldelim}@$attributeNo}][attributeHtml]">
					{lang}wcf.acp.bbcode.attribute.attributeHtml{/lang}
				</label>
			</dt>
			<dd>
				<input
					type="text"
					id="attributes[{ldelim}@$attributeNo}][attributeHtml]"
					name="attributes[{ldelim}@$attributeNo}][attributeHtml]"
					value=""
					class="long"
				/>
			</dd>
		</dl>
		
		<dl>
			<dt>
				<label for="attributes[{ldelim}@$attributeNo}][validationPattern]">
					{lang}wcf.acp.bbcode.attribute.validationPattern{/lang}
				</label>
			</dt>
			<dd>
				<input
					type="text"
					id="attributes[{ldelim}@$attributeNo}][validationPattern]"
					name="attributes[{ldelim}@$attributeNo}][validationPattern]"
					value=""
					class="long"
				/>
			</dd>
		</dl>
		
		<dl>
			<dt class="reversed">
				<label for="attributes[{ldelim}@$attributeNo}][required]">
					{lang}{lang}wcf.acp.bbcode.attribute.required{/lang}{/lang}
				</label>
			</dt>
			<dd>
				<input
					type="checkbox"
					id="attributes[{ldelim}@$attributeNo}][required]"
					name="attributes[{ldelim}@$attributeNo}][required]"
					value="1"
				/>
			</dd>
		</dl>
		
		<dl>
			<dt class="reversed">
				<label for="attributes[{ldelim}@$attributeNo}][useText]">
					{lang}{lang}wcf.acp.bbcode.attribute.useText{/lang}{/lang}
				</label>
			</dt>
			<dd>
				<input
					type="checkbox"
					id="attributes[{ldelim}@$attributeNo}][useText]"
					name="attributes[{ldelim}@$attributeNo}][useText]"
					value="1"
				/>
				<small>{lang}wcf.acp.bbcode.attribute.useText.description{/lang}</small>
			</dd>
		</dl>
		
		{event name='attributeFields'}
	</fieldset>
{/capture}

<script data-relocate="true">
//<![CDATA[
	$(function() {
		$('.jsDeleteButton').click(function (event) {
			$(event.target).parent().parent().remove();
		});
		
		var attributeNo = {if !$attributes|count}0{else}{assign var='lastAttribute' value=$attributes|end}{$lastAttribute->attributeNo+1}{/if};
		var attributeTemplate = new WCF.Template('{@$attributeTemplate|encodeJS}');
		
		$('.jsAddButton').click(function (event) {
			var $html = $($.parseHTML(attributeTemplate.fetch({ attributeNo: attributeNo++ })));
			$html.find('.jsDeleteButton').click(function (event) {
				$(event.target).parent().parent().remove();
			});
			$('#attributeFieldset').append($html);
		});
		
		var $buttonSettings = $('.jsButtonSetting');
		var $showButton = $('#showButton');
		function toggleButtonSettings() {
			if ($showButton.is(':checked')) {
				$buttonSettings.show();
			}
			else {
				$buttonSettings.hide();
			}
		}
		
		$showButton.change(toggleButtonSettings);
		toggleButtonSettings();
	});
//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.project.bbcode.{$action}{/lang}</h1>
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
			<li><a href="{link controller='Project' id=$packageID}#bbcode{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectBBCodeAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.eventListener.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.bbcode.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectBBCodeAdd'}{/link}{else}{link controller='ProjectBBCodeEdit' object=$bbcode}{/link}{/if}">
	<div class="container containerPadding marginTop">
		<fieldset>
			<legend>{lang}wcf.global.form.data{/lang}</legend>
			
			<dl{if $errorType.bbcodeTag|isset} class="formError"{/if}>
				<dt>
					<label for="bbcodeTag">
						{lang}wcf.acp.bbcode.bbcodeTag{/lang}
					</label>
				</dt>
				
				<dd>
					<input
						type="text"
						id="bbcodeTag"
						name="bbcodeTag"
						value="{$bbcodeTag}"
						required="required"
						autofocus="autofocus"
						pattern="^[a-zA-Z0-9]+$"
						class="medium"
					/>
					
					{if $errorType.bbcodeTag|isset}
						<small class="innerError">
							{if $errorType.bbcodeTag == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.bbcode.bbcodeTag.error.{$errorType.bbcodeTag}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl{if $errorType.htmlOpen|isset} class="formError"{/if}>
				<dt>
					<label for="htmlOpen">
						{lang}wcf.acp.bbcode.htmlOpen{/lang}
					</label>
				</dt>
				
				<dd>
					<input
						type="text"
						id="htmlOpen"
						name="htmlOpen"
						value="{$htmlOpen}"
						class="long"
					/>
					
					{if $errorType.htmlOpen|isset}
						<small class="innerError">
							{lang}wcf.acp.bbcode.htmlOpen.error.{$errorType.htmlOpen}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl{if $errorType.htmlClose|isset} class="formError"{/if}>
				<dt>
					<label for="htmlClose">
						{lang}wcf.acp.bbcode.htmlClose{/lang}
					</label>
				</dt>
				
				<dd>
					<input
						type="text"
						id="htmlClose"
						name="htmlClose"
						value="{$htmlClose}"
						class="long"
					/>
					
					{if $errorType.htmlClose|isset}
						<small class="innerError">
							{lang}wcf.acp.bbcode.htmlClose.error.{$errorType.htmlClose}{/lang}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl{if $errorType.allowedChildren|isset} class="formError"{/if}>
				<dt>
					<label for="allowedChildren">
						{lang}wcf.acp.bbcode.allowedChildren{/lang}
					</label>
				</dt>
				
				<dd>
					<input
						type="text"
						id="allowedChildren"
						name="allowedChildren"
						value="{$allowedChildren}"
						class="long"
						required="required"
						pattern="^((all|none)\^)?([a-zA-Z0-9]+,)*[a-zA-Z0-9]+$"
					/>
					
					{if $errorType.allowedChildren|isset}
						<small class="innerError">
							{if $errorType.allowedChildren == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.acp.bbcode.allowedChildren.error.{$errorType.allowedChildren}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed">
					<label for="isSourceCode">
						{lang}wcf.acp.bbcode.isSourceCode{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="isSourceCode"
						name="isSourceCode"
						value="1"
						{if $isSourceCode} checked="checked"{/if}
					/>
					
					<small>{lang}wcf.acp.bbcode.isSourceCode.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.className|isset} class="formError"{/if}>
				<dt>
					<label for="className">
						{lang}wcf.acp.bbcode.className{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						id="className"
						name="className"
						value="{$className}"
						class="long"
						pattern="^\\?([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$"
					/>
					
					{if $errorType.className|isset}
						<small class="innerError">
							{if $errorType.className == 'notFound'}
								{lang}wcf.project.error.className.notFound{/lang}
							{else}
								{lang}wcf.acp.bbcode.className.error.{$errorType.className}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			{if $nativeBBCode|empty}
				<dl>
					<dt class="reversed">
						<label for="showButton">
							{lang}wcf.acp.bbcode.showButton{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="checkbox"
							id="showButton"
							name="showButton"
							value="1"
							{if $showButton} checked="checked"{/if}
						/>
					</dd>
				</dl>
				
				<dl class="jsButtonSetting{if $errorType.buttonLabel|isset} formError{/if}">
					<dt><label for="buttonLabel">{lang}wcf.acp.bbcode.buttonLabel{/lang}</label></dt>
					<dd>
						<input
							type="text"
							id="buttonLabel"
							name="buttonLabel"
							value="{$i18nPlainValues['buttonLabel']}"
							class="long"
						/>
						
						{if $errorType.buttonLabel|isset}
							<small class="innerError">
								{if $errorType.buttonLabel == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{elseif $errorType.buttonLabel == 'multilingual'}
									{lang}wcf.global.form.error.multilingual{/lang}
								{else}
									{lang}wcf.acp.bbcode.buttonLabel.error.{@$errorType}{/lang}
								{/if}
							</small>
						{/if}
						
						{include file='multipleLanguageInputJavascript' elementIdentifier='buttonLabel' forceSelection=false}
					</dd>
				</dl>
				
				<dl class="jsButtonSetting{if $errorType.wysiwygIcon|isset} formError{/if}">
					<dt>
						<label for="wysiwygIcon">
							{lang}wcf.acp.bbcode.wysiwygIcon{/lang}
						</label>
					</dt>
					<dd>
						<input
							type="text"
							id="wysiwygIcon"
							name="wysiwygIcon"
							value="{$wysiwygIcon}"
							class="long"
						/>
						
						{if $errorType.wysiwygIcon|isset}
							<small class="innerError">
								{if $errorTypee.wysiwygIcon == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.acp.bbcode.wysiwygIcon.error.{@$errorTypee.wysiwygIcon}{/lang}
								{/if}
							</small>
						{/if}
						<small>{lang}wcf.acp.bbcode.wysiwygIcon.description{/lang}</small>
					</dd>
				</dl>
			{/if}
			
			{event name='dataFields'}
		</fieldset>
		
		<fieldset id="attributeFieldset">
			<legend>
				<span class="icon icon16 icon-plus pointer jsAddButton jsTooltip" title="{lang}wcf.global.button.add{/lang}"></span>
				{lang}wcf.acp.bbcode.attributes{/lang}
			</legend>
			
			{foreach from=$attributes item='attribute'}
				<fieldset>
					<legend><span class="icon icon16 icon-remove pointer jsDeleteButton jsTooltip" title="{lang}wcf.global.button.delete{/lang}"></span> <span>{lang}wcf.acp.bbcode.attribute{/lang} {#$attribute->attributeNo}</span></legend>
					
					<dl>
						<dt>
							<label for="attributes[{@$attribute->attributeNo}][attributeHtml]">
								{lang}wcf.acp.bbcode.attribute.attributeHtml{/lang}
							</label>
						</dt>
						
						<dd>
							<input
								type="text"
								id="attributes[{@$attribute->attributeNo}][attributeHtml]"
								name="attributes[{@$attribute->attributeNo}][attributeHtml]"
								value="{$attribute->attributeHtml}"
								class="long"
							/>
						</dd>
					</dl>
					
					{assign var=attributeIndex value='attributeValidationPattern'|concat:$attribute->attributeNo}
					<dl{if $errorType.$attributeIndex|isset} class="formError"{/if}>
						<dt>
							<label for="attributes[{@$attribute->attributeNo}][validationPattern]">
								{lang}wcf.acp.bbcode.attribute.validationPattern{/lang}
							</label>
						</dt>
						
						<dd>
							<input
								type="text"
								id="attributes[{@$attribute->attributeNo}][validationPattern]"
								name="attributes[{@$attribute->attributeNo}][validationPattern]"
								value="{$attribute->validationPattern}"
								class="long"
							/>
							
							{if $errorType.$attributeIndex|isset}
								<small class="innerError">
									{lang}wcf.acp.bbcode.attribute.validationPattern.error.notValid{/lang}
								</small>
							{/if}
						</dd>
					</dl>
					
					<dl>
						<dt class="reversed">
							<label for="attributes[{@$attribute->attributeNo}][required]">
								{lang}wcf.acp.bbcode.attribute.required{/lang}
							</label>
						</dt>
						
						<dd>
							<input
								type="checkbox"
								id="attributes[{@$attribute->attributeNo}][required]"
								name="attributes[{@$attribute->attributeNo}][required]"
								value="1"
								{if $attribute->required} checked="checked"{/if}
							/>
						</dd>
					</dl>
					
					<dl>
						<dt class="reversed">
							<label for="attributes[{@$attribute->attributeNo}][useText]">
								{lang}wcf.acp.bbcode.attribute.useText{/lang}
							</label>
						</dt>
						
						<dd>
							<input
								type="checkbox"
								id="attributes[{@$attribute->attributeNo}][useText]"
								name="attributes[{@$attribute->attributeNo}][useText]"
								value="1"
								{if $attribute->useText} checked="checked"{/if}
							/>
							
							<small>{lang}wcf.acp.bbcode.attribute.useText.description{/lang}</small>
						</dd>
					</dl>
					
					{event name='attributeFields'}
				</fieldset>
			{/foreach}
		</fieldset>
		
		{event name='fieldsets'}
	</div>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
		<input type="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" />
		<input type="hidden" name="packageID" value="{@$packageID}" />
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}