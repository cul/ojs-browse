{foreach from=$browsePublishedArticles item=article key=key}

<div class="media-list">
	<div class="article-summary media">
		<div class="media-body">

			<h3 class="media-heading">
				<a title="{$article->getLocalizedTitle()}" {if $journal}href="{url journal=$journal->getPath() page="article" op="view" path=$article->getId()}"{else}href="{url page="article" op="view" path=$article->getId()}"{/if}  >{$article->getLocalizedTitle()}</a>
				<div class="article-published-date">
					<time datetime="{$article->getDatePublished()|date_format:$dateFormatShort}" pubdate>{$article->getDatePublished()|date_format:$dateFormatLong}</time> |
					<span class="type {$article->sectionTitle|lower}">
					<a {if $journal}href="{url journal=$journal->getPath() page="browse" path=$article->getSectionId()}"{else}href="{url page="browse" section=$article->getSectionId()}"{/if}
					>{$article->getSectionTitle()}
					</a>
				</span>
				</div>
			</h3>
	
			<div class="meta">
				<div class="authors">
					{foreach from=$article->getAuthors() item=author name=authorList}
					<span class="author">{$author->getFullName()|escape}</span>{if !$smarty.foreach.authorList.last},{/if}
					{/foreach}
				</div>
			</div>
			
			<div class="btn-group" role="group">
	
				{foreach from=$article->getLocalizedGalleys() item=galley name=galleyList}
				
	
					{* Link to the download for PDF and to the view for everything else *}
	
					{if $galley->getGalleyLabel() != 'PDF'}<a href="{url page="article" op="view" path=$article->getBestArticleId($currentJournal)|to_array:$galley->getBestGalleyId($currentJournal)}" class="obj_galley_link file" target="_parent">{$galley->getGalleyLabel()|escape}</a>
	
					{else}
					
					<a class="galley-link btn btn-primary pdf" target="_parent" href="{url page="article" op="download" path=$article->getBestArticleId($currentJournal)|to_array:$galley->getBestGalleyId($currentJournal)}">{$galley->getGalleyLabel()}</a>
	
					{/if}
					
				
				{/foreach}
	
			</div>
		
		</div>
	</div>
</div>

{/foreach}
