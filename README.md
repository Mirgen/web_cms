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
- available web modules, which you can use to build your web site
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

Sub modules/features you can use
---------------------------------------------------------
- Some features (CMS modules) are build on these but you DONT have to use them at all.

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
