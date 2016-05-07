<table id="{@$name}Table" class="table">
	<thead>
		<tr>
			<th class="columnIcon"></th>
			<th class="columnTitle">templateName</th>
			<th class="columnText">application</th>
			
			{event name='columnHeads'}
		</tr>
	</thead>
	
	<tbody>
		{assign var=ucName value=$name|ucfirst}
		
		{foreach from=$templates item=$template}
			<tr class="js{$ucName}Row">
				<td class="columnIcon">
					{event name='rowButtons'}
				</td>
				<td class="columnTitle">{$template->templateName}</td>
				<td class="columnText">{$template->application}</td>
				
				{event name='columns'}
			</tr>
		{/foreach}
	</tbody>
</table>

{event name='table'}