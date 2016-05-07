{if !$ucName|isset}{assign var=ucName value=$name|ucfirst}{/if}

{assign var=item value=$items|reset}
{assign var=hasParent value=$item->parentMenuItem|isset}

<script data-relocate="true">
	//<![CDATA[
	$(function() {
		new WCF.Project.Table.Sortable({
			containerID: '{@$name}Table',
			className: '{@$sortableActionName}',
			{if $hasParent}
				categoryDataField: 'parentMenuItem',
				categorySelector: '.projectHighlightedRow',
			{/if}
		});
	});
	//]]>
</script>

{event name='javascriptInclude'}

<table id="{@$name}Table" class="table">
	<thead>
	
		<tr>
			<th class="columnIcon"></th>
			<th class="columnTitle">menuItem</th>
			<th class="columnDigits">showOrder</th>
			
			{event name='columnHeads'}
		</tr>
	</thead>
	
	<tbody class="sortableList">
		{assign var='length' value=$languageCat|strlen}
		{assign var='length' value=$length+1}
		{assign var=controller value='Project'|concat:$ucName:'Edit'}
		{assign var=currentParent value=''}
	
		{foreach from=$items item=$item}
			{if $hasParent && $item->parentMenuItem != $currentParent && !$item->parentMenuItem|empty}
				<tr class="projectHighlightedRow sortableNoSorting" data-parent-menu-item="{$item->parentMenuItem}">
					<td colspan="5">{$item->parentMenuItem}</td>
				</tr>
				{assign var=currentParent value=$item->parentMenuItem}
			{/if}
			
			<tr
				class="js{@$ucName}Row sortableNode"
				data-object-id="{@$item->menuItemID}"
				data-show-order="{@$item->showOrder}"
				data-auto-show-order="{@$item->autoShowOrder}"
				{if $hasParent}data-parent-menu-item="{$currentParent}"{/if}	
			>
				<td class="columnIcon">
					<a title="{lang}wcf.global.button.edit{/lang}" href="{link controller=$controller id=$item->menuItemID}packageID={@$packageID}{/link}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					<span class="icon icon16 icon-remove jsTooltip js{@$ucName}DeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$item->menuItemID}" data-confirm-message="{lang}wcf.project.{@$name}.delete.sure{/lang}"></span>
					
					{event name='rowButtons'}
				</td>
				
				<td class="columnTitle">{if $languageCat|strlen > 0 && $item->menuItem|strpos:$languageCat === 0}{$item->menuItem|substr:$length}{else}{$item->menuItem}{/if}</td>
				
				<td class="columnDigits columnShowOrder">
					{if $item->autoShowOrder}
						<span class="icon icon-16 icon-asterisk jsTooltip" title="{lang}wcf.project.autoShowOrder{/lang}"></span>
					{else}
						<span>{$item->showOrder}</span>
					{/if}
				</td>
				
				{event name='columns'}
			</tr>
		{/foreach}
	</tbody>
</table>

{event name='table'}