``` _    ___                  ____  ___           __                
| |  / (_)______  ______ _/ /  |/  /___ ______/ /____  __________
| | / / / ___/ / / / __ `/ / /|_/ / __ `/ ___/ __/ _ \/ ___/ ___/
| |/ / (__  ) /_/ / /_/ / / /  / / /_/ (__  ) /_/  __/ /  (__  ) 
|___/_/____/\__,_/\__,_/_/_/  /_/\__,_/____/\__/\___/_/  /____/  
```                                                              
                                                                 
VisualMasters Base Theme for WordPress
by VisualMasters



# Versioning and Authors
==================================================================
Version: 	1.0.1
Author:		Justin Streuper - VisualMasters

GitHub:		https://github.com/VisualMasters/VisualMasters-WordPress-Boilerplate
Support:	justin@visualmasters.nl



# Planned for future releases:
==================================================================
-	Implementation of a GIT deploy hook to automatically install this
	theme to development, staging and production environements



# Credits:
==================================================================	
Sander Koedood (CowDev) for the basic layout of the installation 
and cooking up the gulpfile to get this baby working correctly



# Description:
==================================================================
This base theme is used to provide a boilerplate for developing
safe and modular WordPress themes. It also uses an alternative
structure in terms of file-locations of the WordPress core. 
These files are placed within the /wp/ folder instead of the 
public_html folder directly. We use this setup to prevent any
standard hacks. 



# Installation:
==================================================================
1. 	Download the package and upload it to the domain-root 
	directory. This means you have to put the files one folder
	up from the public_html folder. We do this because then the 
	.env-file which contains all the security information as 
	database-credentials and salts are located outside the site 
	root. So if a malicious user gets access to the files of the
	website, he (or she) can't access the files directly (unless)
	they know what to look for. 
	
2.	Go to the .env-file in the root and change the database
	credentials. 
	
	Change the WP_Home parameter to the site URL, for example:
	https://www.google.com
	
	It is optional to add the FTP information to your server. 
	The gulpfile uses the command "gulp deploy" to automatically
	deploy all the files located in the base theme folder to the
	FTP location. 
	
	!! Please note that the FTP_PATH is the full path to the 
	   base-theme folder. So for example, the full path is:
	   /home/user/domains/example.com/public_html/content/themes/base/
	
	Last step is to generate the salts by visiting the link 
	in the .env-file. 

3.	Download the latest copy of the WordPress core files. Upload
	all the files in the TAR to the server (or local environment)
	and place these files in the /wp/ folder within the public_html. 
	So the admin would go into /public_html/wp/wp-admin/
	
	The wp-content folder can be deleted, you can (if you like) copy
	the plugin and language folders to /public_html/content/ 

4.	Visit the URL and finalize the install and activate the theme. 

5.	Go to the theme folder in your terminal and run the following 
	commands:
	``` 
	gulp install --save-dev
	gulp build
	gulp
	``` 

6.	You're ready to go 


