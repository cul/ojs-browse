{foreach from=$browsePublishedArticles item=article key=key}
<li>
	<div class="content">
		<h3>
			<a title="{$article->getLocalizedTitle()}" href="/index.php/tremor/article/view/{$article->getId()}">{$article->getLocalizedTitle()}</a>
		</h3>

		<address>
			{foreach from=$article->getAuthors() item=author name=authorList}
			<span class="author">{$author->getFullName()|escape}</span>{if !$smarty.foreach.authorList.last},{/if}
			{/foreach}
		</address>

		<span class="type {$article->sectionTitle | lower}"><a href="/index.php/tremor/browse?section={$article->getSectionId()}">{$article->getSectionTitle()}</a></span> <time datetime="{$article->getDatePublished()|date_format:$dateFormatShort}" pubdate>{$article->getDatePublished()|date_format:$dateFormatLong}</time>
		<ul class="downloads">

			{foreach from=$article->getLocalizedGalleys() item=galley name=galleyList}
			<li>

				{* Link to the download for PDF and to the view for everything else *}

				{if $galley->getGalleyLabel() != 'PDF'}<a href="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)|to_array:$galley->getBestGalleyId($currentJournal)}" class="action file {$galley->getGalleyLabel()|lower}" target="_parent">{$galley->getGalleyLabel()|escape}</a>

				{else}
				<a class="action file {$galley->getGalleyLabel()|lower}" target="_parent" href="{url page="article" op="download" path=$article->getBestArticleId($currentJournal)|to_array:$galley->getBestGalleyId($currentJournal)}">{$galley->getGalleyLabel()}</a>

				{/if}
			</li>
			{/foreach}

		</ul>
	</div>
</li>
{/foreach}
