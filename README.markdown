# Twit-ee - Show data from Twitter API in ExpressionEngine templates #

* **Author**: [George Ornbo][]
* **Source Code**: [Github][]

## Compatibility

* ExpressionEngine Version 1.6.x
* PHP 5.x.x
* cURL support

## Installation

This file pi.twitee.php must be placed in the /system/plugins/ folder in your ExpressionEngine installation.
Ensure your /system/cache/ folder is writable. 

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

	limit=5
	
Limits the number of results returned. Default - 10

### Refresh ###

	refresh=5
	
The number of minutes between cache refreshes. Default - 5 minutes.

## Single Variables ##

### For Status methods ###

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


### For Basic user methods ###

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
	
## Examples ##

To be done..	
	
## See also ##

To be done
	
## License ##

Twit-ee is licensed under a [Open Source Initiative - BSD License][] license.

---
This file is written using the MarkDown syntax. It may or may not have been parsed. If you are having trouble reading it try running the contents through http://daringfireball.net/projects/markdown/dingus.

[George Ornbo]: http://shapeshed.com/
[ExpressionEngine]:http://www.expressionengine.com/index.php?affiliate=shapeshed
[Open Source Initiative - BSD License]: http://opensource.org/licenses/bsd-license.php
[Github]: http://github.com/shapeshed/ss.twitee.ee_addon/