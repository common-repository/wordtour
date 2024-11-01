<input type="hidden" name="venue-header" value="{$venue} - Directions"></input>
<input type="hidden" name="venue-country" value="{$country_code}"></input>
<input type="hidden" name="venue-address" value="{$google_map}"></input>
<textarea style="display:none;">
	<table>
	{if $directions!=''}
		<tr><th>Directions:</th><td>{$directions}</td></tr>
	{/if}
	{if $address!=''}
		<tr><th>Address:</th><td>{$address}</td></tr>
	{/if}
	{if $city!=''}
		<tr><th>City:</th><td>{$city}</td></tr>
	{/if}
	{if $state!=''}
		<tr><th>State:</th><td>{$state}</td></tr>
	{/if}
	{if $country!=''}
		<tr><th>Country:</th><td>{$country}</td></tr>
		{/if}
	{if $zip!=''}
		<tr><th>Zip:</th><td>{$zip}</td></tr>
	{/if}
	{if $venue_phone!=''}
		<tr><th>Phone:</th><td>{$venue_phone}</td></tr>
	{/if}
	{if $venue_website!=''}
		<tr><th>Website:</th><td><a href="{$venue_website}" target="_blank">{$venue_website}</a></td></tr>
	{/if}
	</table>
</textarea>