<div id="wordtour-content">
	<div class="wt-list wt-artists">
		<table cellspacing='0'>
			<thead>
			<tr>
				<th></th>
				<th>Name</th>
				<th>Upcoming Shows</th>
				<th>Record Label</th>
				<th>Genre</th>
			</tr>
			</thead>
			{if !$data} 
			<tr>
				<td colspan="5">No Artists Listed</td>
			</tr>
			{else}
			{loop $data}
			<tr>
				<td><a href="{$url}"><div class='thumbnail-item'>{if $poster.url!=""}{$poster.imgTag}{/if}</div></a></td>
				<td width="60%"><a href="{$url}">{$name}</a></td>
				<td><div>{$total}</div></td>
				<td><div>{$label}</div></td>
				<td><div>{$genre}</div></td>
			</tr>
			<tr class="wt-no-border">
				<td class='wt-no-border' colspan='5'>{$short_bio}</td>
			</tr>
			{/loop}
			{/if}
		</table>
	</div>
</div>