LoyaltyManager PRO Module for ExpressionEngine
==============================================

This module is developed specifically for using the API with EE solutions.

An API token is needed for accessing the API.<br>
Documentation for the API can be found at http://loyaltymanager.dk/api/v1/doc/ <br>
Contact helpdesk@loyalitetsbureauet.dk to obtain such a token.

Installation
=

Copy the contents of the file into `system/expressionengine/third_party` <br>
After you install the module from the Module Control Panel, access the LoyaltyManager module and provide the API token.


Templates
=

There are three available methods to use in the templates:

 - add
 - edit
 - remove
 
Each of these has some different parameters to help you use the module in your templates.

**Add: {exp:lmp:add}**

Parameters:
 - groups - set a fixed group id or more seperated by pipe (|) to automatically add a new member to fixed groups. ie `groups="210|211"`
 - dateformat - Allows you to specify what format you want the date set to - format is like [php.net/date](http://php.net/date) - default is Y-m-d
 - birthday_fields - Allows you to define three POST fields you wan't to identify as birthday fields. Format is returned as Y-m-d.
 - return - Set the URL to return to after member has been added.
 - required - If you need any fields to be required. Name and email is always required. Seperator is pipe (|).
 
Variable pairs:
 - groups - A variable pair if you wan't to allow the member to select the group he want's to be attached to with two variables: `{id}` and `{name}` of the group.
 
 
All input fields names should be named as provided in the [documentaion](http://loyaltymanager.dk/api/v1/doc/#add_member)

**Example 1**<br>
The following example allows a member to register and requires the new member to fill in mobile and write his birthday in a format like 28-11-1984 and puts the new member in group ID 210

	{exp:lmp:add groups="210" return="http://website.com/thank_you" dateformat="d-m-Y" required="mobile"}
	<p>
		<label>Name:</label>
		<input type="text" name="name">
	</p>
	
	<p>
		<label>E-mail:</label>
		<input type="text" name="email">
	</p>
	
	<p>
		<label>Mobile:</label>
		<input type="text" name="mobile">
	</p>
	
	<p>
		<label>Birthday:</label>
		<input type="text" name="birthday">
	</p>
	<p>
		<input type="submit">
	</p>
	{/exp:lmp:add}
	
	
**Example 2**<br>
The following example allows a member to register and by using the parameter `birthday_fields` we can create dropdowns for setting the birthday.<br>
Additionally, the user is allowed to select a group to be attached to.<br>
(Example has been combined with the [Loop Plugin](http://www.putyourlightson.net/loop-plugin) by [PutYourlightsOn](http://www.putyourlightson.net/))

	{exp:lmp:add return="http://website.com/thank_you" birthday_fields="birth_year|birth_month|birth_day"}
	<p>
		<label>Name:</label>
		<input type="text" name="name">
	</p>
	
	<p>
		<label>E-mail:</label>
		<input type="text" name="email">
	</p>
		
	<p>
		<label>Birthday:</label>
		Birthday:<br>
		<select name="birth_day">
		{exp:for_loop start="1" end="31" increment="1" pad_zero="2" parse="inward"}
		     <option value="{index}">{index}</option>
		{/exp:for_loop}
		</select>
		
		<select name="birth_month">
		<option value="01">January</option>
		<option value="02">February</option>
		<option value="03">March</option>
		<option value="04">April</option>
		<option value="05">May</option>
		<option value="06">June</option>
		<option value="07">July</option>
		<option value="08">August</option>
		<option value="09">Septemper</option>
		<option value="10">October</option>
		<option value="11">November</option>
		<option value="12">December</option>
		</select>
		
		<select name="birth_year">
		{exp:for_loop start="2012" end="1900" increment="-1"}  
		     <option value="{index}">{index}</option>
		{/exp:for_loop}
		</select>
	</p>
	<p>
		<label>Group:</label>
		<select name="groups[]">
		{groups}
		<option value="{id}">{name}</option>
		{/groups}
		</select>
	</p>	
	<p>
		<input type="submit">
	</p>
	{/exp:lmp:add}
	
**Edit: {exp:lmp:edit}**

Parameters:
 - groups - set a fixed group id or more seperated by pipe (|) to automatically add a new member to fixed groups. ie `groups="210|211"`
 - dateformat - Allows you to specify what format you want the date set to - format is like [php.net/date](http://php.net/date) - default is Y-m-d
 - birthday_fields - Allows you to define three POST fields you wan't to identify as birthday fields. Format is returned as Y-m-d.
 - return - Set the URL to return to after member has been added.
 - required - If you need any fields to be required. Name and email is always required. Seperator is pipe (|).
 
Variable pairs:
 - groups - A variable pair if you wan't to allow the member to select the group he want's to be attached to with two variables: `{id}` and `{name}` of the group. Additionally, variable `{in_group}` which tells whether or not the member is in that group.
 
Variables:
 - name
 - email
 - mobile
 - zipcode
 - sex
 - address
 - city
 - country
 - birthday
 
Conditional variables:
If you use the `birthday_fields` parameter, the three named variables will also be available.

This basically follows the same examples as the `add` examples, with two differences. You have some additional variables to use.
To edit a member, you must provide the member_id as the GET parameter `v`.

**Example**

	{exp:lmp:edit required="mobile" return="http://website.com/thank_you" dateformat="d-m-Y"}
	<p>
		<label>Name</label>
		<input type="text" name="name" value="{name}">
	</p>
	
	<p>
		<label>E-mail:</label>
		<input type="text" name="email" value="{email}"><br>
	</p>
	
	<p>
		<label>Birthday:</label>
		<input type="text" name="birthday" value="{birthday}">
	</p>
	
	<p>
		<label>Mobile:</label>
		<input type="text" name="mobile" value="{mobile}">
	</p>
	
	<p>
		<label>Group:</label>
		<select name="groups[]">
		{groups}
		<option value="{id}"{if in_group} selected="selected"{/if}>{name}</option>
		{/groups}
		</select>
	</p>
	
	<p>
		<input type="submit">
	</p>
	{/exp:lmp:edit}
	
**Remove: {exp:lmp:remove}**

Parameters:
 - return - Set the URL to return to after member has been removed.
 - auto - If you set this to yes, the member will be removed right away without being prompted.
 

Variables:
 - name
 - email
 - mobile
 

To delete/remove a member, you must provide the member_id as the GET parameter `v`.

**Example with autoremove**

	{exp:lmp:remove auto="yes"}
	
	<p>
		Hi {name},<br><br>
		
		You are now unsubscribed!
	</p>

	{/exp:lmp:remove}
	
**Example without autoremove**

	{exp:lmp:remove return="http://website.com/goodbye"}
	
	<p>
		Hi {name},<br><br>
		
		Do you really want to unsubscribe with the email adress {email} ?
		
		<input type="submit" name="confirm" value="Yes">
	</p>

	{/exp:lmp:remove}
	
