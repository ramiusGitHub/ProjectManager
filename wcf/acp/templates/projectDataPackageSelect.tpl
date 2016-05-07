<dl>
	<dt>
		<label for="packageID">
			{lang}wcf.project.selectProject{/lang}
		</label>
	</dt>
	<dd>
		<select
			id="packageID"
			name="packageID"
			{if $action == 'edit'}disabled="disabled"{/if}
		>
			{foreach from=$projects item=$project}
				<option
					value="{@$project->getObjectID()}"
					{if $project->getObjectID() == $packageID}selected="selected"{/if}
				>{$project->getName()} ({$project->package})</option>
			{/foreach}
		</select>
		
		<small>{lang}wcf.project.selectProject.description{/lang}</small>
	</dd>
</dl>