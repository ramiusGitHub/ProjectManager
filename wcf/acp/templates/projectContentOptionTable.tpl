{assign var=ucName value=$name|ucfirst}

{if $sortableActionName|isset}
	<script data-relocate="true">
		//<![CDATA[
		$(function() {
			new WCF.Project.Table.Sortable({
				containerID: '{@$name}Table',
				className: '{@$sortableActionName}',
				categoryDataField: 'categoryName',
				categorySelector: '.projectHighlightedRow',
				blockAboveFirstCategory: true
			});
		});
		//]]>
	</script>
{/if}

{event name='javascriptInclude'}

<table id="{@$name}Table" class="table">
	<thead>
		<tr>
			<th class="columnIcon"></th>
			<th class="columnTitle">optionName</th>
			<th class="columnText">optionType</th>
			<th class="columnDigits">showOrder</th>
			
			{event name='columnHeads'}
		</tr>
	</thead>
	
	<tbody class="sortableList">
		{if $additionalName|isset}{append var='additionalName' value='.'}{else}{assign var='additionalName' value=''}{/if}
		{assign var=controller value='Project'|concat:$ucName:'Edit'}
		{assign var=currentCategory value=''}
	
		{foreach from=$options item=option}
			{assign var='optionName' value=$languageCat|concat:".":$additionalName:$option->optionName}
			{assign var='optionDesc' value=$optionName|concat:".description"}
			
			{if $option->categoryName != $currentCategory}
				<tr class="projectHighlightedRow" data-category-name="{$option->categoryName}">
					<td colspan="5">{$option->categoryName}</td>
				</tr>
				
				{assign var=currentCategory value=$option->categoryName}
			{/if}
			
			<tr
				class="js{@$ucName}Row sortableNode"
				data-object-id="{@$option->optionID}"
				data-show-order="{@$option->showOrder}"
				data-auto-show-order="{@$option->autoShowOrder}"
				data-is-hidden="{$option->hidden|intval}"
				data-category-name="{$currentCategory}"	
			>
				<td class="columnIcon">
					<a href="{link controller=$controller id=$option->optionID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 icon-pencil"></span></a>
					<span class="icon icon16 icon-remove jsTooltip js{@$ucName}DeleteButton pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$option->optionID}" data-confirm-message="{lang}wcf.project.{@$name}.delete.sure{/lang}"></span>
					
					{event name='rowButtons'}
				</td>
				<td class="columnTitle">
					<span class="jsTooltip" title="{if $option->defaultValue|isset}{$option->defaultValue}{else}{$option->optionValue}{/if}">{$option->optionName}</span>
				</td>
				<td class="columnText" title="{$option->optionType}">{$option->optionType}</td>
				<td class="columnDigits columnShowOrder">
					{if $option->hidden}
						<span class="icon icon-16 icon-eye-close jsTooltip" title="{lang}wcf.project.option.hidden{/lang}"></span>
					{elseif $option->autoShowOrder}
						<span class="icon icon-16 icon-asterisk jsTooltip" title="{lang}wcf.project.autoShowOrder{/lang}"></span>
					{else}	
						<span>{@$option->showOrder}</span>
					{/if}
				</td>
				
				{event name='columns'}
			</tr>
		{/foreach}
	</tbody>
</table>

{event name='table'}