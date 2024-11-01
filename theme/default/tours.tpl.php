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
				{capture "index"}{counter start=1 skip=1}{/capture}
				{loop $data}
				{capture "index"}{counter}{/capture}
				<tr class="row-{$.capture.index%2}">
					<td>
						<div class='poster'>
						{$poster}
						</div>
					</td>
					<td>
						<div><a href="{$url}">{$name}</a></div>
					</td>
					<td>{$total}</td>
				</tr>
				<tr class="row-{$.capture.index%2}">
					<td colspan="5">{$short_description}</td>
				</tr>
				{/loop}
			{/if}
		</table>
	</div>
</div>