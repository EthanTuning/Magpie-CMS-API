# Magpie-PHP

This is a PHP fork of the Magpie CMS.

The folders are:

* database - This has the .sql code to import the database locally so the API can work.
* documentation - Will hold the SRS and developer documentation
* public_html
    * api - This holds everything needed to run the API, minus the database initialization.  Slim framework is included in here.
    * tester - This holds a simple webpage for grabbing a Google token to test the API with.

### Additional Documentation

API User documentation is at:  https://documenter.getpostman.com/view/4418001/RW87rVjU

### Authentication Note

* The API requires an Authentication header of type Bearer in every API request.  The token is grabbed from the user's Firebase login.

# Development Setup

### Ubuntu LAMP

1. Install the packages for a LAMP stack
    * `sudo apt-get install apache2 mysql-server libapache2-mod-php php`
2. (Optional) Install phpmyadmin
    * `sudo apt-get install phpmyadmin`
3. Move the git directory to `/var/www/html/`
4. You might have to `chown` the magpie directory

### AMPPS

1. http://www.ampps.com/download

### XAMPP

1. Download and install XAMPP from https://www.apachefriends.org/index.html
2. Navigate to C:\xampp\apache\conf\extra and open httpd-vhosts.conf, replace its contents with:
	```
    <VirtualHost *:80>
        DocumentRoot "your-repository-directory\public_html"
        ServerName home.dev
        <Directory "your-repository-directory\public_html">
            Allow from all
            Require all granted
            AllowOverride All
        </Directory>
    </VirtualHost>
    ```
3. Navigate to C:\Windows\System32\drivers\etc\hosts and add '127.0.0.1 home.dev' to the bottom.
4. start Apache through the XAMPP Control Panel then in a browser search home.dev (the MagpieHunt homepage should pop up)

# Deployment Notes

### Server setup

1. If using shared hosting (like Bluehost) you can skip to step 5.

2. Follow the Ubuntu LAMP deployment steps first.

3. Modify the apache configuration files appropriately, add the following to either apache2.conf or httpd.conf (linux command: httpd -V):
    * On Ubuntu: /etc/apache2/apache2.conf
		 ```
		<Directory FULL_PATH_TO_MAGPIE_DIRECTORY_GOES_HERE>
				AllowOverride All
				Require all granted
		</Directory>
		```
    * Note: Under Apache 2, you must set UseCanonicalName = On and ServerName. Otherwise, this value reflects the hostname supplied by the client, which can be spoofed. It is not safe to rely on this value in security-dependent contexts. 

4. Enable apache2 module thingy:
    * `sudo a2enmod rewrite && sudo service apache2 restart`

5. In the `public_html/api/v1/deployment_files/` directory, follow the Readme.htaccess file instructions.

6. Ensure you have PHP 7. In bluehost there's a web configuration tool for this. Latest versions of Ubuntu (18.04+) use PHP 7 by default.

### Database setup

1. Refer to the MagpieDB.sql script in the database folder.

### API Configuration

1. Firebase Config
	* In the `public_html/api/v1/src/Creds/` folder, add your Firebase Admin SDK json credential (download it from Firebase Console).
	* Modify the 'src/AuthenticationMiddleware.php' file to reflect this credential.

2. In `public_html/api/v1/src/Creds/config.php`:
	* change `$config['displayErrorDetails']` to 'false'
	* change the password to reflect the actual password for 'magpiehu_api' user

3. In `public_html/api/v1/src/index.php`, change the "base_url" string to reflect the deployment location.

4. Change the permissions of the entire `src/` folder: `chmod -R 700 src/`


