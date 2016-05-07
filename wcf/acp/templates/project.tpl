{include file='header' pageTitle='wcf.project.view'}

<script data-relocate="true" src="{@$__wcf->getPath()}acp/js/WCF.Project{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		// The tab menu
		WCF.TabMenu.init();
		
		// Filtering
		{if $project->getOptionCount()}new WCF.Project.Table.Filter('#optionFilterInput', '#optionTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getOptionCategoryCount()}new WCF.Project.Table.Filter('#optionCategoryFilterInput', '#optionCategoryTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getUserGroupOptionCount()}new WCF.Project.Table.Filter('#userGroupOptionFilterInput', '#userGroupOptionTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getUserGroupOptionCategoryCount()}new WCF.Project.Table.Filter('#userGroupOptionCategoryFilterInput', '#userGroupOptionCategoryTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getUserOptionCount()}new WCF.Project.Table.Filter('#userOptionFilterInput', '#userOptionTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getUserOptionCategoryCount()}new WCF.Project.Table.Filter('#userOptionCategoryFilterInput', '#userOptionCategoryTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getACLOptionCount()}new WCF.Project.Table.Filter('#aclOptionFilterInput', '#aclOptionTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getACLOptionCategoryCount()}new WCF.Project.Table.Filter('#aclOptionCategoryFilterInput', '#aclOptionCategoryTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getACPMenuItemCount()}new WCF.Project.Table.Filter('#acpMenuItemFilterInput', '#acpMenuItemTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getPageMenuItemCount()}new WCF.Project.Table.Filter('#pageMenuItemFilterInput', '#pageMenuItemTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getUserMenuItemCount()}new WCF.Project.Table.Filter('#userMenuItemFilterInput', '#userMenuItemTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getUserProfileMenuItemCount()}new WCF.Project.Table.Filter('#userProfileMenuItemFilterInput', '#userProfileMenuItemTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getEventListenerCount()}new WCF.Project.Table.Filter('#eventListenerFilterInput', '#eventListenerTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getTemplateListenerCount()}new WCF.Project.Table.Filter('#templateListenerFilterInput', '#templateListenerTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getCronjobCount()}new WCF.Project.Table.Filter('#cronjobFilterInput', '#cronjobTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getLanguageVariableCount()}new WCF.Project.Table.Filter('#languageVariableFilterInput', '#languageVariableTable', '.projectHighlightedRow', 'language-item', {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getFileCount()}new WCF.Project.Table.Filter('#fileFilterInput', '#fileTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getTemplateCount()}new WCF.Project.Table.Filter('#templateFilterInput', '#templateTable', null, null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getAcpTemplateCount()}new WCF.Project.Table.Filter('#acpTemplateFilterInput', '#acpTemplateTable', null, null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getDatabaseTableCount()}new WCF.Project.Table.Filter('#databaseTableFilterInput', '#databaseTableTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getDatabaseColumnCount()}new WCF.Project.Table.Filter('#databaseColumnFilterInput', '#databaseColumnTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getDatabaseIndexCount()}new WCF.Project.Table.Filter('#databaseIndexFilterInput', '#databaseIndexTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getDatabaseForeignKeyCount()}new WCF.Project.Table.Filter('#databaseForeignKeyFilterInput', '#databaseForeignKeyTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getObjectTypeCount()}new WCF.Project.Table.Filter('#objectTypeFilterInput', '#objectTypeTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getObjectTypeDefinitionCount()}new WCF.Project.Table.Filter('#objectTypeDefinitionFilterInput', '#objectTypeDefinitionTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getBBCodeCount()}new WCF.Project.Table.Filter('#bbcodeFilterInput', '#bbcodeTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getDashboardBoxCount()}new WCF.Project.Table.Filter('#dashboardBoxFilterInput', '#dashboardBoxTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		{if $project->getInstructionCount()}new WCF.Project.Table.Filter('#instructionFilterInput', '#instructionTable', '.projectHighlightedRow', null, {@PROJECT_MANAGER_DISJUNCTIVE_FILTERING|intval});{/if}
		
		// Set focus on the filter field visible after page load
		$(location.hash+'FilterInput').focus();
		
		// Deletion
		new WCF.Project.Action.Delete('wcf\\data\\project\\option\\ProjectOptionAction', '.jsOptionRow', '.jsOptionDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\option\\category\\ProjectOptionCategoryAction', '.jsOptionCategoryRow', '.jsOptionCategoryDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\option\\ProjectUserOptionAction', '.jsUserOptionRow', '.jsUserOptionDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\option\\category\\ProjectUserOptionCategoryAction', '.jsUserOptionCategoryRow', '.jsUserOptionCategoryDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\group\\option\\ProjectUserGroupOptionAction', '.jsUserGroupOptionRow', '.jsUserGroupOptionDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\group\\option\\category\\ProjectUserGroupOptionCategoryAction', '.jsUserGroupOptionCategoryRow', '.jsUserGroupOptionCategoryDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\acl\\option\\ProjectACLOptionAction', '.jsACLOptionRow', '.jsACLOptionDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\acl\\option\\category\\ProjectACLOptionCategoryAction', '.jsACLOptionCategoryRow', '.jsACLOptionCategoryDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\acp\\menu\\item\\ProjectACPMenuItemAction', '.jsACPMenuItemRow', '.jsACPMenuItemDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\page\\menu\\item\\ProjectPageMenuItemAction', '.jsPageMenuItemRow', '.jsPageMenuItemDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\menu\\item\\ProjectUserMenuItemAction', '.jsUserMenuItemRow', '.jsUserMenuItemDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\profile\\menu\\item\\ProjectUserProfileMenuItemAction', '.jsUserProfileMenuItemRow', '.jsUserProfileMenuItemDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\event\\listener\\ProjectEventListenerAction', '.jsEventListenerRow', '.jsEventListenerDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\template\\listener\\ProjectTemplateListenerAction', '.jsTemplateListenerRow', '.jsTemplateListenerDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\cronjob\\ProjectCronjobAction', '.jsCronjobRow', '.jsCronjobDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\language\\item\\ProjectLanguageItemAction', '.jsLanguageVariableRow', '.jsLanguageItemDeleteButton', 'language-item');
		new WCF.Project.Action.Delete('wcf\\data\\project\\acp\\search\\provider\\ProjectACPSearchProviderAction', '.jsACPSearchProviderRow', '.jsACPSearchProviderDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\user\\notification\\event\\ProjectUserNotificationEventAction', '.jsUserNotificationEventRow', '.jsUserNotificationDeleteDeleteButton');
		new WCF.Project.Action.Delete.Database();
		new WCF.Project.Action.Delete('wcf\\data\\project\\object\\type\\ProjectObjectTypeAction', '.jsObjectTypeRow', '.jsObjectTypeDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\object\\type\definition\\ProjectObjectTypeDefinitionAction', '.jsObjectTypeDefinitionRow', '.jsObjectTypeDefinitionDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\bbcode\\ProjectBBCodeAction', '.jsBBCodeRow', '.jsBBCodeDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\dashboard\\box\\ProjectDashboardBoxAction', '.jsDashboardBoxRow', '.jsDashboardBoxDeleteButton');
		new WCF.Project.Action.Delete('wcf\\data\\project\\instruction\\ProjectInstructionAction', '.jsInstructionRow', '.jsInstructionDeleteButton');
		
		// Highlight spanned rows
		new WCF.Project.Table.SpannedRowHighlight('#languageVariableTable', 'language-item');
		new WCF.Project.Table.SpannedRowHighlight('#clipboardActionTable', 'action-name');
		
		// Add current active tab menu item to quick link
		$('.projectQuickLink').click(function(event) {
			$anker = $(event.currentTarget);
			$anker.attr("href", $anker.attr("href") + location.hash);
			return true;
		});
	});
	//]]>
</script>

{event name='javascriptInclude'}

<header class="boxHeadline">
	<h1>{lang}wcf.project.view{/lang}</h1>
</header>

<div class="contentNavigation">
	<nav>
		<ul>
			{if $projects|count > 1}
				<li class="dropdown">
					<a class="button dropdownToggle"><span class="icon icon16 icon-sort"></span> <span>{lang}wcf.project.button.choose{/lang}</span></a>
					<div class="dropdownMenu">
						<ul class="scrollableDropdownMenu">
							{foreach from=$projects item='p'}
								<li{if $p->packageID == $packageID} class="active"{/if}><a class="projectQuickLink" href="{link controller='Project' id=$p->packageID}{/link}"><span class="icon icon16 fa-cube"></span> <span>{$p->getName()}</span></a></li>
							{/foreach}
						</ul>
					</div>
				</li>
			{/if}
			
			<li><a href="{link controller='ProjectEdit' id=$project->packageID}{/link}" class="button"><span class="icon icon16 icon-edit"></span> <span>{lang}wcf.project.button.edit{/lang}</span></a></li>
			
			<li><a href="{link controller='ProjectList'}{/link}" class="button buttonPrimary"><span class="icon icon16 fa-cubes"></span> <span>{lang}wcf.project.button.list{/lang}</span></a></li>
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<div class="tabMenuContainer">
	<nav class="tabMenu">
		<ul>
			<li><a href="{@$__wcf->getAnchor(_options)}"><span>{lang}wcf.project.options{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_menus)}"><span>{lang}wcf.project.menus{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_system)}"><span>{lang}wcf.project.system{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_files)}"><span>{lang}wcf.project.files{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_database)}"><span>{lang}wcf.project.database{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_objects)}"><span>{lang}wcf.project.objects{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_apperance)}"><span>{lang}wcf.project.apperance{/lang}</span></a></li>
			<li><a href="{@$__wcf->getAnchor(_installation)}"><span>{lang}wcf.project.installation{/lang}</span></a></li>
			
			{event name='tabMenuTabs'}
		</ul>
	</nav>
	
	<div id="_options" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(option)}"><span>{lang}wcf.project.options{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(userGroupOption)}"><span>{lang}wcf.project.userGroupOptions{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(userOption)}"><span>{lang}wcf.project.userOptions{/lang}</span></a></li>
				{* // TODO <li><a href="{@$__wcf->getAnchor(aclOption)}"><span>{lang}wcf.project.aclOptions{/lang}</span></a></li> *}
				
				{event name='optionsSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="option">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectOptionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="optionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">

						{if $project->getOptionCount() > 0}
							{lang}wcf.project.options{/lang}

							<span id="optionCount" class="badge badgeInverse badgeCounter">{#$project->getOptionCount()}</span>
						{else}
							{lang}wcf.project.option.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getOptionCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionTable'
						name='option'
						languageCat='wcf.acp.option'
						options=$project->getOptions()
						sortableActionName='wcf\\\\data\\\\project\\\\option\\\\ProjectOptionAction'}
				{/if}
			</div>
			
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectOptionCategoryAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="optionCategoryFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getOptionCategoryCount() > 0}
							{lang}wcf.project.optionCategories{/lang}

							<span id="optionCategoryCount" class="badge badgeInverse badgeCounter">{#$project->getOptionCategoryCount()}</span>
						{else}
							{lang}wcf.project.option.categoryEmpty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getOptionCategoryCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionCategoryTable'
						name='option'
						languageCat='wcf.acp.option'
						categories=$project->getOptionCategories()
						sortableActionName='wcf\\\\data\\\\project\\\\option\\\\category\\\\ProjectOptionCategoryAction'}
				{/if}
			</div>
		</div>
		
		<div id="userGroupOption">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserGroupOptionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userGroupOptionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserGroupOptionCount() > 0}
							{lang}wcf.project.userGroupOptions{/lang}
							
							<span id="userGroupOptionCount" class="badge badgeInverse badgeCounter">{#$project->getUserGroupOptionCount()}</span>
						{else}
							{lang}wcf.project.userGroupOption.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserGroupOptionCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionTable'
						name='userGroupOption'
						languageCat='wcf.acp.group'
						additionalName='option'
						options=$project->getUserGroupOptions()
						sortableActionName='wcf\\\\data\\\\project\\\\user\\\\group\\\\option\\\\ProjectUserGroupOptionAction'}
				{/if}
			</div>
			
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserGroupOptionCategoryAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userGroupOptionCategoryFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserGroupOptionCategoryCount() > 0}
							{lang}wcf.project.userGroupOptionCategories{/lang}

							<span id="userGroupOptionCategoryCount" class="badge badgeInverse badgeCounter">{#$project->getUserGroupOptionCategoryCount()}</span>
						{else}
							{lang}wcf.project.userGroupOption.categoryEmpty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserGroupOptionCategoryCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionCategoryTable'
						name='userGroupOption'
						languageCat='wcf.acp.group'
						additionalName='option'
						categories=$project->getUserGroupOptionCategories()
						sortableActionName='wcf\\\\data\\\\project\\\\user\\\\group\\\\option\\\\category\\\\ProjectUserGroupOptionCategoryAction'}
				{/if}
			</div>
		</div>
		
		<div id="userOption">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserOptionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userOptionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserOptionCount() > 0}
							{lang}wcf.project.userOptions{/lang}
							
							<span id="userOptionCount" class="badge badgeInverse badgeCounter">{#$project->getUserOptionCount()}</span>
						{else}
							{lang}wcf.project.userOption.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserOptionCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionTable'
						name='userOption'
						languageCat='wcf.acp.user'
						additionalName='option'
						options=$project->getUserOptions()
						sortableActionName='wcf\\\\data\\\\project\\\\user\\\\option\\\\ProjectOptionAction'}
				{/if}
			</div>
			
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserOptionCategoryAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userOptionCategoryFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserOptionCategoryCount() > 0}
							{lang}wcf.project.userOptionCategories{/lang}

							<span id="userOptionCategoryCount" class="badge badgeInverse badgeCounter">{#$project->getUserOptionCategoryCount()}</span>
						{else}
							{lang}wcf.project.userOption.categoryEmpty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserOptionCategoryCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionCategoryTable'
						name='userOption'
						languageCat='wcf.acp.user'
						additionalName='option'
						categories=$project->getUserOptionCategories() 
						sortableActionName='wcf\\\\data\\\\project\\\\user\\\\option\\\\category\\\\ProjectUserOptionCategoryAction'}
				{/if}
			</div>
		</div>
		
		{* TODO
		<div id="aclOption">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectACLOptionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="aclOptionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">

						{if $project->getACLOptionCount() > 0}
							{lang}wcf.project.aclOptions{/lang}

							<span id="aclOptionCount" class="badge badgeInverse badgeCounter">{#$project->getACLOptionCount()}</span>
						{else}
							{lang}wcf.project.aclOption.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getACLOptionCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionTable'
						name='aclOption'
						languageCat='wcf.acl.option'
						options=$project->getACLOptions()}
				{/if}
			</div>
			
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectACLOptionCategoryAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="aclOptionCategoryFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getACLOptionCategoryCount() > 0}
							{lang}wcf.project.aclOptionCategories{/lang}

							<span id="aclOptionCategoryCount" class="badge badgeInverse badgeCounter">{#$project->getACLOptionCategoryCount()}</span>
						{else}
							{lang}wcf.project.aclOption.categoryEmpty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getACLOptionCategoryCount() > 0}
					{include
						sandbox=true
						file='projectContentOptionCategoryTable'
						name='aclOption'
						languageCat='wcf.acl.option'
						categories=$project->getACLOptionCategories()}
				{/if}
			</div>
		</div> *}
	
		{event name='optionsContent'}
	</div>
	
	<div id="_menus" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(acpMenuItem)}"><span>{lang}wcf.project.acpMenuItems{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(pageMenuItem)}"><span>{lang}wcf.project.pageMenuItems{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(userMenuItem)}"><span>{lang}wcf.project.userMenuItems{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(userProfileMenuItem)}"><span>{lang}wcf.project.userProfileMenuItems{/lang}</span></a></li>
				
				{event name='menusSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="acpMenuItem">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectACPMenuItemAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="acpMenuItemFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getACPMenuItemCount() > 0}
							{lang}wcf.project.acpMenuItems{/lang}
							
							<span id="acpMenuItemCount" class="badge badgeInverse badgeCounter">{#$project->getACPMenuItemCount()}</span>
						{else}
							{lang}wcf.project.acpMenuItem.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getACPMenuItemCount() > 0}
					{include
						sandbox=true
						file='projectContentMenuTable'
						name='acpMenuItem'
						ucName='ACPMenuItem'
						languageCat='wcf.acp.menu.link'
						items=$project->getACPMenuItems()
						sortableActionName='wcf\\\\data\\\\project\\\\acp\\\\menu\\\\item\\\\ProjectACPMenuItemAction'}
				{/if}
			</div>
		</div>
		
		<div id="pageMenuItem">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectPageMenuItemAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="pageMenuItemFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getPageMenuItemCount() > 0}
							{lang}wcf.project.pageMenuItems{/lang}
							
							<span id="pageMenuItemCount" class="badge badgeInverse badgeCounter">{#$project->getPageMenuItemCount()}</span>
						{else}
							{lang}wcf.project.pageMenuItem.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getPageMenuItemCount() > 0}
					{include
						sandbox=true
						file='projectContentMenuTable'
						name='pageMenuItem'
						languageCat=''
						items=$project->getPageMenuItems()
						sortableActionName='wcf\\\\data\\\\project\\\\page\\\\menu\\\\item\\\\ProjectPageMenuItemAction'}
				{/if}
			</div>
		</div>
		
		<div id="userMenuItem">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserMenuItemAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userMenuItemFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserMenuItemCount() > 0}
							{lang}wcf.project.userMenuItems{/lang}
							
							<span id="userMenuItemCount" class="badge badgeInverse badgeCounter">{#$project->getUserMenuItemCount()}</span>
						{else}
							{lang}wcf.project.userMenuItem.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserMenuItemCount() > 0}
					{include
						sandbox=true
						file='projectContentMenuTable'
						name='userMenuItem'
						languageCat='wcf.user.usercp.menu.link'
						items=$project->getUserMenuItems()
						sortableActionName='wcf\\\\data\\\\project\\\\user\\\\menu\\\\item\\\\ProjectUserMenuItemAction'}
				{/if}
			</div>
		</div>
		
		<div id="userProfileMenuItem">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserProfileMenuItemAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userProfileMenuItemFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserProfileMenuItemCount() > 0}
							{lang}wcf.project.userProfileMenuItems{/lang}
							
							<span id="userProfileMenuItemCount" class="badge badgeInverse badgeCounter">{#$project->getUserProfileMenuItemCount()}</span>
						{else}
							{lang}wcf.project.userProfileMenuItem.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserProfileMenuItemCount() > 0}
					{include
						sandbox=true
						file='projectContentMenuTable'
						name='userProfileMenuItem'
						languageCat='wcf.user.profile.menu.link'
						items=$project->getUserProfileMenuItems()
						userProfileMenu=true
						sortableActionName='wcf\\\\data\\\\project\\\\user\\\\profile\\\\menu\\\\item\\\\ProjectUserProfileMenuItemAction'}
				{/if}
			</div>
		</div>

		{event name='menusContent'}
	</div>
	
	<div id="_system" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(eventListener)}"><span>{lang}wcf.project.eventListeners{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(templateListener)}"><span>{lang}wcf.project.templateListeners{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(cronjob)}"><span>{lang}wcf.project.cronjobs{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(languageVariable)}"><span>{lang}wcf.project.languageVariables{/lang}</span></a></li>
				{* TODO <li><a href="{@$__wcf->getAnchor(acpSearchProvider)}"><span>{lang}wcf.project.acpSearchProviders{/lang}</span></a></li> *}
				{* TODO <li><a href="{@$__wcf->getAnchor(userNotificationEvent)}"><span>{lang}wcf.project.userNotificationEvents{/lang}</span></a></li> *}
				
				{event name='systemSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="eventListener">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectEventListenerAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="eventListenerFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getEventListenerCount() > 0}
							{lang}wcf.project.eventListeners{/lang}
							
							<span id="eventListenerCount" class="badge badgeInverse badgeCounter">{#$project->getEventListenerCount()}</span>
						{else}
							{lang}wcf.project.eventListener.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getEventListenerCount() > 0}
					<table id="eventListenerTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnText">environment</th>
								<th class="columnText">eventName</th>
								<th class="columnText">listenerClassName</th>
								<th class="columnIcon">inherit</th>
								<th class="columnDigits">niceValue</th>
							</tr>
						</thead>
						
						<tbody>
							{assign var=currentEventClassName value=''}
							
							{foreach from=$project->getEventListeners() item=$listener}
								{if $listener->eventClassName != $currentEventClassName}
									{assign var=eventClassNameLastBackslash value=$listener->eventClassName|mb_strrpos:"\\"}
									
									<tr class="projectHighlightedRow sortableNoSorting">
										<td colspan="6"><span class="jsTooltip" title="{$listener->eventClassName}">{$listener->eventClassName|mb_substr:1+$eventClassNameLastBackslash}</span></td>
									</tr>
									
									{assign var=currentEventClassName value=$listener->eventClassName}
								{/if}
							
								<tr class="jsEventListenerRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectEventListenerEdit' id=$listener->listenerID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsEventListenerDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$listener->listenerID}" data-confirm-message="{lang}wcf.project.eventListener.delete.sure{/lang}"></span>
									</td>
									<td class="columnText">{$listener->environment}</td>

									<td class="columnText">{$listener->eventName}</td>

									{assign var=listenerClassNameLastBackslash value=$listener->listenerClassName|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$listener->listenerClassName}">{$listener->listenerClassName|mb_substr:1+$listenerClassNameLastBackslash}</td>

									<td class="columnIcon"><span class="icon icon16 icon-{if $listener->inherit}ok{else}remove{/if}"></span></td>
									<td class="columnDigits">{$listener->niceValue}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="templateListener">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectTemplateListenerAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="templateListenerFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getTemplateListenerCount() > 0}
							{lang}wcf.project.templateListeners{/lang}
							
							<span id="templateListenerCount" class="badge badgeInverse badgeCounter">{#$project->getTemplateListenerCount()}</span>
						{else}
							{lang}wcf.project.templateListener.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getTemplateListenerCount() > 0}
					<table id="templateListenerTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnText">name</th>
								<th class="columnText">environment</th>
								<th class="columnText">event</th>
							</tr>
						</thead>
						
						<tbody>
							{assign var=currentTemplateName value=""} 
							
							{foreach from=$project->getTemplateListeners() item=$listener}
								{if $listener->templateName != $currentTemplateName}
									<tr class="projectHighlightedRow sortableNoSorting">
										<td colspan="4">{$listener->templateName}</td>
									</tr>
									
									{assign var=currentTemplateName value=$listener->templateName}
								{/if}
								
								<tr class="jsTemplateListenerRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectTemplateListenerEdit' id=$listener->listenerID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsTemplateListenerDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$listener->listenerID}" data-confirm-message="{lang}wcf.project.templateListener.delete.sure{/lang}"></span>
									</td>
									<td class="columnTitle">{$listener->name}</td>
									<td class="columnText">{$listener->environment}</td>
									<td class="columnText">{$listener->eventName}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="cronjob">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectCronjobAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="cronjobFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getCronjobCount() > 0}
							{lang}wcf.project.cronjobs{/lang}
							
							<span id="cronjobCount" class="badge badgeInverse badgeCounter">{#$project->getCronjobCount()}</span>
						{else}
							{lang}wcf.project.cronjob.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getCronjobCount() > 0}
					<table id="cronjobTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnText">Description</th>
								<th class="columnDigits">m</th>
								<th class="columnDigits">h</th>
								<th class="columnDigits">D</th>
								<th class="columnDigits">M</th>
								<th class="columnDigits">DoW</th>
								<th class="columnText">className</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getCronjobs() item=$cronjob}
								<tr class="jsCronjobRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectCronjobEdit' id=$cronjob->cronjobID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsCronjobDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$cronjob->cronjobID}" data-confirm-message="{lang}wcf.project.cronjob.delete.sure{/lang}"></span>
									</td>
									<td class="columnText">{lang}{$cronjob->description}{/lang}</td>
									<td class="columnDigits">{$cronjob->startMinute}</td>
									<td class="columnDigits">{$cronjob->startHour}</td>
									<td class="columnDigits">{$cronjob->startDom}</td>
									<td class="columnDigits">{$cronjob->startMonth}</td>
									<td class="columnDigits">{$cronjob->startDow}</td>
									
									{assign var=cronjobClassNameLastBackslash value=$cronjob->className|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$cronjob->className}">{$cronjob->className|mb_substr:1+$cronjobClassNameLastBackslash}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="languageVariable">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectLanguageVariableAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="languageVariableFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getLanguageVariableCount() > 0}
							{lang}wcf.project.languageVariables{/lang}
							
							<span id="languageVariableCount" class="badge badgeInverse badgeCounter">{#$project->getLanguageVariableCount()}</span>
						{else}
							{lang}wcf.project.languageVariable.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getLanguageVariableCount() > 0}
					<table id="languageVariableTable" class="table">
						<tbody>
							{assign var='oldLanguageItem' value=''}
							{assign var='oldLanguageItemCategory' value=''}
							{assign var='newVariable' value=false}
							
							{cycle name=projectLanguageVariableRow values="1,2" print=false}
							{foreach from=$project->getLanguageVariables() item=$row}
								{cycle name=projectLanguageVariableRow print=false}
								{assign var=languageItemIDs value=''}
								
								{foreach from=$row key=$languageID item=$item}
									{if !$languageItemIDs|empty}{append var=languageItemIDs value=','}{/if}
									{append var=languageItemIDs value=$item->languageItemID}
								{/foreach}
								
								
								{foreach from=$row key=$languageID item=$item}
									{assign var='languageCategoryID' value=$item->languageCategoryID}
									{assign var='languageCategory' value=$languageCategories.$languageCategoryID}
									
									{if $oldLanguageItem != $item->languageItem}
										{assign var='newVariable' value=true}
										{assign var='oldLanguageItem' value=$item->languageItem}
										{assign var='categoryLength' value=$languageCategory->languageCategory|strlen}
									{/if}
									
									{if $oldLanguageItemCategory != $languageCategory->languageCategory}
										<tr class="projectHighlightedRow">
											<td colspan="3">{$languageCategory->languageCategory}</td>
										</tr>
										{assign var=oldLanguageItemCategory value=$languageCategory->languageCategory}
									{/if}
									
									<tr id="languageVariable-{$item->languageItemID}" class="jsLanguageVariableRow projectLanguageVariableRow-{cycle name=projectLanguageVariableRow advance=false}" data-language-item="{$item->languageItem}">
										{if $newVariable}
											{assign var=count value=$row|count}
											<td rowspan="{@$count + 1}" class="columnIcon">
												<a title="{lang}wcf.global.button.edit{/lang}" href="{link controller='ProjectLanguageVariableEdit'}refLanguageItemName={$item->languageItem}{/link}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
												<span class="icon icon16 icon-remove jsTooltip jsLanguageItemDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$item->languageItemID}" data-object-ids="[{@$languageItemIDs}]" data-confirm-message="{lang}wcf.project.languageVariable.delete.sure{/lang}"></span>
											</td>
											<td colspan="2" class="columnTitle"><span style="display: none">.</span><span>{$item->languageItem|substr:$categoryLength + 1}</span></td>
											
											</tr>
											<tr id="languageVariable-{$item->languageItemID}" class="jsLanguageVariableRow projectLanguageVariableRow-{cycle name=projectLanguageVariableRow advance=false}" data-language-item="{$item->languageItem}">
										{/if}
										<td class="columnIcon">{@$project->getLanguageIcon($languageID)}</td>
										<td class="columnText">{$item->languageItemValue}</td>
									</tr>
									{assign var='newVariable' value=false}
								{/foreach}
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		{* TODO
		<div id="acpSearchProvider">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectACPSearchProviderAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="acpSearchProviderFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getACPSearchProviderCount() > 0}
							{lang}wcf.project.acpSearchProviders{/lang}
							
							<span id="acpSearchProviderCount" class="badge badgeInverse badgeCounter">{#$project->getACPSearchProviderCount()}</span>
						{else}
							{lang}wcf.project.acpSearchProvider.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getACPSearchProviderCount() > 0}
					<table id="acpSearchProviderTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnTitle">providerName</th>
								<th class="columnText">className</th>
								<th class="columnDigits">showOrder</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getACPSearchProviders() item=$provider}
								<tr 
									class="jsACPSearchProviderRow sortableNode"
									data-object-id="{@$provider->providerID}"
									data-show-order="{@$provider->showOrder}"
									data-auto-show-order="{@$provider->autoShowOrder}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectACPSearchProviderEdit' id=$provider->providerID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsACPSearchProviderDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$provider->providerID}" data-confirm-message="{lang}wcf.project.acpSearchProvider.delete.sure{/lang}"></span>
									</td>
									<td class="columnTitle">{$provider->providerName}</td>

									{assign var=providerClassNameLastBackslash value=$provider->className|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$provider->className}">{$provider->className|mb_substr:1+$providerClassNameLastBackslash}</td>
									
									<td class="columnDigits columnShowOrder">
										{if $provider->autoShowOrder}
											<span class="icon icon-16 icon-asterisk jsTooltip" title="{lang}wcf.project.autoShowOrder{/lang}"></span>
										{else}
											<span>{$provider->showOrder}</span>
										{/if}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div> *}
		
		{* TODO
		<div id="userNotificationEvent">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectUserNotificationEventAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="userNotificationEventFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getUserNotificationEventCount() > 0}
							{lang}wcf.project.userNotificationEvents{/lang}
							
							<span id="userNotificationEventCount" class="badge badgeInverse badgeCounter">{#$project->getUserNotificationEventCount()}</span>
						{else}
							{lang}wcf.project.userNotificationEvent.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getUserNotificationEventCount() > 0}
					<table id="userNotificationEventTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnTitle">eventName</th>
								<th class="columnText">className</th>
								<th class="columnIcon">preset</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getUserNotificationEvents() item=$event}
								<tr class="jsUserNotificationEventRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectUserNotificationEventEdit' id=$event->eventID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsUserNotificationEventDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$event->eventID}" data-confirm-message="{lang}wcf.project.userNotificationEvent.delete.sure{/lang}"></span>
									</td>
									<td class="columnTitle">{$event->eventName}</td>

									{assign var=eventClassNameLastBackslash value=$event->className|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$event->className}">{$event->className|mb_substr:1+$eventClassNameLastBackslash}</td>
									
									<td class="columnIcon">
										{if $event->preset}<span class="icon icon16 icon-ok"></span>{/if}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div> *}
	
		{event name='systemContent'}
	</div>
	
	<div id="_files" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(file)}"><span>{lang}wcf.project.files{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(template)}"><span>{lang}wcf.project.templates{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(acpTemplate)}"><span>{lang}wcf.project.acpTemplates{/lang}</span></a></li>
				
				{event name='filesSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="file">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						{* TODO
						<a class="jsTooltip" href="{link controller='ProjectFileAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						*}
						
						<input id="fileFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getFileCount() > 0}
							{lang}wcf.project.files{/lang}
							
							<span id="fileCount" class="badge badgeInverse badgeCounter">{#$project->getFileCount()}</span>
						{else}
							{lang}wcf.project.file.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getFileCount() > 0}
					<table id="fileTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnTitle">{lang}wcf.project.file.filename{/lang}</th>
								<th class="columnText">{lang}wcf.project.file.application{/lang}</th>
				
								{event name='columnHeads'}
							</tr>
						</thead>
						
						<tbody>
							{assign var=currentDir value=''}
							
							{foreach from=$project->getFiles() item=$file}
								{assign var=dirname value=$file->filename|dirname}
								
								{if $dirname != $currentDir}
									<tr class="projectHighlightedRow">
										<td colspan="3"><span class="icon icon16 icon-{if $file->filename|strpos:"image/" !== false || $file->filename|strpos:"icon/" !== false}picture
										{elseif $file->filename|strpos:"system/event/listener/" !== false}headphones
										{elseif $file->filename|strpos:"system/cronjob/" !== false}time
										{elseif $file->filename|strpos:"system/" !== false}gear
										{elseif $file->filename|strpos:"action/" !== false}code
										{elseif $file->filename|strpos:"form/" !== false}code
										{elseif $file->filename|strpos:"page/" !== false}code
										{elseif $file->filename|strpos:"style/" !== false}desktop
										{elseif $file->filename|strpos:"js/" !== false}terminal
										{elseif $file->filename|strpos:"templates/" !== false}puzzle-piece
										{elseif $file->filename|strpos:"lib/acp/" !== false || $file->filename|strpos:"lib/data/" !== false}file-text{/if}"></span>&nbsp;&nbsp;&nbsp;{$dirname}</td>
									</tr>
									
									{assign var=currentDir value=$dirname}
								{/if}

								<tr class="jsFileRow">
									<td class="columnIcon">
										{event name='rowButtons'}
									</td>
									
									<td class="columnTitle">{$file->filename|basename}</td>
									
									{if !$project->isApplication}
										<td class="columnText">
											{$file->application}
										</td>
									{/if}
				
									{event name='columns'}
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="template">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						{* TODO
						<a class="jsTooltip" href="{link controller='ProjectTemplateAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						*}
						
						<input id="templateFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">

						{if $project->getTemplateCount() > 0}
							{lang}wcf.project.templates{/lang}

							<span id="templateCount" class="badge badgeInverse badgeCounter">{#$project->getTemplateCount()}</span>
						{else}
							{lang}wcf.project.template.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getTemplateCount() > 0}
					{include sandbox=true file='projectContentTemplateTable' name='template' tplDir='templates/' templates=$project->getTemplates()}
				{/if}
			</div>
		</div>
		
		<div id="acpTemplate">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						{* TODO
						<a class="jsTooltip" href="{link controller='ProjectACPTemplateAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						*}
						
						<input id="acpTemplateFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">

						{if $project->getACPTemplateCount() > 0}
							{lang}wcf.project.acpTemplates{/lang}

							<span id="acpTemplateCount" class="badge badgeInverse badgeCounter">{#$project->getACPTemplateCount()}</span>
						{else}
							{lang}wcf.project.acpTemplate.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getACPTemplateCount() > 0}
					{include sandbox=true file='projectContentTemplateTable' name='acpTemplate' tplDir='acp/templates/' templates=$project->getACPTemplates()}
				{/if}
			</div>
		</div>

		{event name='filesContent'}
	</div>
	
	<div id="_database" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(databaseTable)}"><span>{lang}wcf.project.databaseTables{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(databaseColumn)}"><span>{lang}wcf.project.databaseColumns{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(databaseIndex)}"><span>{lang}wcf.project.databaseIndices{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(databaseForeignKey)}"><span>{lang}wcf.project.databaseForeignKeys{/lang}</span></a></li>
				
				{event name='databaseSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="databaseTable">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectDatabaseTableAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="databaseTableFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getDatabaseTableCount() > 0}
							{lang}wcf.project.databaseTables{/lang}
							
							<span id="databaseTableCount" class="badge badgeInverse badgeCounter">{#$project->getDatabaseTableCount()}</span>
						{else}
							{lang}wcf.project.database.table.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getDatabaseTableCount() > 0}
					<table id="databaseTableTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<th class="columnTitle">databaseTable</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getDatabaseTables() item=$table}
								<tr class="jsDatabaseTableRow">
									<td class="columnIcon">
										{if $table->getLog()->isNew()}
											<a href="{link controller='ProjectDatabaseTableEdit' id=$table->getLog()->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
												<span class="icon icon16 icon-pencil"></span>
											</a>
										{else}
											<span class="icon icon16 icon-ban-circle jsTooltip" title="{lang}wcf.project.database.table.exportedNotEditable{/lang}"></span>
										{/if}
										
										<span
											class="icon icon16 icon-remove jsTooltip jsDatabaseDeleteButton pointer"
											title="{lang}wcf.global.button.delete{/lang}"
											data-object-id="{@$table->getLog()->getObjectID()}"
											data-confirm-message="{lang}wcf.project.database.table.delete.sure{/lang}"
										></span>
									</td>
									<td class="columnTitle">{$table->getName()}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="databaseColumn">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectDatabaseColumnAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="databaseColumnFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getDatabaseColumnCount() > 0}
							{lang}wcf.project.databaseColumns{/lang}
							
							<span id="databaseColumnCount" class="badge badgeInverse badgeCounter">{#$project->getDatabaseColumnCount()}</span>
						{else}
							{lang}wcf.project.database.column.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getDatabaseColumnCount() > 0}
					<table id="databaseColumnTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">databaseColumn</th>
								<th class="columnText">type</th>
								<th class="columnText">length / values</th>
								<th class="columnText">default</th>
								<th class="columnIcon">notNull</th>
								<th class="columnIcon">AI</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getDatabaseColumns() key=tableName item=columns}
								<tr class="projectHighlightedRow">
									<td colspan="8">{$tableName}</td>
								</tr>
								
								{foreach from=$columns item=column}
									<tr class="jsDatabaseColumnRow">
										<td class="columnIcon">
											<a href="{link controller='ProjectDatabaseColumnEdit' id=$column->getLog()->getObjectID()}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
												<span class="icon icon16 icon-pencil"></span>
											</a>
										
											<span
												class="icon icon16 icon-remove jsTooltip jsDatabaseDeleteButton pointer"
												title="{lang}wcf.global.button.delete{/lang}"
												data-object-id="{@$column->getLog()->getObjectID()}"
												data-confirm-message="{lang}wcf.project.database.column.delete.sure{/lang}"
											></span>
										</td>
										<td class="columnTitle">{$column->getName()}</td>
										<td class="columnText">{$column->getType()}</td>
										<td class="columnText">
											{if !$column->getValues()|empty}
												{@"<br />"|implode:$column->getValues()}
											{else}
												{if $column->getType() == 'char' ||
												$column->getType() == 'varchar' ||
												$column->getType() == 'decimal'} 
													{$column->getLength()}
												{/if}
											{/if}
										</td>
										<td class="columnText">
											{if $column->getDefault() === null}
												{if $column->getNotNull()}
													<span class="icon icon16 icon-ban-circle"></span> NONE
												{else}
													<span class="icon icon16 icon-check-empty"></span> NULL
												{/if}
											{else}
												{if $column->getDefault() == ''}
													<span class="icon icon16 fa-file-o"></span> EMPTY
												{else}
													{$column->getDefault()}
												{/if}
											{/if}
										</td>
										<td class="columnIcon">
											{if $column->getNotNull()}
												<span class="icon icon16 icon-ok"></span>
											{else}
												<span class="icon icon16 icon-remove"></span>
											{/if}
										</td>
										<td class="columnIcon">
											{if $column->getAutoIncrement()}
												<span class="icon icon16 icon-ok"></span>
											{else}
												<span class="icon icon16 icon-remove"></span>
											{/if}
										</td>
									</tr>
								{/foreach}
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="databaseIndex">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectDatabaseIndexAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="databaseIndexFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getDatabaseIndexCount() > 0}
							{lang}wcf.project.databaseIndices{/lang}
							
							<span id="databaseIndexCount" class="badge badgeInverse badgeCounter">{#$project->getDatabaseIndexCount()}</span>
						{else}
							{lang}wcf.project.database.index.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getDatabaseIndexCount() > 0}
					<table id="databaseIndexTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">databaseIndex</th>
								<th class="columnText">columns</th>
								<th class="columnText">type</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getDatabaseIndices() key=tableName item=indices}
								<tr class="projectHighlightedRow">
									<td colspan="4">{$tableName}</td>
								</tr>
								
								{foreach from=$indices item=index}
									<tr class="jsDatabaseIndexRow">
										<td class="columnIcon">
											<span
												class="icon icon16 icon-remove jsTooltip jsDatabaseDeleteButton pointer"
												title="{lang}wcf.global.button.delete{/lang}"
												data-object-id="{@$index->getLog()->getObjectID()}"
												data-confirm-message="{lang}wcf.project.database.index.delete.sure{/lang}"
											></span>
										</td>
										<td class="columnTitle">{$index->getName()}</td>
										<td class="columnText">
											{@"<br />"|implode:$index->getColumnNames()}
										</td>
										<td class="columnText">{$index->getType()}</td>
									</tr>
								{/foreach}
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="databaseForeignKey">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectDatabaseForeignKeyAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="databaseForeignKeyFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getDatabaseForeignKeyCount() > 0}
							{lang}wcf.project.databaseForeignKeys{/lang}
							
							<span id="databaseForeignKeyCount" class="badge badgeInverse badgeCounter">{#$project->getDatabaseForeignKeyCount()}</span>
						{else}
							{lang}wcf.project.database.foreignKey.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getDatabaseForeignKeyCount() > 0}
					<table id="databaseForeignKeyTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">columns</th>
								<th class="columnText">referenced</th>
								<th class="columnText">refTable</th>
								<th class="columnText">ON DELETE</th>
								<th class="columnText">ON UPDATE</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getDatabaseForeignKeys() key=tableName item=foreignKeys}
								<tr class="projectHighlightedRow">
									<td colspan="7">{$tableName}</td>
								</tr>
								
								{foreach from=$foreignKeys item=foreignKey}
									<tr class="jsDatabaseForeignKeyRow">
										<td class="columnIcon">
											<span
												class="icon icon16 icon-remove jsTooltip jsDatabaseDeleteButton pointer"
												title="{lang}wcf.global.button.delete{/lang}"
												data-object-id="{@$foreignKey->getLog()->getObjectID()}"
												data-confirm-message="{lang}wcf.project.database.foreignKey.delete.sure{/lang}"
											></span>
										</td>
										<td class="columnTitle">
											{@"<br />"|implode:$foreignKey->getColumnNames()}
										</td>
										<td class="columnText">
											{@"<br />"|implode:$foreignKey->getReferencedColumnNames()}
										</td>
										<td class="columnText">{$foreignKey->getReferencedTable()->getName()}</td>
										<td class="columnText">{$foreignKey->getOnDelete()}</td>
										<td class="columnText">{$foreignKey->getOnUpdate()}</td>
									</tr>
								{/foreach}
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		{event name='databaseContent'}
	</div>
	
	<div id="_objects" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(objectType)}"><span>{lang}wcf.project.objectTypes{/lang}</span></a></li>
				<li><a href="{@$__wcf->getAnchor(objectTypeDefinition)}"><span>{lang}wcf.project.definitions{/lang}</span></a></li>
				{* TODO <li><a href="{@$__wcf->getAnchor(coreObject)}"><span>{lang}wcf.project.coreObjects{/lang}</span></a></li> *}
				{* TODO <li><a href="{@$__wcf->getAnchor(clipboard)}"><span>{lang}wcf.project.clipboard{/lang}</span></a></li> *}
				
				{event name='objectsSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="objectType">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectObjectTypeAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="objectTypeFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getObjectTypeCount() > 0}
							{lang}wcf.project.objectTypes{/lang}
							
							<span id="objectTypeCount" class="badge badgeInverse badgeCounter">{#$project->getObjectTypeCount()}</span>
						{else}
							{lang}wcf.project.objectTypes.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getObjectTypeCount() > 0}
					<table id="objectTypeTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">objectType</th>
								<th class="columnText">className</th>
							</tr>
						</thead>
						
						<tbody>
							{assign var=currentDefinition value=''}
							
							{foreach from=$project->getObjectTypes() item=$type}
								{if $type->definitionID != $currentDefinition}
									<tr class="projectHighlightedRow">
										<td colspan="3">{$project->getObjectTypeDefinition($type->definitionID)->definitionName}</td>
									</tr>
									{assign var=currentDefinition value=$type->definitionID}
								{/if}
								
								<tr class="jsObjectTypeRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectObjectTypeEdit' id=$type->objectTypeID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsObjectTypeDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{$type->objectTypeID}" data-confirm-message="{lang}wcf.project.objectType.delete.sure{/lang}"></span>
									</td>
									<td class="columnTitle">{$type->objectType}</td>
									<td class="columnText">{$type->className}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		<div id="objectTypeDefinition">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectObjectTypeDefinitionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="objectTypeDefinitionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getObjectTypeDefinitionCount() > 0}
							{lang}wcf.project.objectTypeDefinitions{/lang}
							
							<span id="objectTypeDefinitionCount" class="badge badgeInverse badgeCounter">{#$project->getObjectTypeDefinitionCount()}</span>
						{else}
							{lang}wcf.project.objectTypeDefinitions.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getObjectTypeDefinitionCount() > 0}
					<table id="objectTypeDefinitionTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">definitionName</th>
								<th class="columnText">interfaceName</th>
							</tr>
						</thead>
						
						<tbody>
							{assign var=currentCategory value=''}
							
							{foreach from=$project->getObjectTypeDefinitions() item=$definition}
								{if $definition->categoryName != $currentCategory}
									<tr class="projectHighlightedRow">
										<td colspan="3">{$definition->categoryName}</td>
									</tr>
									{assign var=currentCategory value=$definition->categoryName}
								{/if}
								
								<tr class="jsObjectTypeDefinitionRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectObjectTypeDefinitionEdit' id=$definition->definitionID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsObjectTypeDefinitionDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{$definition->definitionID}" data-confirm-message="{lang}wcf.project.objectTypeDefinition.delete.sure{/lang}"></span>
									</td>
									<td class="columnTitle">{$definition->definitionName}</td>
									<td class="columnText">{$definition->interfaceName}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		{* TODO
		<div id="coreObject">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectCoreObjectAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="coreObjectFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getCoreObjectCount() > 0}
							{lang}wcf.project.coreObjects{/lang}
							
							<span id="coreObjectCount" class="badge badgeInverse badgeCounter">{#$project->getCoreObjectCount()}</span>
						{else}
							{lang}wcf.project.coreObject.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getCoreObjectCount() > 0}
					<table id="coreObjectTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">objectName</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getCoreObjects() item=$object}
								<tr class="jsCoreObjectRow">
									<td class="columnIcon">
										<a href="{link controller='ProjectCoreObjectEdit' id=$object->objectID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsCoreObjectDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{$object->objectID}" data-confirm-message="{lang}wcf.project.coreObject.delete.sure{/lang}"></span>
									</td>
									<td class="columnTitle">{$type->objectName}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		*}
		
		{* TODO
		<div id="clipboard">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectClipboardActionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="clipboardActionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getClipboardActionCount() > 0}
							{lang}wcf.project.clipboardActions{/lang}
							
							<span id="clipboardActionCount" class="badge badgeInverse badgeCounter">{#$project->getClipboardActionCount()}</span>
						{else}
							{lang}wcf.project.clipboard.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getClipboardActionCount() > 0}
					<script data-relocate="true">
						//<![CDATA[
						$(function() {
							new WCF.Project.Table.Sortable({
								containerID: 'clipboardActionTable',
								className: 'wcf\\data\\project\\clipboard\\action\\ProjectClipboardAction',
								categoryDataField: 'actionClassName',
								categorySelector: '.projectHighlightedRow',
								blockAboveFirstCategory: true
							});
						});
						//]]>
					</script>
				
					<table id="clipboardActionTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">actionName</th>
								<th class="columnText">page</th>
								<th class="columnDigits">showOrder</th>
							</tr>
						</thead>
						
						<tbody class="sortableList">
							{assign var=currentActionClassName value=''}
							
							{foreach from=$project->getClipboardActions() item=$clipboardAction}
								{if $clipboardAction->actionClassName != $currentActionClassName}
									<tr class="projectHighlightedRow" data-action-class-name="{$clipboardAction->actionClassName}">
										<td colspan="3">{$clipboardAction->actionClassName}</td>
									</tr>
									
									{assign var=currentActionClassName value=$clipboardAction->actionClassName}
								{/if}
								
								<tr
									class="jsClipboardActionRow sortableNode"
									data-object-id="{@$clipboardAction->actionID}"
									data-show-order="{@$clipboardAction->showOrder}"
									data-action-class-name="{$currentActionClassName}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectClipboardActionEdit' id=$clipboardAction->actionID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsClipboardActionDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{$clipboardAction->actionID}" data-confirm-message="{lang}wcf.project.clipboardAction.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{$clipboardAction->actionName}</td>
									
									<td class="columnText">
										{@"<br />"|implode:$clipboardAction->pages}
									</td>
									
									<td class="columnDigits columnShowOrder">
										{if $clipboardAction->showOrder != -1}
											<span>{$clipboardAction->showOrder}</span>
										{/if}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		*}
	
		{event name='objectsContent'}
	</div>
	
	<div id="_apperance" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				{* TODO <li><a href="{@$__wcf->getAnchor(style)}"><span>{lang}wcf.project.styles{/lang}</span></a></li> *}
				{* TODO <li><a href="{@$__wcf->getAnchor(bbcode)}"><span>{lang}wcf.project.bbcodes{/lang}</span></a></li> *}
				{* TODO <li><a href="{@$__wcf->getAnchor(smiley)}"><span>{lang}wcf.project.smileys{/lang}</span></a></li> *}
				<li><a href="{@$__wcf->getAnchor(dashboardBox)}"><span>{lang}wcf.project.dashboardBoxes{/lang}</span></a></li>
				{* TODO <li><a href="{@$__wcf->getAnchor(sitemap)}"><span>{lang}wcf.project.sitemap{/lang}</span></a></li> *}
				
				{event name='apperanceSubTabMenuTabs'}
			</ul>
		</nav>
		
		{* TODO
		<div id="style">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectStyleAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="styleFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getStyleCount() > 0}
							{lang}wcf.project.styles{/lang}
							
							<span id="styleCount" class="badge badgeInverse badgeCounter">{#$project->getStyleCount()}</span>
						{else}
							{lang}wcf.project.style.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getStyleCount() > 0}
					<table id="styleTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">styleName</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getStyles() item=$style}
								<tr 
									class="jsStyleRow"
									data-object-id="{@$style->styleID}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectStyleEdit' id=$style->styleID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsStyleDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$style->styleID}" data-confirm-message="{lang}wcf.project.style.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{$style->styleName}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		*}
		
		{* TODO
		<div id="bbcode">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectBBCodeAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="bbcodeFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getBBCodeCount() > 0}
							{lang}wcf.project.bbcodes{/lang}
							
							<span id="bbcodeCount" class="badge badgeInverse badgeCounter">{#$project->getBBCodeCount()}</span>
						{else}
							{lang}wcf.project.bbcode.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getBBCodeCount() > 0}
					<table id="bbcodeTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">Tag</th>
								<th class="columnText">className</th>
								<th class="columnIcon">Icon & Label</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getBBCodes() item=$bbcode}
								<tr 
									class="jsBBCodeRow sortableNode"
									data-object-id="{@$bbcode->bbcodeID}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectBBCodeEdit' id=$bbcode->bbcodeID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsBBCodeDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$bbcode->bbcodeID}" data-confirm-message="{lang}wcf.project.bbcode.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{$bbcode->bbcodeTag}</td>

									{assign var=bbcodeClassNameLastBackslash value=$bbcode->className|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$bbcode->className}">{$bbcode->className|mb_substr:1+$bbcodeClassNameLastBackslash}</td>
									
									<td class="columnIcon">
										{if $bbcode->wysiwygIcon|empty}
											<span{if !$bbcode->buttonLabel|empty} title="{lang}{$bbcode->buttonLabel}{/lang}"{/if} class="jsTooltip icon icon16 icon-circle-blank"></span>
										{else}
											<img{if !$bbcode->buttonLabel|empty} title="{lang}{$bbcode->buttonLabel}{/lang}"{/if} src="{@$__wcf->getPath()}icon/{$bbcode->wysiwygIcon}" />
										{/if}
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		*}
		
		{* TODO
		<div id="smiley">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectSmileyAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="smileyFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getSmileyCount() > 0}
							{lang}wcf.project.smilies{/lang}
							
							<span id="smileyCount" class="badge badgeInverse badgeCounter">{#$project->getSmileyCount()}</span>
						{else}
							{lang}wcf.project.smiley.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getSmileyCount() > 0}
					<table id="smileyTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">smileyTitle</th>
								<th class="columnText">smileyCode</th>
								<th class="columnText">aliases</th>
								<th class="columnText">smileyPath</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getSmilies() item=$smiley}
								<tr 
									class="jsSmileyRow"
									data-object-id="{@$smiley->smileyID}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectSmileyEdit' id=$smiley->smileyID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsSmiliesDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$smiley->smileyID}" data-confirm-message="{lang}wcf.project.smiley.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{$smiley->smileyTitle}</td>
									<td class="columnText">{$smiley->smileyCode}</td>
									<td class="columnText">{$smiley->aliases|nl2br}</td>
									<td class="columnText">{$smiley->smileyPath}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		*}
		
		<div id="dashboardBox">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectDashboardBoxAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="dashboardBoxFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getDashboardBoxCount() > 0}
							{lang}wcf.project.dashboardBoxes{/lang}
							
							<span id="dashboardBoxCount" class="badge badgeInverse badgeCounter">{#$project->getDashboardBoxCount()}</span>
						{else}
							{lang}wcf.project.dashboardBox.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getDashboardBoxCount() > 0}
					<table id="dashboardBoxTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">boxName</th>
								<th class="columnText">className</th>
							</tr>
						</thead>
						
						<tbody>
							{assign var=boxType value=''}
						
							{foreach from=$project->getDashboardBoxes() item=$box}
								{if $box->boxType != $boxType}
									<tr class="projectHighlightedRow">
										<td colspan="3">{$box->boxType}</td>
									</tr>
									{assign var=boxType value=$box->boxType}
								{/if}
								
								<tr 
									class="jsDashboardBoxRow"
									data-object-id="{@$box->boxID}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectDashboardBoxEdit' id=$box->boxID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsDashboardBoxDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$box->boxID}" data-confirm-message="{lang}wcf.project.dashboardBox.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{lang}{$box->boxName}{/lang}</td>

									{assign var=boxClassNameLastBackslash value=$box->className|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$box->className}">{$box->className|mb_substr:1+$boxClassNameLastBackslash}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		{* TODO
		<div id="sitemap">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectSitemapAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="sitemapFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getSitemapCount() > 0}
							{lang}wcf.project.sitemaps{/lang}
							
							<span id="sitemapCount" class="badge badgeInverse badgeCounter">{#$project->getSitemapCount()}</span>
						{else}
							{lang}wcf.project.sitemap.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getSitemapCount() > 0}
					<table id="dashboardBoxTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">sitemapName</th>
								<th class="columnText">className</th>
								<th class="columnDigits">showOrder</th>
							</tr>
						</thead>
						
						<tbody>
							{foreach from=$project->getSitemaps() item=$sitemap}
								<tr 
									class="jsSitemapRow sortableNode"
									data-object-id="{@$sitemap->sitemapID}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectSitemapEdit' id=$sitemap->sitemapID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsSitemapDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$sitemap->sitemapID}" data-confirm-message="{lang}wcf.project.sitemap.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{lang}{$sitemap->sitemapName}{/lang}</td>

									{assign var=sitemapClassNameLastBackslash value=$sitemap->className|mb_strrpos:"\\"}
									<td class="jsTooltip columnText" title="{$sitemap->className}">{$sitemap->className|mb_substr:1+$sitemapClassNameLastBackslash}</td>
									
									<td class="columnDigits">{$sitemap->showOrder}</td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		*}
		
		{event name='apperanceContent'}
	</div>
	
	<div id="_installation" class="container containerPadding tabMenuContainer tabMenuContent">
		<nav class="menu">
			<ul>
				<li><a href="{@$__wcf->getAnchor(instruction)}"><span>{lang}wcf.project.instructions{/lang}</span></a></li>
				
				{event name='installationSubTabMenuTabs'}
			</ul>
		</nav>
		
		<div id="instruction">
			<div class="projectTabularBox tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<a class="jsTooltip" href="{link controller='ProjectInstructionAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.add{/lang}">
							<span class="icon icon16 icon-plus"></span>
						</a>
						
						<input id="instructionFilterInput" class="projectTableFilterInput" placeholder="{lang}wcf.project.filter.placeholder{/lang}">
						
						{if $project->getInstructionCount() > 0}
							{lang}wcf.project.instructions{/lang}
							
							<span id="instructionCount" class="badge badgeInverse badgeCounter">{#$project->getInstructionCount()}</span>
						{else}
							{lang}wcf.project.instruction.empty{/lang}
						{/if}
					</h2>
				</header>
				
				{if $project->getInstructionCount() > 0}
					<script data-relocate="true">
						//<![CDATA[
						$(function() {
							new WCF.Project.Table.Sortable({
								containerID: 'instructionTable',
								className: 'wcf\\data\\project\\instruction\\ProjectInstructionAction',
								categoryDataField: 'position',
								categorySelector: '.projectHighlightedRow',
								blockAboveFirstCategory: true,
								orderName: 'executionOrder'
							});
						});
						//]]>
					</script>
					
					<table id="instructionTable" class="table">
						<thead>
							<tr>
								<th class="columnIcon"></th>
								<!-- // TODO language variables -->
								<th class="columnTitle">name</th>
								<th class="columnText">pip</th>
								<th class="columnText">versions</th>
								<th class="columnIcon">atInstall</th>
								<th class="columnDigit">order</th>
							</tr>
						</thead>
						
						<tbody class="sortableList">
							{assign var=currentPosition value=''}
						
							{foreach from=$project->getInstructions() item=$instruction}
								{if $currentPosition == '' && $instruction->position == 'end'}
									<tr class="projectHighlightedRow" data-position="start">
										<td colspan="6">{lang}wcf.project.instruction.position.start{/lang}</td>
									</tr>
									
									{assign var=currentPosition value='start'}
								{/if}
								
								{if $instruction->position != $currentPosition}
									<tr class="projectHighlightedRow" data-position="{$instruction->position}">
										<td colspan="6">{lang}wcf.project.instruction.position.{$instruction->position}{/lang}</td>
									</tr>
										
									{assign var=currentPosition value=$instruction->position}
								{/if}
								
								<tr 
									class="jsInstructionRow sortableNode"
									data-object-id="{@$instruction->instructionID}"
									data-execution-order="{@$instruction->executionOrder}"
									data-position="{$instruction->position}"
								>
									<td class="columnIcon">
										<a href="{link controller='ProjectInstructionEdit' id=$instruction->instructionID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
										<span class="icon icon16 icon-remove jsTooltip jsInstructionDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$instruction->instructionID}" data-confirm-message="{lang}wcf.project.instruction.delete.sure{/lang}"></span>
									</td>
									
									<td class="columnTitle">{$instruction->name}</td>
									
									<td class="columnText">{$instruction->pip}</td>
									
									<td class="columnText">{@$instruction->versions|nl2br}</td>
									
									<td class="columnIcon">
										{if $instruction->atInstall}
											<span class="icon icon16 icon-ok"></span>
										{/if}
									</td>
									
									<td class="columnDigit columnExecutionOrder">{$instruction->executionOrder}</td>
								</tr>
							{/foreach}
							
							{if $currentPosition == 'start'}
								<tr class="projectHighlightedRow" data-position="end">
									<td colspan="6">{lang}wcf.project.instruction.position.end{/lang}</td>
								</tr>
								
								{assign var=currentPosition value='start'}
							{/if}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
		
		{event name='installationContent'}
	</div>
	
	{event name='tabMenuContent'}
</div>

{include file='footer'}