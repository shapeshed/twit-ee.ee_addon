# Twit-ee - Show data from Twitter API in ExpressionEngine templates #

* **Author(s)**: [George Ornbo][]
* **Source Code**: [Github][]

## Compatibility

* ExpressionEngine Version 1.6.x
* PHP 5.x.x
* cURL support

## License ##

Twit-ee is free for personal and commercial use. 

If you use it commercially use a donation of $10 is suggested. You can send [donations here](http://pledgie.org/campaigns/2898). 

Twit-ee is licensed under a [Open Source Initiative - BSD License][] license. I encourage others to fork the code and enhance it. 

## Installation

* Copy the /modules/twitee folder to your /system/modules/ folder
* Copy the /language/english/lang.twitee.php file to your /system/languages/english folder
* Open the [Module Manager](http://www.expressionengine.com/index.php?affiliate=shapeshed&page=/docs/cp/modules/index.html)
* Install the Twit-ee module
* In the module enter your Twitter username and password 
* Ensure that your /system/cache/ folder is writable
* Twit-ee is MSM compatible so you can have a separate twitter account for each site

## Name

Twit-ee

## Synopsis

Show data from Twitter API in ExpressionEngine templates

## Description

Twit-ee fetches data from the Twitter API and allows you to display it in your ExpressionEngine templates. A variety of tags are available allowing you to show your own tweets, your friends tweets and more.

## Tags

### Status Methods ###


#### Public Timeline ####

Returns the 20 most recent statuses from non-protected users who have set a custom user icon.  Does not require authentication.  Note that the public timeline is cached for 60 seconds so requesting it more often than that is a waste of resources.

	{exp:twitee:public_timeline}
	{/exp:twitee:public_timeline}
	
#### Friends Timeline ####

Returns the 20 most recent statuses posted by the authenticating user and that user's friends. This is the equivalent of /home on the Web.
	
	{exp:twitee:friends_timeline}
	{/exp:twitee:friends_timeline}
	
#### User Timeline ####

Returns the 20 most recent statuses posted from the authenticating user. It's also possible to request another user's timeline via the id parameter below. This is the equivalent of the Web /archive page for your own user, or the profile page for a third party.

	{exp:twitee:user_timeline}
	{/exp:twitee:user_timeline}

#### Replies ####

Returns the 20 most recent @replies (status updates prefixed with @username) for the authenticating user.
	
	{exp:twitee:replies}
	{/exp:twitee:replies}

#### Favorites ####

Returns the 20 most recent favorite statuses for the authenticating user or user specified by the ID parameter in the requested format. 
	
	{exp:twitee:favorites}
	{/exp:twitee:favorites}
	
### Basic User Methods ###

#### Friends ####

Returns the authenticating user's friends, each with current status inline. They are ordered by the order in which they were added as friends.

	{exp:twitee:friends}
	{/exp:twitee:friends}
	
#### Followers ####

Returns the authenticating user's followers, each with current status inline.  They are ordered by the order in which they joined Twitter (this is going to be changed). 

	{exp:twitee:followers}
	{/exp:twitee:followers}
	

## Parameters ##

### Limit ###

	limit="10"
	
Limits the number of results returned. Default - 10

### Refresh ###

	refresh="5"
	
The number of minutes between cache refreshes. Default - 5 minutes.

### Site ID ###

	site_id="1"
	
Allows you to show data from another MSM enabled site. Default - the current site id.

### Site ID ###

	site_id="1"
	
Allows you to show data from another MSM enabled site. Default - the current site id.

### Convert URLs into links ###

	convert_urls="n"
	
Convert urls in the tweet into anchors. Default - "y"

### Convert @usernames into links ###

	convert_usernames="n"
	
Convert @username in the tweet into anchors that point to the users profile. Default - "y"

### Convert #hastags into links ###

	convert_hash_tags="n"
	
Convert #hashtags in the tweet into anchors that point to search.twitter.com. Default - "y"

### Capitalise the first letter of the relative time ###

	ucfirst_relative_time="y"
	
Changes the first letter of the relative time to uppercase. Default - "n"

## Single Variables ##

### For Status methods ###

Public Timeline, Friends Timeline, User Timeline, Replies and Favorites
	
	Status
		{created_at}
		{id}
		{text}
		{source}
		{truncated}
		{in_reply_to_status_id}
		{in_reply_to_user_id}
		{favorited}

		User
			{id}
			{name}
			{screen_name}
			{description}
			{location}
			{profile_image_url}
			{url}
			{protected}
			{followers_count}
			
	{count}
	{total_results}


### For Basic user methods ###

Friends, Followers
	
	User
		{id}
		{name}
		{screen_name}
		{location}
		{description}
		{profile_image_url}
		{url}
		{protected}
		{followers_count}
		{created_at}

		Status
			{id}
			{text}
			{source}
			{truncated}
			{in_reply_to_status_id}
			{in_reply_to_user_id}
			{favorited}
			{in_reply_to_screen_name}
			
	{count}
	{total_results}

## Examples ##

Show the latest 5 tweets from the public timeline with a cache time of five minutes

	{exp:twitee:public_timeline refresh="5" limit="10"}
	{if count == 1}<ul>{/if}
		<li>{text}</li>
	{if count == total_results}</ul>{/if}
	{/exp:twitee:public_timeline}

Show the latest 10 tweets from your favorites with a cache time of 30 minutes

	{exp:twitee:favorites refresh="30" limit="10"}
	{if count == 1}<ul>{/if}
		<li>{text}</li>
	{if count == total_results}</ul>{/if}
	{/exp:twitee:favorites}


## See also ##

* [Twitter REST API Documentation](http://apiwiki.twitter.com/REST+API+Documentation)
* [Github][]
	
---
This file is written using the MarkDown syntax. It may or may not have been parsed. If you are having trouble reading it try running the contents through http://daringfireball.net/projects/markdown/dingus.

[George Ornbo]: http://shapeshed.com/
[ExpressionEngine]:http://www.expressionengine.com/index.php?affiliate=shapeshed
[Open Source Initiative - BSD License]: http://opensource.org/licenses/bsd-license.php
[Github]: http://github.com/shapeshed/ss.twitee.ee_addon/