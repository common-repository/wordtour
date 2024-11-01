<div id="wordtour-content">
	<div class="wt-list">
		<table cellspacing='0'>
			<thead>
			<tr>
				<th></th>
				<th>Name</th>
				<th>Events</th>
			</tr>
			</thead>
			{if !$data} 
			<tr>
				<td colspan="5">No Tour Listed</td>
			</tr>
			{else}
				{loop $data}
				<tr>
					<td>
						{if $poster.url!=""}
						<div class='thumbnail-item'>
						{$poster.imgTag}
						</div>
						{/if}
					</td>
					<td width="70%">
						<div><a href="{$url}">{$name}</a></div>
					</td>
					<td>{$total}</td>
				</tr>
				<tr class="wt-no-border">
					<td class='wt-no-border' colspan='5'>{$short_description}</td>
				</tr>
				{/loop}
			{/if}
		</table>
	</div>
</div>