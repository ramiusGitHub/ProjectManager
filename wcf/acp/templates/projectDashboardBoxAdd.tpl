{include file='header' pageTitle='wcf.project.dashboardBox.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.dashboardBox.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#dashboardBox{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectDashboardBoxAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.dashboardBox.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.dashboardBox.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectDashboardBoxAdd'}{/link}{else}{link controller='ProjectDashboardBoxEdit' id=$boxID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.boxName|isset} class="formError"{/if}>
				<dt><label for="boxName">{lang}wcf.project.dashboardBox.boxName{/lang}</label></dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="boxName"
						id="boxName"
						value="{$boxName}"
						required="required" 
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					{if $errorType.boxName|isset}
						<small class="innerError">
							{if $errorType.boxName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.boxName.{@$errorType.boxName}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.dashboardBox.boxName.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt><label for="boxType">{lang}wcf.project.dashboardBox.boxType{/lang}</label></dt>
				<dd>
					<select name="boxType" id="boxType">
						<option value="content"{if $boxType == 'content'} selected="selected"{/if}>{lang}wcf.project.dashboardBox.boxType.content{/lang}</option>
						<option value="sidebar"{if $boxType == 'sidebar'} selected="selected"{/if}>{lang}wcf.project.dashboardBox.boxType.sidebar{/lang}</option>
					</select>
				</dd>
			</dl>

			<dl{if $errorType.displayedName|isset} class="formError"{/if}>
				<dt><label for="displayedName">{lang}wcf.project.dashboardBox.displayedName{/lang}</label></dt>
				<dd>
					<input
						type="text"
						id="displayedName"
						name="displayedName"
						value=""
						class="long"
					/>
					
					{if $errorType.displayedName|isset}
						<small class="innerError">{lang}wcf.project.dashboardBox.displayedName.error.{@$errorType.displayedName}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.dashboardBox.displayedName.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='displayedName' forceSelection=true}
			
			<dl{if $errorType.className|isset} class="formError"{/if}>
				<dt><label for="className">{lang}wcf.project.dashboardBox.className{/lang}</label></dt>
				<dd>
					<input
						type="text"
						class="inputText long"
						name="className"
						id="className"
						value="{$className}"
						required="required"
					/>
					
					{if $errorType.className|isset}
						<small class="innerError">
							{if $errorType.className == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.className.{@$errorType.className}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.dashboardBox.className.description{/lang}</small>
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