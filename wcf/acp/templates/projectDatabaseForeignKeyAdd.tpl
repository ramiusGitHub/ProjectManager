{include file='header' pageTitle='wcf.project.database.foreignKey.'|concat::$action}

<script data-relocate="true" src="{@$__wcf->getPath()}acp/js/WCF.Project{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Project.Database.ForeignKeyAddTable();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.project.database.foreignKey.{@$action}{/lang}</h1>
</header>

{include file='projectDatabaseFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#databaseForeignKey{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectDatabaseForeignKeyAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.database.foreignKey.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.database.foreignKey.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectDatabaseForeignKeyAdd'}{/link}{else}{link controller='ProjectDatabaseForeignKeyEdit' id=$foreignKeyID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.deleteAction|isset} class="formError"{/if}>
				<dt>
					<label for="deleteAction">
						{lang}wcf.project.database.foreignKey.deleteAction{/lang}
					</label>
				</dt>
				<dd>
					<select name="deleteAction" id="deleteAction">
						{foreach from=$actions item=a}
							<option value="{$a}"{if $deleteAction == $a} selected="selected"{/if}>{$a}</option>
						{/foreach}
					</select>
					
					{if $errorType.deleteAction|isset}
						<small class="innerError">
							{if $errorType.deleteAction == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.database.foreignKey.deleteAction.{@$errorType.deleteAction}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.database.foreignKey.deleteAction.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.updateAction|isset} class="formError"{/if}>
				<dt>
					<label for="updateAction">
						{lang}wcf.project.database.foreignKey.updateAction{/lang}
					</label>
				</dt>
				<dd>
					<select name="updateAction" id="updateAction">
						{foreach from=$actions item=a}
							<option value="{$a}"{if $updateAction == $a} selected="selected"{/if}>{$a}</option>
						{/foreach}
					</select>
					
					{if $errorType.updateAction|isset}
						<small class="innerError">
							{if $errorType.updateAction == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.database.foreignKey.updateAction.{@$errorType.updateAction}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.database.foreignKey.updateAction.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='actions'}
		</fieldset>
		
		<fieldset>
			<legend>
				<label>{lang}wcf.project.database.foreignKey.childTable{/lang}</label>
				<label style="float: right;">{lang}wcf.project.database.foreignKey.parentTable{/lang}</label>
			</legend>
			
			<div class="tabularBox tabularBoxTitle marginTop">
				<header>
					<h2>
						<select name="sqlTable" id="sqlTable">
							{foreach from=$tables key=tableName item=columns}
								<option value="{$tableName}"{if $sqlTable == $tableName} selected="selected"{/if}>{$tableName}</option>
							{/foreach}
						</select>
						
						<select style="float: right;" name="referencedSqlTable" id="referencedSqlTable">
							{foreach from=$tables key=tableName item=columns}
								<option value="{$tableName}"{if $referencedSqlTable == $tableName} selected="selected"{/if}>{$tableName}</option>
							{/foreach}
						</select>
					</h2>
				</header>
				
				<table id="projectForeignKeyAddTable" class="table">
					<thead>
						<tr>
							<th class="left">{lang}wcf.project.database.foreignKey.columns{/lang}</th>
							<th class="right">{lang}wcf.project.database.foreignKey.selecetedColumns{/lang}</th>
							<th class="left">{lang}wcf.project.database.foreignKey.referencedColumns{/lang}</th>
							<th class="right">{lang}wcf.project.database.foreignKey.columns{/lang}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td id="availableColumns" class="left">
								{foreach from=$tables key=tableName item=table}
									<ul style="display: none;" data-table-name="{$tableName}">
										{foreach from=$table->getColumns() item=column}
											{if !$selectedColumns.$tableName|isset || !$column->getName()|in_array:$selectedColumns.$tableName}
												<li>
													<input
														type="hidden"
														disabled="disabled"
														value="{$column->getName()}"
														name="columns[{$tableName}][]"
														data-type="{$column->getType()}"
													/>
													{$column->getName()}
												</li>
											{/if}
										{/foreach}
									</ul>
								{/foreach}
							</td>
							<td id="selectedColumns" class="right">
								{foreach from=$tables key=tableName item=table}
									<ul style="display: none;" data-table-name="{$tableName}">
										{if $selectedColumns.$tableName|isset}
											{foreach from=$table->getColumns() item=column}
												{if $column->getName()|in_array:$selectedColumns.$tableName}
													<li>
														<input
															type="hidden"
															value="{$column->getName()}"
															name="columns[{$tableName}][]"
															data-type="{$column->getType()}"
														/>
														{$column->getName()}
													</li>
												{/if}
											{/foreach}
										{/if}
									</ul>
								{/foreach}
			
								{if $errorType.selectedColumns|isset}
									<small class="innerError">
										{if $errorType.selectedColumns == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.project.error.database.foreignKey.selectedColumns.{@$errorType.selectedColumns}{/lang}
										{/if}
									</small>
								{/if}
							</td>
							<td id="selectedReferencedColumns" class="left">
								{foreach from=$tables key=tableName item=table}
									<ul style="display: none;" data-table-name="{$tableName}">
										{if $referencedColumns.$tableName|isset}
											{foreach from=$table->getColumns() item=column}
												{if $column->getName()|in_array:$referencedColumns.$tableName}
													<li>
														<input
															type="hidden"
															value="{$column->getName()}"
															name="referencedColumns[{$tableName}][]"
															data-type="{$column->getType()}"
														/>
														{$column->getName()}
													</li>
												{/if}
											{/foreach}
										{/if}
									</ul>
								{/foreach}
								
								{if $errorType.referencedColumns|isset}
									<small class="innerError">
										{if $errorType.referencedColumns == 'empty'}
											{lang}wcf.global.form.error.empty{/lang}
										{else}
											{lang}wcf.project.error.database.foreignKey.referencedColumns.{@$errorType.referencedColumns}{/lang}
										{/if}
									</small>
								{/if}
							</td>
							<td id="availableReferencedColumns" class="right">
								{foreach from=$tables key=tableName item=table}
									<ul style="display: none;" data-table-name="{$tableName}">
										{foreach from=$table->getColumns() item=column}
											{if !$referencedColumns.$tableName|isset || !$column->getName()|in_array:$referencedColumns.$tableName}
												<li>
													<input
														type="hidden"
														disabled="disabled"
														value="{$column->getName()}"
														name="referencedColumns[{$tableName}][]"
														data-type="{$column->getType()}"
													/>
													{$column->getName()}
												</li>
											{/if}
										{/foreach}
									</ul>
								{/foreach}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			
			{event name='columns'}
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