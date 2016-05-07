{if $errorType.projectVersion|isset}
	<p class="error">{lang}wcf.project.error.projectVersion.{@$errorType.projectVersion}{/lang}</p>
{else}
	{if $errorType.duplicate|isset}
		<p class="error">{lang}wcf.project.error.duplicate{/lang}</p>
	{else}
		{include file='formError'}
	{/if}
{/if}