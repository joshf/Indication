SHTracker Readme
================

SHTracker is a PHP download counter. You can use it to track the number of times a link has been clicked or the number of times a file has been downloaded. SHTracker can also be used to hide affiliate links.

The script uses SQL databases. It comes with an admin panel where you can view how many times a download has been accessed. You can also easily add, edit or remove downloads using the panel. SHTracker can also display the current count for a download on any web page.

#### Current Version: 4.0.1 "PanickyPanda"

Features:
---------

* Password protect downloads
* Can be used to track links
* Count unique visitors to avoid multiple counts from same user
* Supports displaying of ads before user is redirected to download
* Full admin panel
* Display download counts to users
* Themed by Twitter Bootstrap, offers a choice of themes
* Sort and search downloads through the use of DataTables
* Works well on mobile devices due to a responsive layout

Screenshots:
------------

Screenshots of SHTracker can be found [here](http://imgur.com/a/7aQPl).

Downloads:
------------

[v4.0.1](https://github.com/joshf/SHTracker/zipball/4.0.1) (released 05/01/13)

[v4.0](https://github.com/joshf/SHTracker/zipball/4.0) (released 23/12/12)

[v3.4.4](https://github.com/joshf/SHTracker/zipball/3.4.4) (released 17/11/12)

[v3.4.3](https://github.com/joshf/SHTracker/zipball/3.4.3) (released 11/10/12)

[v3.4.2](https://github.com/joshf/SHTracker/zipball/3.4.2) (released 29/09/12)

Installation:
-------------

1. Create a new database using your web hosts control panel (for instructions on how to do this please contact your web host)
2. Download and unzip SHTracker-xxxx.zip
3. Upload the SHTracker folder to your server via FTP or your hosts control panel
4. Open up http://yoursite.com/SHTracker/installer in your browser and enter your database/user details
5. Delete the "installer" folder from your server
6. Login to the admin panel using the username and password you set during the install process
7. Add your downloads
8. SHTracker should now be set up

Usage:
------

The main script is called like this: /get.php?id=mydownload1

Replace ID with the ID name/number of your URL, for example: http://yoursite.com/SHTracker/get.php?id=mydownload1

So instead of linking to http://yoursite.com/some/directory/mydownload1.zip, link to http://yoursite.com/SHTracker/get.php?id=mydownload1

To find this URL select the download and click the "Show Tracking Link" button

This will log the count of the download and redirect users to the file or web page

This script can also be called via $_POST just set the name of the form to ID and the value to the ID you wish to download

To show the download count for one ID only, call http://yoursite.com/SHTracker/display.php?id=mydownload1. To show just the count with no other text add "&plain=true" to the end of the URL. This could be done by linking directly to display.php, using an iframe or by using a PHP include

**Examples:**

With formatting:

```php
<?php
$_GET["id"] = "download1";
include("SHTracker/display.php");
?>
```

Without formatting:

```php
<?php
$_GET["id"] = "download1";
$_GET["plain"] = "true";
include("SHTracker/display.php");
?>
```

OR:

With formatting:

```html
<iframe src="SHTracker/display.php?id=download1"></iframe>
```

Without formatting:

```html
<iframe src="SHTracker/display.php?id=download1&plain=true"></iframe>
```

Administration:
---------------

Open up SHTracker/admin to add new downloads, view statistics, update existing downloads or delete downloads. The admin panel can also be used to password protect downloads or to show you the tracking link for a download.

Removal:
--------

To remove SHTracker, simply delete the SHTracker folder from your server and delete the "Data" table from your database

Contributing:
-------------

Feel free to fork and make any changes you want to SHTracker. If you want them to be added to master then send a pull request.
