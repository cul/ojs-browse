
{strip}
{assign var="pageTitleTranslated" value=$siteTitle}
{include file="frontend/components/header.tpl"}
{/strip}

<div>{$journalDescription}</div>

{call_hook name="Templates::Index::journal"}

{if $homepageImage}
<br />
<div id="homepageImage"><img src="{$publicFilesDir}/{$homepageImage.uploadName|escape:"url"}" width="{$homepageImage.width|escape}" height="{$homepageImage.height|escape}" {if $homepageImageAltText != ''}alt="{$homepageImageAltText|escape}"{else}alt="{translate key="common.journalHomepageImage.altText"}"{/if} /></div>
{/if}


{if $enableAnnouncementsHomepage}
	{* Display announcements *}
	<div id="announcementsHome">
		<h3>{translate key="announcement.announcementsHome"}</h3>
		{include file="announcement/list.tpl"}	
		<table class="announcementsMore">
			<tr>
				<td><a href="{url page="announcement"}">{translate key="announcement.moreAnnouncements"}</a></td>
			</tr>
		</table>
	</div>
{/if}

{if $issue}
	{* Display the table of contents or cover page of the current issue. *}
	{** It's all a continuous issue '<h3 id="issueTitle">{$issue->getIssueIdentification()|strip_unsafe_html|nl2br}</h3> *}
	{include file="browse/recent.tpl"}

{/if}


{include file="frontend/components/footer.tpl"}

