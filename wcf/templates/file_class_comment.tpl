/**{if $comment|isset && !$comment|empty} 
 * {@$comment} 
 * {/if}{if !$author|empty || !$authorURL|empty} 
 * @author{if !$author|empty}	{@$author}{/if}{if !$authorURL|empty} &#x003C;{@$authorURL}&#x003E;{/if}{/if}{if !$copyright|empty || !$copyrightURL|empty} 
 * @copyright{if !$copyright|empty}	{@$copyright}{/if}{if !$copyrightURL|empty} &#x003C;{@$copyrightURL}&#x003E;{/if}{/if}{if !$license|empty || !$licenseURL|empty} 
 * @license{if !$license|empty}	{@$license}{/if}{if !$licenseURL|empty} &#x003C;{@$licenseURL}&#x003E;{/if}{/if} 
 * @package {@$packageName} 
 **/