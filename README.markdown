Twit-ee - Show data from Twitter API in ExpressionEngine templates
===========================================================================

**Author**: [George Ornbo][]
**Source Code**: [Github][]

Installation
-----

This file pi.twitee.php must be placed in the /system/plugins/ folder in your ExpressionEngine installation.

Name
------------------

Twit-ee

Synopsis
-------

Show data from Twitter API in ExpressionEngine templates

Description
-------

To be done..

Parameters
-------

Status methods

	{exp:twitee:public_timeline}
	{/exp:twitee:public_timeline}
	
	{exp:twitee:friends_timeline}
	{/exp:twitee:friends_timeline}
	
	{exp:twitee:user_timeline}
	{/exp:twitee:user_timeline}
	
	{exp:twitee:replies}
	{/exp:twitee:replies}
	
Single Variables
-------

The following single variables are available for Status methods

	{created_at}
	{id}
	{text}
	{source}
	{truncated}
	{in_reply_to_status_id}
	{in_reply_to_user_id}
	{favorited}
	{name}
	{screen_name}
	{description}
	{location}
	{profile_image_url}
	{url}
	{protected}
	{followers_count}
	
Examples
-------

To be done..	
	
Compatibility
-------

ExpressionEngine Version 1.6.x

See also
-------

To be done
	
License
-------

Twit-ee is licensed under a [Open Source Initiative - BSD License][] license.


---

This file is written using the MarkDown syntax. It may or may not have been parsed. If you are having trouble reading it try running the contents through http://daringfireball.net/projects/markdown/dingus.

[George Ornbo]: http://shapeshed.com/
[ExpressionEngine]:http://www.expressionengine.com/index.php?affiliate=shapeshed
[Open Source Initiative - BSD License]: http://opensource.org/licenses/bsd-license.php
[Github]: http://github.com/shapeshed/ss.twitee.ee_addon/