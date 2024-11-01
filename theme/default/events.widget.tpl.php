{loop $tpl.group}
	{if $name!=''}
	<ul>
		<li><b>{$name}</b></li>
	{/if}
	
	{if !$subgroup} 
		No events
	{/if}
	{loop $subgroup}
			{if $name!=''}
			<ul>
				<li><b>{$name}</b></li>
			{/if}
			<ul>
				{loop $data}
				<li>
				<b><a href="{$url}">{$artist.name} at the {$venue.name}, {if $venue.country_code == "US" && $venue.state_code!=''}
					{$state_code}, 
				{/if}
				{$venue.country_code}</a></b><br></br>
				{$date}
				</li>
				{/loop}
			</ul>
		{if $name!=''}
		</ul>
		{/if}
	{/loop}
	{if $name!=''}
	</ul>
	{/if}
{/loop}

