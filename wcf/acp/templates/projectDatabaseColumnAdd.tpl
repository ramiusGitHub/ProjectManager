{include file='header' pageTitle='wcf.project.database.column.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.database.column.{@$action}{/lang}</h1>
</header>

{include file='projectDatabaseFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#databaseColumn{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectDatabaseColumnAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.database.column.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.database.column.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectDatabaseColumnAdd'}{/link}{else}{link controller='ProjectDatabaseColumnEdit' id=$object->getObjectID()}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		{capture assign='tableField'}
			<dl{if $errorType.sqlTable|isset} class="formError"{/if}>
				<dt>
					<label for="sqlTable">
						{lang}wcf.project.database.column.sqlTable{/lang}
					</label>
				</dt>
				<dd>
					{if $action == 'add'}
						<select name="sqlTable" id="sqlTable">
							<option value="" disabled="disabled"{if $sqlTable|empty} selected="selected"{/if}>{lang}wcf.project.database.column.sqlTable.placeholder{/lang}</option>
							
							{foreach from=$tables item=table}
								<option value="{$table->getName()}"{if $sqlTable == $table->getName()} selected="selected"{/if}>{$table->getName()}</option>
							{/foreach}
						</select>
						
						{if $errorType.sqlTable|isset}
							<small class="innerError">
								{if $errorType.sqlTable == 'empty'}
									{lang}wcf.global.form.error.empty{/lang}
								{else}
									{lang}wcf.project.error.database.column.sqlTable.{@$errorType.tableName}{/lang}
								{/if}
							</small>
						{/if}
						
						<small>{lang}wcf.project.database.column.sqlTable.description{/lang}</small>
					{else}
						{$sqlTable}
					{/if}
				</dd>
			</dl>
		{/capture}
		
		{include file='__projectDatabaseColumnData'}
		
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