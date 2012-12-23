SHTracker Readme
================

SHTracker is a PHP click counter which can also be used as a download counter. You can use it to track the number of times a link has been clicked or the number of times a file has been downloaded. SHTracker can also be used to hide affiliate links.

The script uses SQL databases. It comes with an admin panel where you can view how many times a link has been clicked. You can also easily add, edit or remove links using the included admin panel. SHTracker can also display the current click count on any web page.

#### Current Version: 4.0 "PanickyPanda"

Features:
---------

* Password protect downloads
* Count unique visitors to avoid multiple counts from same user
* Supports displaying of ads before user is redirected to download
* Full admin panel
* Display download counts to users
* Themed by Twitter Bootstrap
* Sort and search download through the use of DataTables
* Works well on mobile due to a responsive layout

Screenshots:
------------

Screenshots of SHTracker can be found [here](http://imgur.com/a/7aQPl).

Downloads:
------------

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
7. Add your links/downloads
8. SHTracker should now be set up

Usage:
------

The main script is called like this: /get.php?id=mydownload1

Replace ID with the ID name/number of your URL, for example: http://yoursite.com/SHTracker/get.php?id=mydownload1

So instead of linking to http://yoursite.com/some/directory/mydownload1.zip, link to http://yoursite.com/SHTracker/get.php?id=mydownload1

This will log the count of the download and redirect users to the file

This script can also be called via $_POST just set the name of the form to id and the value to the id you wish to download

To show the download count for one id only, call http://yoursite.com/SHTracker/show.php?id=mydownload1. To show just the count with no other text add "&plain=true" to the end of the URL. Similarly, to list all downloads add "&list=true" to the end of the URL. The showing of counts could be done using an include or an iframe (see below)

**Example codes to show count to a user:**

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

Contributing:
-------------

Feel free to fork and make any changes you want to SHTracker. If you want them to be added to master then send a pull request.
