{assign var=ucName value=$name|ucfirst}

{if $sortableActionName|isset}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Project.Table.Sortable({
				containerID: '{@$name}CategoryTable',
				className: '{@$sortableActionName}',
				categoryDataField: 'parentCategoryName',
				categorySelector: '.projectHighlightedRow'
			});
		});
		//]]>
	</script>
{/if}

{event name='javascriptInclude'}

<table id="{@$name}CategoryTable" class="table">
	<thead>
		<tr>
			<th class="columnIcon"></th>
			<th class="columnTitle">categoryName</th>
			<th class="columnDigits">showOrder</th>
			<th class="columnText">permissions</th>
			<th class="columnText">options</th>
			
			{event name='columnHeads'}
		</tr>
	</thead>
	
	<tbody class="sortableList">
		{if $additionalName|isset}{append var='additionalName' value='.'}{else}{assign var='additionalName' value=''}{/if}
		{assign var=ucName value=$name|ucfirst}
		{assign var=controller value='Project'|concat:$ucName:'CategoryEdit'}
		{assign var=currentParent value=''}
	
		{foreach from=$categories item=$category}
			{assign var='categoryName' value=$languageCat|concat:".":$additionalName:"category.":$category->categoryName}
			
			{if $category->parentCategoryName != $currentParent}
				<tr class="projectHighlightedRow sortableNoSorting" data-category-name="{$category->parentCategoryName}">
					<td colspan="6">{$category->parentCategoryName}</td>
				</tr>
				{assign var=currentParent value=$category->parentCategoryName}
			{/if}
			
			<tr
				class="js{@$ucName}CategoryRow sortableNode"
				data-object-id="{@$category->categoryID}"
				data-show-order="{@$category->showOrder}"
				data-auto-show-order="{@$category->autoShowOrder}"
				data-parent-category-name="{$currentParent}"	
			>
				<td class="columnIcon">
					<a href="{link controller=$controller id=$category->categoryID}packageID={@$packageID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					<span class="icon icon16 icon-remove jsTooltip js{@$ucName}CategoryDeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$category->categoryID}" data-confirm-message="{lang}wcf.project.{@$name}.categoryDelete.sure{/lang}"></span>
					
					{event name='rowButtons'}
				</td>
				
				<td class="columnTitle">{$category->categoryName}</td>
				
				<td class="columnDigits columnShowOrder">
					{if $category->autoShowOrder}
						<span class="icon icon-16 icon-asterisk jsTooltip" title="{lang}wcf.project.autoShowOrder{/lang}"></span>
					{else}
						<span>{@$category->showOrder}</span>
					{/if}
				</td>
				
				<td class="columnText">{@","|str_replace:"<br />":$category->permissions}</td>
				
				<td class="columnText">{@","|str_replace:"<br />":$category->options}</td>
				
				{event name='columns'}
			</tr>
		{/foreach}
	</tbody>
</table>

{event name='table'}