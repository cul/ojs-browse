<div class="tab-pane" id="in-press">

	<ul class="papers">
		{foreach from=$inPressArticles item=article}
		<li>
			<div class="content">
				<h3>
					{$article->title}
				</h3>
				<address>
					{foreach from=$article->authors item=author name=authorList}
					<span class="author">{$author->first_name} {if $author->middle_name != ''}{$author->middle_initial}.{/if} {$author->last_name}</span>{if !$smarty.foreach.authorList.last},{/if}
					{/foreach}
				</address>
			</div>
		</li>
		{/foreach}
	</ul>
</div>