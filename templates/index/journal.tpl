{**
   * index.tpl
   *
   * Copyright (c) 2003-2010 John Willinsky
   * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
   *
   * Journal index page. Ploidy version.
   *
   * $Id$
   *}
   {strip}
   {assign var="pageTitleTranslated" value=$siteTitle}
   {include file="frontend/components/header.tpl"}
   {/strip}

   <!--<div>{$journalDescription}</div>-->
   
   
   <div class="infobox">
	   
		<div class="cover">
			<img src="{$baseUrl}/plugins/themes/tremor/assets/cover.png">
		</div>
	   
		<ul>
			<li>
				<img src="{$baseUrl}/plugins/themes/tremor/assets/icon-calendar.png">
				<p>
					<strong>Rapid Turnaround Time</strong><br>
					Avg. time from submission to first decision is <strong>22 days (~3 weeks)</strong>.<br>
					Avg. time from acceptance to publication is <strong>21 days (3 weeks)</strong>.
				</p>
			</li>
			<li>
				<img src="{$baseUrl}/plugins/themes/tremor/assets/icon-articles.png">
				<p>
					<strong>Fewer Page Restrictions</strong><br>
					Full-length articles may be up to 5,000 words. Brief Reports may be up to 2,750 words.
				</p>
			</li>
			<li>
				<img src="{$baseUrl}/plugins/themes/tremor/assets/icon-varied.png">
				<p>
					<strong>Numerous Article Types</strong><br>
					Full-length Articles, Case Reports, Brief Reports, Reviews, Viewpoints, Editorials, Letters, Teaching Images, Video Abstracts, and more.
				</p>
			</li>
			<li>
				<img src="{$baseUrl}/plugins/themes/tremor/assets/icon-oa.png">
				<p>
					<strong>Open Access</strong><br>
					Authors maintain copyright. Greater visibility, increased citations, and higher impact on the field.
				</p>
			</li>
		</ul>
	   
   </div>

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

<div class="tabbable" style="margin-bottom: 18px;">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#recent" data-toggle="tab"><h3>Selected Recent Publications</h3></a></li>
        <li class=""><a href="#in-press" data-toggle="tab"><h3>In Press</h3></a></li>
    </ul>
    <div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
        {include file=$browseRecentTemplate}
        
        {include file=$browseInpressTemplate}
    </div>
</div>
{/if}
<!--{if $additionalHomeContent}
<br />
{$additionalHomeContent}
{/if}-->
{include file="frontend/components/footer.tpl"}

