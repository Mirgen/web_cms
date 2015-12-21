WEB CMS
=========================================================

Classic web Content Management System based on PHP framework Nette (https://nette.org/). Only 4 steps to start using it. System uses its modules which you can use to build a web site. 

Consists of 
- front part - which the visitors of your web site will see
- admin part - for you and your client to manage content of the web site, accesible by link your-web-site/admin

Instalation
---------------------------------------------------------
1. Copy all the files to your web hosting (or your localhost).
2. Create database and write its credentials (database name, server host, user name, user passsword) to file config.neon (or to config.local.neon) in section parameters > database > default. For work with database you can use build in Adminer (https://www.adminer.org/).
3. Now you have to import table.sql to your database. Find table.sql in the root of the project and run its content in your favourite database management system (e.g. PHP My Admin or Adminer).  
4. To be able to log in to administration create your user account. For now, we have only static (in source code) accounts. Change following in config.neon:

        authenticator: Nette\Security\SimpleAuthenticator([
            user: password
            user2: password2
        ])
   
   Replace "user" to your prefered user name and "password". Remove or rename also "user2" and "password2". But you can see how to have more than one user account. For example one for admin and one for your client. 

Modules
---------------------------------------------------------
Available web modules, which you can use to build your web site. Up-to-date list of available modules can be found under /ModulesModule/.

1. Text on your web
2. Contact form
3. Guestbook
4. Image galery 
5. Pictograms
6. Featuring 
7. References
8. Insert code
9. Insert google maps
10. Slideshow - image/text presentation
11. E-mail gatherer - newsleter, gather e-mail of your visitors who wants to have news from you via e-mails
12. Header with logo and menu
13. Menu - very dynamic menu, you can add links to your site, to module or external links
14. Social Footer - footer with links for Facebook, Twitter, Instagram 
15. Simple eshop - you can add productw with images, prize, sale, customers can buy them and both client and you will get email with order

Layouts
---------------------------------------------------------
As you know web sites can have several different layouts. For example layout with header, content and footer. Or layout with header, left menu, content on the right side and footer. 

In this CMS there is confortable way how to create new layout and switch between available layouts. 

On Web settings page in Admin you can choose which layout your website will use. After pressing one button you will see confortable Layout browser where you can choose your prefered layout. 

For now we can set layout globaly for whole web. Next step (TODO) will be possibility to have layout per page (which will be easy to implement).

Layouts - structure
---------------------------------------------------------
This is structure of each layout. 

    app/FrontModule/templates/layouts/NameOfSpecificLayout/languages/cs-CZ.php
                                                                    /en-GB.php
                                                                    /...
    app/FrontModule/templates/layouts/NameOfSpecificLayout/NameOfSpecificLayoutLayout.php
                                                          /segments.php
                                                          /setting.php
                                                          /@layout.latte
    www/layouts/NameOfSpecificLayout/css/...
                                    /js/...
                                    /images/1.jpg
                                           /2.jpg
                                           /...

Where 
- @layout.latte - is the most important, it have the layout with HTML elements like html, body, ... and have segments in it (like footer, header, content)
- segments.php - in this file we can find array of segments this layout have (segments like footer, header, content, ...)
- setting.php - array of informations about author, author email, version, etc.
- NameOfSpecificLayoutLayout.php - this file will have name you will choose, name of your layout (eg. LeftMenuLayout), must follow name of the parent folder! Also must implement ILayout interface. So it is not mandatory to use segments, setting files. You just have to imeplement methods in ILayout.
- /languages/ - in this folder you will have for each langugae you want to support array with informations such as title (name) of layout, subtitle, description.
- www/layouts/ - in this folder you will have all the files like images, CSS, JS. In @layout.latte file you will link to these CSS and JS files.
- www/layouts/NameOfSpecificLayout/images/ - this folder is for preview images of your layout, eg. layout scheme, preview of web with this layout, examples. These files will be seen in layout browser. So make them nice to attract users to your layout. :)

I highly recommend you to look into existing layout like app/FrontModule/templates/layouts/Basic or app/FrontModule/templates/layouts/Blank. Just copy them, rename them and also rename file *Layout.php. Change setting, @layout.latte, settings.php, etc.

If you will have new layout feel free to create pull-request. I will check it and gladly approve it.

3rd party modules/features you can use
---------------------------------------------------------
Some modules mentioned above are build on these, but when you will create your own modules you DONT have to use them at all.

PHP
- Adminer - for your work with database (https://www.adminer.org/)
- Tracy - for debugging, very nice (https://nette.org/)
- Latte - template system (https://nette.org/)
- Tester - for testing your PHP code (https://nette.org/)
 
Javascript
- jQuery  - Javascript framework (http://jquery.com/)
- tinymce - for clever (like Word) text editing (http://www.tinymce.com/)
- Colorbox - images javascript galery based on jQuery (http://www.jacklmoore.com/colorbox/)
- Bootstrap - a lot of nice features from this plugin based on jQuery (http://getbootstrap.com/javascript/) 

CSS
- Bootstrap - one of the most used CSS library. A lot of CSS and other components like icons, ... (http://getbootstrap.com/css)

Fonts
- Bootstrap - font for icons (http://getbootstrap.com/components/)
