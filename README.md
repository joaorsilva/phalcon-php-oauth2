# phalcon-php-oauth2
OAuth2 functionality for Phalcon PHP Framework.
This project aims to provide a full Oauth2 implementation of client and server into PHP Phalcon Framework.
In this repository you will be able to find all Phalcon classes and their respective implementations in full Phalcon applications.

** NOTE: This project is still in developemnt. **

## Environment ##
For this project I'm using the following stack:
* Ubuntu 16.04 LTS
* NGINX server
* PHP 7.0 fpm
* Phalcon PHP Framework 3.3.2 
* MongoDB 3.6 

## Configration ##
This configuration is just for local development environments. Production environments configuration will differ a bit.

For this configuration we will assume the following:
* Host IP: 192.168.0.253
* Project URL(s):
  * Server: https://oauth2.local
  * Client: https://oauth2cli.local

### Host names (URLs) ###
For this, just open your hosts file `/etc/hosts` with root permissions as shown bellow.
```bash
user@host: sudo nano /etc/hosts
```
The andd the following lines:
```
127.0.0.1 oauth2.local
127.0.0.1 oauth2cli.local
```
Save and close it.

To test if the addresses are being resolved on, just ping `oauth2.local` and `oauth2cli.local` and check if they ping well.

### Generating SSL certificates (self signed) for NGINX ###
In order to configure the SSL cetificates to use HTTPS protocol under NGINX you have to do the following:
```bash
cd /etc/nginx/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/oauth2.key -out /etc/nginx/ssl/oauth2.crt
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/oauth2cli.key -out /etc/nginx/ssl/oauth2cli.crt
```
This command generates two files, the `.key` file which is the site private key and the `.crt` file that is the public key.
**IMPORTANT:** When the script asks you to insert the FQDN (Fully Qualified Domain Name) you will have to insert `oauth2.local` and `oauth2cli.local` accordingly.

### Configure NGINX virtual hsosts ###
For this configuration we will assume that you have your web folder in `/var/www`.
To configure the server application just create the file `/etc/nginx/sites-available/oauth2` and insert the following code.
```
# This is the default HTTP server configuration.
server {

  #
  # Server address and port
  #

# This server is listen on IPv4 port 80 (http IPv4 default).
  listen 80; 
  
  # This server is listen on IPv6 port 80 (http IPv6 default).
  listen [::]:80;
  
  # This is the server name
  server_name oauth2.local;
  
  # Beacuse we just want to serve in HTTPS we will redirect the request to HTTPS along with its URI.
  return 307 https://oauth2.local$request_uri;
}

# This is the HTTPS server configuration.
server {

  #
  # Server address and port
  #
  
  #This server will listen on 443 port (default for SSL connections).
  listen 443 ssl;
  
  # This is the server name
  server_name oauth2.local;
  
  #
  # Configure the SSL certificate key that we generated before.
  #
  
  # Public key
  ssl_certificate /etc/nginx/ssl/oauth2.local.crt;
  
  # Private key
  ssl_certificate_key /etc/nginx/ssl/auth2.local.key;

  #
  # NGINX paths configuration
  #
  
  # Rewrite for Phalcon framework (recomended)
  rewrite ^(.*)\#(.*)$ $1\?$2 last;
  
  # Web root directory
  root /var/www/phalcon-php-oauth2/oauth2/public;
  
  # Document index
  index index.php;
  
  #
  # NGINX per location configuration 
  #
  
  # Default
  location / {
    try_files $uri $uri/ /index.php?_url=$uri&$args;
  }
  
  # PHP files configuration.
  location ~ \.php$ {
  
    # Call the NGINX PHP module configuration. 
    include snippets/fastcgi-php.conf;
    
    # PHP fast-cgi address and port
    fastcgi_pass unix:/run/php/php7.0-fpm.sock;
    
    # PHP fast-cgi base configuration
    include fastcgi_params;
    fastcgi_split_path_info       ^(.+\.php)(/.+)$;
    fastcgi_param PATH_INFO       $fastcgi_path_info;
    fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }
  
  # Deny access to Apache's .htaccess files (cause we aren't using Apache2).
  location ~ /\.ht {
		  deny all;
	 }
}
```
To speedup the configuration process just replace the `oauth2` word on this document with the `oauth2cli` word and save the file as as `oauth2cli`.





