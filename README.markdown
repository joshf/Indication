Indication Readme
================

Indication is a PHP link counter. You can use it to track the number of times a link has been clicked or the number of times a file has been linked. Indication can also be used to hide affiliate links.

The script uses a SQL database. It comes with an admin panel where you can view how many times a link has been accessed. You can also easily add, edit or remove links using the panel. Indication can also display the current count for a link on any web page.

Features:
---------

* Password protect links
* Can be used to track links aswell
* Count unique visitors to avoid multiple counts from same user
* Supports displaying of ads before user is redirected to link
* Full admin panel
* Display link counts to users
* Works well on mobile devices due to a responsive layout
* Ignore counts when admin is logged in
* Beautiful notifications system thanks to Bootstrap Notify

Donations:
------------

If you like Indication and appreciate my hard work a [donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=UYWJXFX6M4ADW) (no matter how small) would be appreciated. I code in my spare time and make no money formally from my scripts.

Screenshots:
------------

Screenshots of Indication can be found [here](http://imgur.com/a/7aQPl).

Releases:
------------

Releases of Indication can be found on the the [releases page](https://github.com/joshf/Indication/releases).

Installation:
-------------

1. Create a new database using your web hosts control panel (for instructions on how to do this please contact your web host)
2. Download and unzip Indication-xxxx.zip
3. Upload the Indication folder to your server via FTP or your hosts control panel
4. Open up http://yoursite.com/Indication/install in your browser and enter your database/user details
5. Delete the "installer" folder from your server
6. Login to the admin panel using the username and password you set during the install process
7. Add your links
8. Indication should now be set up

Usage:
------

The main script is called like this: /get.php?id=mylink1

Replace ID with the ID name/number of your URL, for example: http://yoursite.com/Indication/get.php?id=mylink1

So instead of linking to http://yoursite.com/some/directory/mylink1.zip, link to http://yoursite.com/Indication/get.php?id=mylink1

To find this URL select the link and click the Maginifying glass icon whilst on the admin page

This will log the count of the link and redirect the user to the file or web page

This script can also be called via $_POST just set the name of your form to "id" and the value to the ID you wish to link

To show the link count for an ID, call http://yoursite.com/Indication/display.php?id=mylink1. This could be done by linking directly to display.php, using an iframe or by using a PHP include

**Examples:**

```php
<?php
require_once("Indication/config.php");

$id = "link1";
$count = file_get_contents("" . PATH_TO_SCRIPT . "/display.php?id=$id");
echo $count;
?>
```

```html
<iframe src="Indication/display.php?id=link1" width="80" height="25" frameBorder="0" scrolling="no"></iframe>
```

Administration:
---------------

Open up Indication to add new links, view statistics, update existing links or delete links. The admin panel can also be used to password protect links or to show you the tracking link for a link.

Updating:
---------

1. Before performing an update please make sure you backup your database
2. Download your config.php file (in the Indication folder) via FTP or your hosts control panel
3. Delete the Indication folder off your server
4. Download the latest version of Indication from [here](https://github.com/joshf/Indication/releases)
5. Unzip the file
6. Upload the unzipped Indication folder to your server via FTP or your hosts control panel
7. Upload your config.php file into the Indication folder
4. Open up http://yoursite.com/Indication/install/upgrade.php in your browser and the upgrade process will start
9. You should now have the latest version of Indication

N.B: The upgrade will only upgrade from the previous version of Indication (e.g 4.3 to 4.4), it cannot be used to upgrade from a historic version.

Removal:
--------

To remove Indication, simply delete the Indication folder from your server and delete the "Data" table from your database

Contributing:
-------------

Feel free to fork and make any changes you want to Indication. If you want them to be added to master then send a pull request.
