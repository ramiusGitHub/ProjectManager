{include file='header' pageTitle='wcf.project.'|concat:$type:'Category.':$action}

{assign var=ucType value=$type|ucfirst}
{assign var=ucAction value=$action|ucfirst}

<header class="boxHeadline">
	<h1>{lang}wcf.project.{@$type}Category.{@$action}{/lang}</h1>
</header>

{include file='projectFormError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<div class="contentNavigation">
	<nav>
		<ul>
			<li><a href="{link controller='Project' id=$packageID}#{@$type}{/link}" title="{lang}wcf.project.view{/lang}" class="button"><span class="icon icon16 fa-cube"></span> <span>{$project->getName()}</span></a></li>

			{if $action != 'add'}<li><a href="{link controller='Project'|concat:$ucType:'CategoryAdd'}packageID={@$packageID}{/link}" title="{lang}wcf.project.{@$type}.add{/lang}" class="button"><span class="icon icon16 icon-plus"></span> <span>{lang}wcf.project.{@$type}Category.add{/lang}</span></a></li>{/if}			
			
			{event name='contentNavigationButtons'}
		</ul>
	</nav>
</div>

<form method="post" action="{if $action == 'add'}{link controller='Project'|concat:$ucType:'CategoryAdd'}{/link}{else}{link controller='Project'|concat:$ucType:'CategoryEdit' id=$categoryID}{/link}{/if}">
	<div class="container containerPadding marginTop">
		{include file='projectDataPackageSelect'}
			
		<fieldset>
			<legend>{lang}wcf.project.general{/lang}</legend>
		
			<dl{if $errorType.categoryName|isset} class="formError"{/if}>
				<dt>
					<label for="categoryName">
						{lang}wcf.project.optionCategory.categoryName{/lang}
					</label>
				</dt>
				<dd>
					<input
						autofocus="autofocus"
						type="text"
						class="inputText long"
						name="categoryName"
						id="categoryName"
						value="{$categoryName}"
						maxlength="{@PROJECT_MANAGER_NAME_MAX_LENGTH}"
						{if $action == 'edit' && $project->getCurrentVersion()->getVersionID() != $object->createdVersionID}disabled="disabled"{/if}
					/>
					
					{if $errorType.categoryName|isset}
						<small class="innerError">
							{if $errorType.categoryName == 'empty'}{lang}wcf.global.form.error.empty{/lang}
							{else}{lang}wcf.project.error.categoryName.{@$errorType.categoryName}{/lang}{/if}
						</small>
					{/if}
					
					<small>{lang}wcf.project.optionCategory.categoryName.description{/lang}</small>
				</dd>
			</dl>

			<dl{if $errorType.displayedName|isset} class="formError"{/if}>
				<dt>
					<label for="displayedName">
						{lang}wcf.project.optionCategory.displayedName{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="text"
						id="displayedName"
						name="displayedName"
						value=""
						class="long"
					/>
					
					{if $errorType.displayedName|isset}
						<small class="innerError">{lang}wcf.project.optionCategory.displayedName.error.{@$errorType.displayedName}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.optionCategory.displayedName.description{/lang}</small>
				</dd>
			</dl>
			{include file='multipleLanguageInputJavascript' elementIdentifier='displayedName' forceSelection=true}
			
			<dl{if $errorType.parentCategoryName|isset} class="formError"{/if}>
				<dt>
					<label for="parentCategoryID">
						{lang}wcf.project.optionCategory.parentCategory{/lang}
					</label>
				</dt>
				<dd>
					{htmlOptions options=$parentCategories selected=$parentCategoryID name='parentCategoryID'}
					
					{if $errorType.parentCategoryID|isset}
						<small class="innerError">{lang}wcf.project.error.parentCategoryID.{@$errorType.parentCategoryID}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.optionCategory.parentCategory.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='general'}
		</fieldset>
		
		<fieldset>
			<legend>{lang}wcf.project.optionCategory.additional{/lang}</legend>
			
			<dl{if $errorType.showOrder|isset} class="formError"{/if}>
				<dt>
					<label for="showOrder">
						{lang}wcf.project.optionCategory.showOrder{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="number"
						min="0"
						name="showOrder"
						id="showOrder"
						value="{$showOrder}"
					/>
					
					{if $errorType.showOrder|isset}
						<small class="innerError">{lang}wcf.project.error.showOrder.{@$errorType.showOrder}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.optionCategory.showOrder.description{/lang}</small>
				</dd>
			</dl>
			
			<dl>
				<dt class="reversed">
					<label for="autoShowOrder">
						{lang}wcf.project.autoShowOrder{/lang}
					</label>
				</dt>
				<dd>
					<input
						type="checkbox"
						id="autoShowOrder"
						name="autoShowOrder"
						value="1"
						{if $autoShowOrder}checked="checked"{/if}
					/>
				
					<small>{lang}wcf.project.autoShowOrder.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.permissions|isset} class="formError"{/if}>
				<dt>
					<label for="permissions">
						{lang}wcf.project.optionCategory.permissions{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="permissions"
						id="permissions"
						rows="5"
						cols="40"
					>{$permissions}</textarea>
					
					{if $errorType.permissions|isset}
						<small class="innerError">{lang}wcf.project.error.permissions.{@$errorType.permissions}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.optionCategory.permissions.description{/lang}</small>
				</dd>
			</dl>
			
			<dl{if $errorType.options|isset} class="formError"{/if}>
				<dt>
					<label for="options">
						{lang}wcf.project.optionCategory.options{/lang}
					</label>
				</dt>
				<dd>
					<textarea
						name="options"
						id="options"
						rows="5"
						cols="40"
					>{$options}</textarea>
					
					{if $errorType.options|isset}
						<small class="innerError">{lang}wcf.project.error.options.{@$errorType.options}{/lang}</small>
					{/if}
					
					<small>{lang}wcf.project.optionCategory.options.description{/lang}</small>
				</dd>
			</dl>
			
			{event name='additional'}
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