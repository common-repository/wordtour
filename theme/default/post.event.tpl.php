<table>
	<tr>
		<th>Start Date:</td>
		<td>{$date}</td>
	</tr>
	<tr>
		<th>End Date:</td>
		<td>{$end_date}</td>
	</tr>
	<tr>
		<th>Location:</th>
		<td>
			{if $venue_url!=''}
				<a href="{$venue_url}" target="_blank">{$venue}</a>
			{else}
				{$venue}
			{/if}
		</td>
	</tr>
	{if $address!=''}
	<tr>
		<th>Address:</th>
		<td>{$address}</td>
	</tr>
	{/if}
	{if $city!=''}
	<tr>
		<th>City:</th>
		<td>{$city}</td>
	</tr>
	{/if}
	{if $state!=''}
	<tr>
		<th>State:</th>
		<td>{$state}</td>
	</tr>
	{/if}
	{if $country!=''}
	<tr>
		<th>Country:</th>
		<td>{$country}</td>
	</tr>
	{/if}
	{if $zip!=''}
	<tr>
		<th>Zip:</th>
		<td>{$zip}</td>
	</tr>
	{/if}
	{if $venue_phone!=''}
	<tr>
		<th>Phone:</th>
		<td>{$venue_phone}</td>
	</tr>
	{/if}
	{if $admission!=''}
	<tr>
		<th>Admission:</th>
		<td>{$admission}</td>
	</tr>
	{/if}
</table>
