{include file='header' pageTitle='wcf.project.version.list.title'}

<script data-relocate="true" src="{@$__wcf->getPath()}acp/js/WCF.Project{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		// Switch version
		new WCF.Project.Version.Switch();
		
		// Delete version
		new WCF.Action.Delete('wcf\\data\\project\\version\\ProjectVersionAction', '.jsVersionRow');
	});
	//]]>
</script>
		
{event name='javascript'}

<header class="boxHeadline">
	<h1>{lang}wcf.project.version.list.title{/lang}</h1>
</header>

<div class="info" id="exporterInfo">
	{lang}wcf.project.export.info{/lang}
</div>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller=Project id=$project->packageID}{/link}" title="{lang}wcf.project.package.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{lang}wcf.project.button.view{/lang}</span></a></li>
			
			<li><a href="{link controller='ProjectList'}{/link}" title="{lang}wcf.project.list{/lang}" class="button"><span class="icon icon16 fa-cubes"></span> <span>{lang}wcf.project.button.list{/lang}</span></a></li>
			
			<li><a href="{link controller=ProjectVersionAdd}packageID={@$project->packageID}{/link}" title="{lang}wcf.project.version.add{/lang}" class="button buttonPrimary"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.project.version.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'} 
		</ul>
	</nav>
</div>

<div class="tabularBox tabularBoxTitle marginTop">
	<table class="table">
		<thead>
			<tr>
				<th class="columnIcon"></th>
				<th class="columnText">{lang}wcf.project.package.packageVersion{/lang}</th>
				<th class="columnIcon">{lang}wcf.project.version.isReleased{/lang}</th>
				
				{event name='columnHeads'}
			</tr>
		</thead>
			
		<tbody>
			{foreach from=$project->getVersions() item=version}
				{if !$version->isDeleted}
					<tr class="jsVersionRow">
						<td class="columnIcon">
							{if $version->isDeletable()}
								<span class="icon icon16 icon-remove jsTooltip jsDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$version->logID}" data-confirm-message="{lang}wcf.project.version.delete.sure{/lang}"></span>
							{else}
								<span class="icon icon16 icon-remove jsTooltip disabled" title="{lang}wcf.global.button.delete{/lang}"></span>
							{/if}
							
							{if $version->isEditable()}
								<a href="{link controller='ProjectVersionEdit' id=$version->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil pointer"></span></a>
							{else}
								<span class="icon icon16 icon-pencil jsTooltip pointer disabled" title="{lang}wcf.global.button.edit{/lang}"></span>
							{/if}
							
							{if $version->isSwitchable()}
								<span class="icon icon16 fa-sign-in jsTooltip jsSwitchButton pointer" title="{lang}wcf.project.button.version.switch{/lang}" data-object-id="{@$version->logID}" data-confirm-message="{lang}wcf.project.version.switch.sure{/lang}"></span>
							{else}
								<span class="icon icon16 fa-sign-in jsTooltip disabled" title="{lang}wcf.project.button.version.switch{/lang}"></span>
							{/if}
							
							{event name='rowButtons'}
						</td>
						<td class="columnPackageVersion columnText">{$version->getVersionString()}</td>
						<td class="columnIsReleased columnIcon">
							{if $version->isReleased()}
								<span class="icon icon16 icon-thumb-tack jsTooltip" title="{lang}wcf.project.version.isReleased{/lang}"></span>
							{/if}
						</td>
						
						{event name='columns'}
					</tr>
				{/if}
			{/foreach}
		</tbody>
	</table>
</div>

{include file='footer'}