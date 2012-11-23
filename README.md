DEVELOPING FOR GRABPRESS:

The GrabPress plugin consists of a few files, the most important of which is GrabPress.php

The GrabPress PHP file is constructed in two parts. The first part defined the GrabPress static class, with all of the functionality needed to communicate with the Grab Autoposter and Catalog APIs.

When accessing the static methods and properties of the GrabPress class, remember to address them as self::methodName() and self::$propertyName from other static methods in the class, and as GrabPress::methodName() and GrabPress::$propertyName from outside of the class (like the HTML sections)

New functions and shared variables should defined as static members of the GrabPress class to protect the global namespace from collisions.

IMPORTANT: it is a strict design decision that the only value to be stored   in the Wordpress database should be the API key. All other operations should read from and write to the Autoposter API until further notice. No variation from this policy will be allowed without prior review as to the reasoning behind it.

GrabPress already provides a getJSON and postJSON pair of methods for communicating with outside APIs

Application-specific functionality can be built upon these methods. See GrabPress::api_get_feeds() as an example. That method contacts the Autoposter API and retrieves the list of created feeds associated with the value stored in GrabPress::$api_key.

The return value is used later in the HTML section of the PHP file to retrieve the Array from which the Manage Feeds table is built.

After forking the repository, you will need to check out the 'table' branch, where I have begun the process of rendering the feeds associated with the current Wordpress installation to an HTML table below the Create New Feed section of the Autoposter admin page.

The new table is under the heading Manage Feeds, and each row is meant to display the current values associated with each feed created from the Create New Feed panel.

Currently, the Grab category does not appear to be correctly selected upon rendering. This will need to be corrected first.
If any of the values were to be changed by the user, a white 'update feed' button should appear at the end of the given row. Clicking this should update the values in the Autoposter database using the Autoposter API.
There is currently a blue X button available. This is meant to commit a delete action on the feed/row and will subtract the feed associated with the row from the Autoposter database via the Autoposter API. It should be colored red if possible.

After any new features or functionality are added to your fork, use GitHub to send a Pull request, and I will review the additions then pull them into the Grab repository.

Welcome to the team!
We are going to do big things together.