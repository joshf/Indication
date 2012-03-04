SHTracker Readme
================

Current Version: 1.9 "CynicalChaffinch"

Installation:
-------------

1. Create a new database using your web hosts control panel (for instructions on how to do this please contact your web host)
2. Download and unzip SHTracker.zip  
3. Upload the SHTracker folder to your server via FTP or your hosts control panel 
4. Open up http://yoursite.com/SHTracker/install.php in your browser and enter your database and user details  
5. Delete install.php from your server
6. Make config.php unwritable (optional)
7. Login to the admin panel using the username and password you set during the install  
8. Add your links/downloads. If you do not enter a count for a download it will start from zero  
9. Done

Usage:
------

The script is called like this: /get.php?id=mydownload1

Replace ID with the ID name/number of your URL, for example: http://yoursite.com/SHTracker/get.php?id=mydownload1

So instead of linking to http://yoursite.com/some/directory/mydownload1.zip, link to http://yoursite.com/SHTracker/get.php?id=mydownload1

This script can also be called via $_POST just set the name of the form to id and the value to the id you wish to download

This will log the count of the download and redirect the user to the file

To show the downloads for one id only, call http://yoursite.com/SHTracker/show.php?id=mydownload1. To show just the count with no other text add "&plain" to the end of the URL. The showing of counts could be done using an include or an iframe (see below)

Example Codes:

```php
<?php
$_GET["id"] = "download1";
include("SHTracker/show.php");
?>
```

OR:

```html
<iframe src="SHTracker/show.php?id=download1"></iframe>
```

Administration:
---------------

Open up SHTracker/admin to add new downloads, view statistics, update existing downloads or delete downloads

Removal:
--------

To remove SHTracker, simply delete the SHTracker folder from your server and delete the "Data" table from your database
