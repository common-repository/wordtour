<div id="wordtour-content">
	<div class="wt-list wt-venues">
		<table cellspacing='0'>
			<thead>
			<tr>
				<th></th>
				<th>Name</th>
				<th>City</th>
				<th>Country</th>
				<th>Upcoming Shows</th>
			</tr>
			</thead>
			{if !$data} 
			<tr>
				<td colspan="5">No Venues Listed</td>
			</tr>
			{else}
			{loop $data}
			<tr>
				<td><a href="{$url}"><div class='thumbnail-item'>{if $poster.url!=""}{$poster.imgTag}{/if}</div></a></td>
				<td><div><a href="{$url}">{$name}</a></div></td>
				<td>
					<div>{$city}</div></td>
				<td><div>{$country}</div></td>
				<td><div>{$total}</div></td>
			</tr>
			{/loop}
			{/if}
		</table>
	</div>
</div>