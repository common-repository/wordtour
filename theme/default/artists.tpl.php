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
			{capture "index"}{counter start=1 skip=1}{/capture}
			{loop $data}
			{capture "index"}{counter}{/capture}
			<tr class="row-{$.capture.index%2}">
				<td><a href="{$url}"><div class='poster'>{$poster}</div></a></td>
				<td><div><a href="{$url}">{$name}</a></div></td>
				<td><div>{$total}</div></td>
				<td><div>{$label}</div></td>
				<td><div>{$genre}</div></td>
			</tr>
			<tr class="row-{$.capture.index%2}">
				<td colspan="5">{$short_bio}</td>
			</tr>
			{/loop}
			{/if}
		</table>
	</div>
</div>