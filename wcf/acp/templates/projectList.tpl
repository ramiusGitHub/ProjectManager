{include file='header' pageTitle='wcf.project.list.title'}

<script data-relocate="true" src="{@$__wcf->getPath()}acp/js/WCF.Project{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Project.Activate();
		new WCF.Project.Deactivate();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.project.list.title{/lang}</h1>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='ProjectAdd'}{/link}" class="button buttonPrimary"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.button.add{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'} 
		</ul>
	</nav>
</div>

{if $projects|count}
	{assign var=active value=0}
	{assign var=inactive value=0}
	{assign var=activeRows value=''}
	{assign var=inactiveRows value=''}
	
	{foreach from=$projects item=$project}
		{capture assign=row}
			<tr class="jsProjectRow">
				<td class="columnIcon">
					<a href="{link controller='ProjectVersionList' id=$project->packageID}{/link}" title="{lang}wcf.project.button.versions{/lang}" class="jsTooltip"><span class="icon icon16 fa-code-fork"></span></a>
					<a href="{link controller='ProjectEdit' id=$project->packageID}{/link}" title="{lang}wcf.project.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					
					{*		
					{if $project->isActive()}
						<a title="{lang}wcf.project.deactivate{/lang}" class="jsTooltip jsProjectChangeStatusButton" data-package-id="{@$project->packageID}"><span class="icon icon16 icon-circle pointer"></span></a>
					{else}
						<a title="{lang}wcf.project.activate{/lang}" class="jsTooltip jsProjectChangeStatusButton" data-package-id="{@$project->packageID}"><span class="icon icon16 icon-circle-blank pointer"></span></a>
					{/if}
					*}
					
					{event name='rowButtons'}
				</td>
				<td>
					<a href="{link controller='Project' id=$project->packageID}{/link}" title="{lang}wcf.project.button.view{/lang}" class="jsTooltip">{$project->getName()}</a>
				</td>
				<td class="columnText">{$project->packageVersion}</td>
				
				{event name='columns'}
			</tr>
		{/capture}
		
		{if $project->isActive()}
			{assign var=active value=$active + 1}
			{append var=activeRows value=$row}
		{else}
			{assign var=inactive value=$inactive + 1}
			{append var=inactiveRows value=$row}
		{/if}
	{/foreach}
	
	{if $active > 0}
		<div class="tabularBox tabularBoxTitle marginTop">
			<header>
				<h2>
					{lang}wcf.project.list.active{/lang}
					<span class="badge badgeInverse">{#$active}</span>
				</h2>
			</header>
			
			<table class="table">
				<thead>
					<tr>
						<th class="columnID"></th>
						<th class="columnTitle">{lang}wcf.project.name{/lang}</th>
						<th class="columnText">{lang}wcf.acp.package.version{/lang}</th>
						
						{event name='columnHeads'}
					</tr>
				</thead>
				
				<tbody>
					{@$activeRows}
				</tbody>
			</table>
		</div>
	{/if}
	
	{if $inactive > 0}
		<div class="tabularBox tabularBoxTitle marginTop">
			<header>
				<h2>
					{lang}wcf.project.list.inactive{/lang}
					<span class="badge badgeInverse">{#$inactive}</span>
				</h2>
			</header>
			
			<table class="table">
				<thead>
					<tr>
						<th class="columnID"></th>
						<th class="columnTitle">{lang}wcf.project.name{/lang}</th>
						<th class="columnText">{lang}wcf.acp.package.version{/lang}</th>
						
						{event name='columnHeads'}
					</tr>
				</thead>
				
				<tbody>
					{@$inactiveRows}
				</tbody>
			</table>
		</div>
	{/if}
{else}
	<p class="info">{lang}wcf.project.list.empty{/lang}</p>
{/if}

{include file='footer'}