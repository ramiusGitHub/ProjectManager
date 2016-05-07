&#x007B;include file="documentHeader"&#x007D;
	<head>
		<title>Hello World! - &#x007B;lang&#x007D;&#x007B;PAGE_TITLE&#x007D;&#x007B;/lang&#x007D;</title>
		&#x007B;include file='headInclude' sandbox=false&#x007D;
	</head>
	<body&#x007B;if $templateName|isset&#x007D; id="tpl&#x007B;$templateName|ucfirst&#x007D;"&#x007B;/if&#x007D;>
		&#x007B;include file='header' sandbox=false&#x007D;
	
		<div id="main">
			Hello World!
		</div>
	
		&#x007B;include file='footer' sandbox=false&#x007D;
	</body>
</html>