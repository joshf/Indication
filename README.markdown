SHTracker Readme
================

SHTracker is a PHP click counter which can also be used as a download counter. You can use it to track the number of times a link has been clicked or the number of times a file has been downloaded. SHTracker can also be used to hide affiliate links.

The script uses SQL databases. It comes with an admin panel where you can view how many times a link has been clicked. You can also easily add, edit, reset or remove links using the admin panel. SHTracker can also display the current click count on any web page.

#### Current Version: 3.0b1 "JoyfulJaguar"

Features:
---------

* Password protect downloads
* Count unique visitors to avoid multiple counts from same user
* Support displaying of ads before user is redirected to download
* Full admin panel, with Jquery support
* Display download counts to users

Installation:
-------------

1. Create a new database using your web hosts control panel (for instructions on how to do this please contact your web host)
2. Download and unzip SHTracker.zip
3. Upload the SHTracker folder to your server via FTP or your hosts control panel
4. Open up http://yoursite.com/SHTracker/install.php in your browser and enter your database/user details
5. Delete the "installer" folder from your server
6. Make config.php unwritable (optional)
7. Login to the admin panel using the username and password you set during the install process
8. Add your links/downloads
9. SHTracker should now be set up

Usage:
------

The script is called like this: /get.php?id=mydownload1

Replace ID with the ID name/number of your URL, for example: http://yoursite.com/SHTracker/get.php?id=mydownload1

So instead of linking to http://yoursite.com/some/directory/mydownload1.zip, link to http://yoursite.com/SHTracker/get.php?id=mydownload1

This will log the count of the download and redirect the user to the file

This script can also be called via $_POST just set the name of the form to id and the value to the id you wish to download

To show the download count for one id only, call http://yoursite.com/SHTracker/show.php?id=mydownload1. To show just the count with no other text add "&plain=true" to the end of the URL. The showing of counts could be done using an include or an iframe (see below)

**Example Codes:**

Normal:

```php
<?php
$_GET["id"] = "download1";
include("SHTracker/show.php");
?>
```

Without formatting:

```php
<?php
$_GET["id"] = "download1";
$_GET["plain"] = "true";
include("SHTracker/show.php");
?>
```

OR:

Normal:

```html
<iframe src="SHTracker/show.php?id=download1"></iframe>
```

Without formatting:

```html
<iframe src="SHTracker/show.php?id=download1&plain=true"></iframe>
```

Administration:
---------------

Open up SHTracker/admin to add new downloads, view statistics, update existing downloads or delete downloads. The admin panel can also be used to password protect downloads or to show you the tracking link for a download.

Removal:
--------

To remove SHTracker, simply delete the SHTracker folder from your server and delete the "Data" table from your database
