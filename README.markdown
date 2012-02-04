SHTracker Readme
================

Installation:
-------------

1. Create a new database using your web hosts control panel (for instructions on how to do this please contact your web host)
2. Select your new database in PhpMyAdmin or other MySQL tool
3. Go to the query tab and paste the following in the box:
<pre>CREATE TABLE \`Data\` (
\`name\` VARCHAR(100) NOT NULL ,
\`id\` VARCHAR(25) NOT NULL ,
\`url\` VARCHAR(200) NOT NULL ,
\`count\` INT(10) NOT NULL default '0' ,
PRIMARY KEY (\`id\`)
) ENGINE = MYISAM;</pre> 
4. Download and unzip SHTracker.zip  
5. Upload the SHTracker folder to your server via FTP or your hosts control panel 
6. Open up http://yoursite.com/SHTracker/install.php in your browser and enter your database and user details  
7. Delete install.php from your server
8. Make config.php unwritable (optional)
9. Login to the admin panel using the username and password you set during the install  
10. Add your links/downloads. If you do not enter a count for a download it will start from zero  
11. Done

Usage:
------

The script is called like this: /get.php?id=mydownload1

So instead of linking to http://yoursite.com/some/directory/mydownload1.zip, link to http://yoursite.com/SHTracker/get.php?id=mydownload1

This script can also be called via $_POST just set the name of the form to id and the value to the id you wish to download

This will log the count of the download and redirect the user to the file

To show the downloads for one id only, call http://yoursite.com/SHTracker/show.php?id=mydownload1. This could be done using an include or an iframe (see below)

To show the number of clicks on an external page use either of the following codes:

```php
<?php
$_GET["id"] = "download1";
include('SHTracker/show.php');
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
