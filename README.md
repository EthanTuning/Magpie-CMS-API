# Magpie-PHP

This is a PHP fork of the Magpie CMS.

The folders are:

* database - This has the .sql code to import the database locally so the API can work.
* documentation - Will hold the SRS, etc
* public_html
    * api - This holds everything needed to run the API, minus the database initialization.
    * webclient - This contains everything needed to serve the Magpie webclient pages.

The api and webclient folders each have their own SLIM frameworks.

# Development Setup

## Ubuntu LAMP

1. Install the packages for a LAMP stack
    * sudo apt-get install apache2 mysql-server libapache2-mod-php php
2. (Optional) Install phpmyadmin
    * sudo apt-get install phpmyadmin
3. Move the git directory to /var/www/html/
4. You might have to chown the magpie directory

## AMPPS

1. http://www.ampps.com/download

## XAMPP

1. Download and install XAMPP from https://www.apachefriends.org/index.html
2. Navigate to C:\xampp\apache\conf\extra and open httpd-vhosts.conf, replace its contents with:

    <VirtualHost *:80>
        DocumentRoot "your-repository-directory\public_html"
        ServerName home.dev
        <Directory "your-repository-directory\public_html">
            Allow from all
            Require all granted
            AllowOverride All
        </Directory>
    </VirtualHost>
    
3. Navigate to C:\Windows\System32\drivers\etc\hosts and add '127.0.0.1 home.dev' to the bottom.
4. start Apache through the XAMPP Control Panel then in a browser search home.dev (the MagpieHunt homepage should pop up)

