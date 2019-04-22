<div class="tab-pane" id="in-press">

	<ul class="papers">
		{foreach from=$inPressArticles item=article}
		<li>
			<div class="content">
				<h3>
				{$article->getLocalizedTitle()}
				</h3>
				<address>
				{$article->getAuthorString()}
				</address>
			</div>
		</li>
		{/foreach}
	</ul>
</div>
