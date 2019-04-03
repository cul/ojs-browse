<div>
	<form method="get">
		<select name="section" onchange='this.form.submit()'>
			{foreach from=$sections item=section key=key }
			<option value="{$key}" {if $key == $sectionId}selected{/if}>{$section}</option>
			{/foreach}
		</select> 
		<select name="sort" onchange='this.form.submit()'>
			<option value="newest">{translate key="browse.form.date.order.desc"}</option>
			<option value="oldest" {if strtolower($sort) == 'oldest'}selected{/if}>{translate key="browse.form.date.order.asc"}</option>
		</select>
		<select name="year" onchange='this.form.submit()'>
			{foreach from=$years item=year }
			<option value="{$year}" {if $year == $activeYear}selected{/if}>{$year}</option>
			{/foreach}
		</select> 
	</form>
</div>
<div style="margin-top:17px;">
	{$pageInfo}
	<div id="results" style="margin-top:17px;">
		<ol class="papers browse" start="{$listStart}">
			{include file=$articlesListTemplate}
		</ol>
	</div>

	{$pageInfo}
	{$pageLinks}
</div>
