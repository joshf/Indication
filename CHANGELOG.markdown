Indication Changelog
====================

#### v4.2.2 (released 05/06/13)

* Fix installer bug

#### v4.2.1 (released 31/05/13)

* Fix security flaw with installer

#### v4.2 (released 30/05/13)

* Add more check to stop blank id's being passed
* Add quick edit selector
* Update bootswatch theme versions
* Better database error messages
* Redesigned add/edit download feedback pages
* Add validation to download name and id
* New mechanism for setting a download password
* New no javascript message
* Focus login form on page load
* Use long hand php rather than short hand
* Move some settings around
* Fix bug where user would be redirected to installer when they should not be
* Re-add option to only count unique visitors. This was broken in previous releases
* Get rewritten to use cases and breaks
* Generate unique key using new method
* Update bootstrap 

#### v4.1.2 (released 01/05/13)

* Sleeker login
* Improve update checking so we don't check every time you go to admin page
* Turn autocomplete off for the settings page
* Don't allow a count below zero
* Allow tilde's and dashes in URL
* Remove auto lowercase conversion

#### v4.1.1 (released 09/03/13)
SHTracker is now called Indication!

* Silence built in database error, use custom ones
* Change Datatables sorting
* Fix settings indentation
* Update bootstrap

#### v4.1 (released 14/02/13)

* Add a variety of themes to SHTracker
* Add config files checks to various files
* Update add/edit feedback pages
* Move backend functions to a worker file
* Use icons in download statistics summary
* Use local jqboostrapvalidation
* New way of shwoing download counts
* Updated bootstrap

#### v4.0.1 (released 05/01/13)

* Updated jQuery
* Add URL validation
* Improve password entry mechanism
* Improve Datatables and bootstrap compatibility
* Add validation to settings
* Remove footers
* Minor cosmetic changes to get.php
* Minor fixes

#### v4.0 (released 23/12/12)
Major rewrite using the Twitter Bootstrap framework. Completely redesigned!

* Validation done by jqboostrapvalidation
* Remove need for admin email

#### v3.4.4 (released 17/11/12)

* Use divs rather tha selectors
* Update jQuery
* Update Datatables
* Switch to using javascript alerts rather than div messages
* Fix bug where them would not be remembered

#### v3.4.3 (released 11/10/12)

* Stop admin pages being indexed
* Check that database exists before trying to connect in get
* Add option to list all downloads
* Remove references to sidhosting.co.uk

#### v3.4.2 (released 29/09/12)

* Updated jQuery
* Updated jQueryUI
* Button sets used rather than individual buttons
* Buttons are hidden when a info message is displayed
* Move help message