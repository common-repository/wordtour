<div id="wordtour-content">
	<div class="wt-list wt-albums">
		<table cellspacing='0'>
			<thead>
			<tr>
				<th></th>
				<th>Title</th>
				<th>Artist</th>
				<th>Release Date</th>
				<th>Tracks</th>
				<th></th>
			</tr>
			</thead>
			{if !$data} 
			<tr>
				<td colspan="5">No Albums Listed</td>
			</tr>
			{else}
			{loop $data}
			<tr>
				<td>{if $poster.url}<a href="{$url}"><div class='thumbnail-item'>{$poster.imgTag}</div></a>{/if}</td>
				<td><a href="{$url}">{$title}</a><br/><small>{$label}</small></td>
				<td>{$artist}</td>
				<td>{$release}</td>
				<td><div>{$total_tracks}</div></td>
				<td>{if $amazon!=''}<div><a href="{$amazon}">Buy</a></div>{/if}</td>
			</tr>
			<tr class="wt-no-border">
				<td class='wt-no-border' colspan='5'>{$short_about}</td>
			</tr>
			{/loop}
			{/if}
		</table>
	</div>
</div>