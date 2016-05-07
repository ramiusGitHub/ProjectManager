{include file='header' pageTitle='wcf.project.database.table.'|concat::$action}

<header class="boxHeadline">
	<h1>{lang}wcf.project.database.table.{@$action}{/lang}</h1>
</header>

{include file='projectDatabaseFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#databaseTable{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='ProjectDatabaseTableAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.database.table.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.database.table.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='ProjectDatabaseTableAdd'}{/link}{else}{link controller='ProjectDatabaseTableEdit' id=$object->getObjectID()}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
		
		{capture assign=tableField}
			<dl{if $errorType.sqlTable|isset} class="formError"{/if}>
				<dt>
					<label for="sqlTable">
						{lang}wcf.project.database.table.sqlTable{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						id="sqlTable"
						name="sqlTable"
						value="{$sqlTable}"
						required="required"
						autofocus="autofocus"
						pattern="^[a-zA-Z_][a-zA-Z0-9_]*$"
						maxlength="64"
						class="long"
					/>
					
					{if $errorType.sqlTable|isset}
						<small class="innerError">
							{if $errorType.sqlTable == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}wcf.project.error.database.table.name.{$errorType.sqlTable}{/lang}
							{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.database.table.sqlTable.description{/lang}</small>
				</dd>
			</dl>
		{/capture}
			
		{if $action == 'edit'}
			<fieldset>
				<legend>{lang}wcf.project.general{/lang}</legend>
				
				{@$tableField}
			</fieldset>
		{else}
			{include file='__projectDatabaseColumnData'}
		{/if}
		
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