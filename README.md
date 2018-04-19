# Magpie-PHP

This is a PHP fork of the Magpie CMS.

The folders are:

* database - This has the .sql code to import the database locally so the API can work.
* documentation - Will hold the SRS, etc
* public_html
    * api - This holds everything needed to run the API, minus the database initialization.
    * webclient - This contains everything needed to serve the Magpie webclient pages.

The api and webclient folders each have their own SLIM frameworks.


# API Note

* The API requires an Authentication header of type Bearer in every API request.  The token is grabbed from the user's Firebase login.


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

# Deployment Notes

1. Follow the Ubuntu LAMP deployment steps first.

2. Modify the apache configuration files appropriately, add the following to either apache2.conf or httpd.conf:
    * On Ubuntu: /etc/apache2/apache2.conf
 ```
<Directory FULL_PATH_TO_MAGPIE_DIRECTORY_GOES_HERE>
        AllowOverride All
        Require all granted
</Directory>
```

3. Enable apache2 module thingy:
    * sudo a2enmod rewrite && sudo service apache2 restart

4. API Configuration
    * In the 'creds/' folder, add your Firebase Admin SDK json credential
    * Modify the 'authentication.php' file to reflect this credential

5. Run the .sql script in the database folder.

