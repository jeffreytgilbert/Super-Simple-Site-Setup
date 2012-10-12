No logins. No MVC. No MySQL. No PEAR/PECL

Requirements: SQLite, FFMPEG (and php module), GD, PHP 5.2+, Apache 

Features:
* Converts videos to H.264
* Takes thumbnail of the video uploaded and caches it as a thumb
* Converts images to thumbnails upon request
* Tracks visitors and the popularity of content
* Has analytics built in
* JQuery is linked up
* Wicked simple to deploy, just give folders permissions and start posting content.
* Facebook comments on content
* XML sitemap for contents
* 304 long life caching of content in the browser via static requests through php and .htaccess
* Doesn't come with header / footer or controller conventions for requiring how you run the page, it's just old school php

If you find this useful as an example or dev on the source at all, please Fork it so I can checkout any improvements you might make. All the posting types were tested and working. The embed type does nothing to prevent you from including whatever content you want other than PHP. You'll need to protect the admin webroot from access unless you want your database and file system potentially accessible to the world (bad idea)