{**
 * site.tpl
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Site index.
 *
 * $Id$
 *}

{call_hook name="Templates::Index::journal"}
{strip}
{if $siteTitle}
	{assign var="pageTitleTranslated" value=$siteTitle}
{/if}
{include file="frontend/components/header.tpl"}
{/strip}
{if $intro}{$intro|nl2br}{/if}

{iterate from=journals item=journal}

	{assign var="displayHomePageImage" value=$journal->getLocalizedSetting('homepageImage')}
	{assign var="displayHomePageLogo" value=$journal->getLocalizedPageHeaderLogo(true)}
	{assign var="displayPageHeaderLogo" value=$journal->getLocalizedPageHeaderLogo()}
{** Commented out because this is unnecessary with one journal and no redirect
 * 	<div style="clear:left;">
 *  	{if $displayHomePageImage && is_array($displayHomePageImage)}
 * 		{assign var="altText" value=$journal->getLocalizedSetting('homepageImageAltText')}
 * 		<div class="homepageImage"><a href="{url journal=$journal->getPath()}" class="action"><img src="{$journalFilesPath}{$journal->getId()}/{$displayHomePageImage.uploadName|escape:"url"}" {if $altText != ''}alt="{$altText|escape}"{else}alt="{translate key="common.pageHeaderLogo.altText"}"{/if} /></a></div>
 * 	{elseif $displayHomePageLogo && is_array($displayHomePageLogo)}
 * 		{assign var="altText" value=$journal->getLocalizedSetting('homeHeaderLogoImageAltText')}
 * 		<div class="homepageImage"><a href="{url journal=$journal->getPath()}" class="action"><img src="{$journalFilesPath}{$journal->getId()}/{$displayHomePageLogo.uploadName|escape:"url"}" {if $altText != ''}alt="{$altText|escape}"{else}alt="{translate key="common.pageHeaderLogo.altText"}"{/if} /></a></div>
 * 	{elseif $displayPageHeaderLogo && is_array($displayPageHeaderLogo)}
 * 		{assign var="altText" value=$journal->getLocalizedSetting('pageHeaderLogoImageAltText')}
 * 		<div class="homepageImage"><a href="{url journal=$journal->getPath()}" class="action"><img src="{$journalFilesPath}{$journal->getId()}/{$displayPageHeaderLogo.uploadName|escape:"url"}" {if $altText != ''}alt="{$altText|escape}"{else}alt="{translate key="common.pageHeaderLogo.altText"}"{/if} /></a></div>
 * 	{/if}
 * 	</div>
 * 	<h3>{$journal->getLocalizedTitle()|escape}</h3>
 ** !Commented out because this is unnecessary with one journal and no redirect *}
	{if $journal->getLocalizedDescription()}
		<p>{$journal->getLocalizedDescription()|nl2br}</p>
	{/if}
{** Commented out because this is unnecessary with one journal and no redirect
 * 	<p><a href="{url journal=$journal->getPath()}" class="action">{translate key="site.journalView"}</a> | <a href="{url journal=$journal->getPath() page="issue" op="current"}" class="action">{translate key="site.journalCurrent"}</a> | <a href="{url journal=$journal->getPath() page="user" op="register"}" class="action">{translate key="site.journalRegister"}</a></p>
 ** !Commented out because this is unnecessary with one journal and no redirect *}

{/iterate}


{if $issue}
	{* Display the table of contents or cover page of the current issue. *}
	<br />
	<h3 id="issueTitle">{$issue->getIssueIdentification()|strip_unsafe_html|nl2br}</h3>
	{include file="issue/view.tpl"}
{/if}

{include file="frontend/components/footer.tpl"}
