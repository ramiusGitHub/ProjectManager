{include file='header' pageTitle='wcf.project.database.index.'|concat::$action}

<script data-relocate="true" src="{@$__wcf->getPath()}acp/js/WCF.Project{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@$__wcfVersion}"></script>
<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Project.Database.IndexAddTable();
	});
	//]]>
</script>

<header class="boxHeadline">
	<h1>{lang}wcf.project.database.index.{@$action}{/lang}</h1>
</header>

{include file='projectDatabaseFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#databaseIndex{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectDatabaseIndexAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.database.index.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.database.index.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectDatabaseIndexAdd'}{/link}{else}{link controller='ProjectDatabaseIndexEdit' id=$indexID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
			
			<dl{if $errorType.sqlIndex|isset} class="formError"{/if}>
				<dt>
					<label for="sqlIndex">
						{lang}wcf.project.database.index.sqlIndex{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						id="sqlIndex"
						name="sqlIndex"
						value="{$sqlIndex}"
						autofocus="autofocus"
						pattern="^(?!.*\_fk$)[a-zA-Z_][a-zA-Z0-9_]*$"
						maxlength="64"
						class="long"
					/>
					
					{if $errorType.sqlIndex|isset}
						<small class="innerError">
							{if $errorType.sqlIndex == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.database.index.sqlIndex.{$errorType.sqlIndex}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.database.index.sqlIndex.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.sqlTable|isset} class="formError"{/if}>
				<dt>
					<label for="sqlTable">
						{lang}wcf.project.database.index.sqlTable{/lang}
					</label>
				</dt>
				<dd>
					<select name="sqlTable" id="sqlTable">
						{foreach from=$tables key=tableName item=table}
							<option value="{$tableName}"{if $sqlTable == $tableName} selected="selected"{/if}>{$tableName}</option>
						{/foreach}
					</select>
					
					{if $errorType.sqlTable|isset}
						<small class="innerError">
							{if $errorType.sqlTable == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.database.index.sqlTable.{@$errorType.sqlTable}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.database.index.sqlTable.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.key|isset} class="formError"{/if}>
				<dt>
					<label for="key">
						{lang}wcf.project.database.index.key{/lang}
					</label>
				</dt>
				<dd>
					<select name="key" id="key">
						{foreach from=$keys item=k}
							<option value="{$k}"{if $key == $k} selected="selected"{/if}>{$k}</option>
						{/foreach}
					</select>
					
					{if $errorType.key|isset}
						<small class="innerError">
							{if $errorType.key == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.database.index.key.{@$errorType.key}{/lang}
							{/if}
						</small>
					{/if}
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.database.index.columns{/lang}</legend>
			
			{if $errorType.columns|isset}
				<small class="innerError">
					{if $errorType.columns == 'empty'}
						{lang}wcf.global.form.error.empty{/lang}
					{else}
						{lang}wcf.project.error.database.index.columns.{@$errorType.columns}{/lang}
					{/if}
				</small>
			{/if}
			
			<div class="tabularBox tabularBoxTitle marginTop">
				<table id="projectForeignKeyAddTable" class="table">
					<thead>
						<tr>
							<th class="left">{lang}wcf.project.database.foreignKey.columns{/lang}</th>
							<th class="right">{lang}wcf.project.database.foreignKey.selecetedColumns{/lang}</th>
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
														/>
														{$column->getName()}
													</li>
												{/if}
											{/foreach}
										{/if}
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