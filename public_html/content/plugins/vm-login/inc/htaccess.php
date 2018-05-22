<?php
	
	
	
	function vm_put_content( $file, $content ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
		$direct_filesystem = new WP_Filesystem_Direct( new StdClass() );
	
		$chmod = defined( 'FS_CHMOD_FILE' ) ? FS_CHMOD_FILE : 0644;
		return $direct_filesystem->put_contents( $file, $content, $chmod );
	}
	
	function vm_create_htaccess_file( $file, $type=null ) {
		if( $type == null ) {
			$fp = fopen( $file, 'w' );
			fclose($fp);
		} else {
			$wp_content_dir = wp_upload_dir(null);
			$fp = fopen( $file, 'w' );
			fclose($fp);
		}
	}
	
	function vm_login_set_htaccess_content() {
		
		// Include TLD Extract library and create instance
		$tld_extract = new LayerShifter\TLDExtract\Extract();
		
		// Get needed URLs for htaccess file
		$plugin_dir = str_replace( str_replace('/wp', '', ABSPATH) , '', WP_PLUGIN_DIR);
		
		// Parse the domain parts for use in htaccess file
		$wp_url = get_bloginfo('url');
		$wp_url_result = $tld_extract->parse($wp_url);

		// Get the site URL and TLD
		$site_url = explode('.', $wp_url_result->getRegistrableDomain())[0];
		$site_tld = $wp_url_result->getSuffix();
		
		// Set correct Absolute Path without the /wp folder
		$abspath = str_replace('/wp', '', ABSPATH);
		
		// Set the admin, content and includes paths
		$wp_content_file = $abspath . ltrim(rtrim(parse_url(WP_CONTENT_URL, PHP_URL_PATH), '/'), '/') . '/.htaccess';
		$wp_upload_file = $abspath . ltrim(rtrim(parse_url(wp_upload_dir()['baseurl'], PHP_URL_PATH), '/'), '/') . '/.htaccess';
		
		$wp_includes_folder = str_replace( $abspath, '', ABSPATH . ltrim(rtrim(parse_url(WPINC, PHP_URL_PATH), '/'), '/') );
		$wp_includes_file = $abspath . $wp_includes_folder . '/.htaccess';
		
		$wp_admin_folder = '/' . ltrim(rtrim(parse_url(get_admin_url(), PHP_URL_PATH), '/'), '/');
		$wp_admin_file = $abspath . ltrim(rtrim(parse_url(get_admin_url(), PHP_URL_PATH), '/'), '/') . '/.htaccess';
		
		
		// Get .htaccess file
		$file = $abspath . '.htaccess';
		
		// Check if the file exists, otherwise create
		if( !file_exists($file) ) {
			vm_create_htaccess_file($file);
		}
		
		// Check if file is writable
		if ( is_writable( $file ) ) {
			// Get current .htaccess content
			$currcontent = @file_get_contents( $file );
	
			// Remove the VM Login marker
			$currcontent = preg_replace( '/# BEGIN VM LOGIN REWRITER[\s\S]+?# END VM LOGIN REWRITER/', '', $currcontent );
	
			// Remove empty spacings
			$currcontent = str_replace( "\n\n" , "\n" , $currcontent );
			
			// Set begin marker
			$content  = '# BEGIN VM LOGIN REWRITER' . PHP_EOL;
			
			// Set Rewrite rules for login pages
			$content .= '
RewriteRule ^^inloggen/? /' . $plugin_dir . '/vm-login/pages/login.php [QSA,L]
RewriteRule ^^login/? /' . $plugin_dir . '/vm-login/pages/login.php [QSA,L]
RewriteRule ^^wachtwoord-vergeten/? /' . $plugin_dir . '/vm-login/pages/lost-password.php [QSA,L]
RewriteRule ^^recover-password/? /' . $plugin_dir . '/vm-login/pages/lost-password.php [QSA,L]
RewriteRule ^^wachtwoord-reset/? /' . $plugin_dir . '/vm-login/pages/reset-password.php [QSA,L]
RewriteRule ^^password-reset/? /' . $plugin_dir . '/vm-login/pages/reset-password.php [QSA,L]' . PHP_EOL;
			
			// Add extra rules for limiting access to the WP Login and Admin pages
			// Prevent for overflooding the WP Admin by sending requests to the wp-login.php of wp-admin
			// by requiring a HTTP Referer to the demo site. 
			$content .= '
# -- only allow post requests when referer is domain --
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteCond %{REQUEST_METHOD} POST
	RewriteCond %{REQUEST_URI} .*/(wp-comments-post|wp-login)\.php.*
	RewriteCond %{HTTP_REFERER} !.*' . $site_url . '.' . $site_tld . '.* [OR]
	RewriteCond %{HTTP_USER_AGENT} ^$
	RewriteRule (.*) http://%{REMOTE_ADDR}/$1 [R=301,L]
</ifModule>' . PHP_EOL;
			
			
			// Disable access to the admin and includes folder
			$content .= '
# -- Disable access to the includes folder --
<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase /
	RewriteRule ^' . $wp_admin_folder . '/includes/ - [F,L]
	RewriteRule !^' . $wp_includes_folder . '/ - [S=3]
	RewriteRule ^' . $wp_includes_folder . '/[^/]+\.php$ - [F,L]
	RewriteRule ^' . $wp_includes_folder . '/js/tinymce/langs/.+\.php - [F,L]
	RewriteRule ^' . $wp_includes_folder . '/theme-compat/ - [F,L]
		  
	# Block enumerating users
	RewriteCond %{REQUEST_URI} !^' . $wp_admin_folder . ' [NC]
	RewriteCond %{QUERY_STRING} ^author=\d+ [NC,OR]
	RewriteCond %{QUERY_STRING} ^author=\{num 
	RewriteRule ^ - [L,R=403]
</IfModule>' . PHP_EOL;


			// Disable XMLRPC as this is no longer useful
			$content .= '
# Protect the xmlrpc file
<Files xmlrpc.php>
  Order Deny,Allow
  Deny from all
</Files>' . PHP_EOL;
			
			// Disable access to htaccess and config files
			$content .= '
# -- Disable access to the htaccess and config files --
<files wp-config.php>
	order allow,deny
	deny from all
</files>

<files ~ "^.*\.([Hh][Tt][Aa])">
	order allow,deny
	deny from all
	satisfy all
</files>' . PHP_EOL;
			
			// Set end marker
			$content .= '
# END VM LOGIN REWRITER
' . PHP_EOL;
			
			// Write general htaccess file
			vm_put_content($file, $content . $currcontent);
		}
			
		
			
		// Add htaccess files to wp-content directory
		// prevent PHP files to be exectuted from this folder
		// Check if the file exists, otherwise create
		if( !file_exists($wp_content_file) ) {
			vm_create_htaccess_file($wp_content_file);
		}
		
		// Check if file is writable
		if ( is_writable( $wp_content_file ) ) {
			// Get current .htaccess content
			$wp_currcontent_file = @file_get_contents( $wp_content_file );
	
			// Remove the VM Login marker
			$wp_currcontent_file = preg_replace( '/# BEGIN VM SECURITY[\s\S]+?# END VM SECURITY/', '', $wp_currcontent_file );
	
			// Remove empty spacings
			$wp_currcontent_file = str_replace( "\n\n" , "\n" , $wp_currcontent_file );
			
			// Set additional content
			$wp_content_content = '
# BEGIN VM SECURITY

<FilesMatch "\.(?i:php)$">
	<IfModule !mod_authz_core.c>
		Order allow,deny
		Deny from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all denied
	</IfModule>
</FilesMatch>

<Files *.php>
	<IfModule !mod_authz_core.c>
		Allow from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all granted
	</IfModule>
</Files>

# END VM SECURITY';
			
			// Write htaccess file in the content folder
			vm_put_content($wp_content_file, $wp_content_content . $wp_currcontent_file);
		}
		
		
		
		// Add htaccess file to wp-content/uploads directory
		// prevent PHP files to be executed from this folder
		// Check if the file exists, otherwise create it first
		if( !file_exists($wp_upload_file) ) {
			vm_create_htaccess_file($wp_upload_file, 'uploads');
		}
		
		if ( is_writable( $wp_upload_file ) ) {
		
			// Get current .htaccess content
			$wp_currcontent_upload_file = @file_get_contents( $wp_upload_file );
		
			// Remove the VM Login marker
			$wp_currcontent_upload_file = preg_replace( '/# BEGIN VM SECURITY[\s\S]+?# END VM SECURITY/', '', $wp_currcontent_upload_file );
		
			// Remove empty spacings
			$wp_currcontent_upload_file = str_replace( "\n\n" , "\n" , $wp_currcontent_upload_file );
			
			// Set additional content
			$wp_upload_content = '
# BEGIN VM SECURITY

<FilesMatch "\.(?i:php)$">
	<IfModule !mod_authz_core.c>
		Order allow,deny
		Deny from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all denied
	</IfModule>
</FilesMatch>

# END VM SECURITY';
			
			// Write htaccess file in the uploads folder
			vm_put_content($wp_upload_file, $wp_upload_content . $wp_currcontent_upload_file);
		}
		
		
		
		// Add htaccess file to wp-includes directory
		// prevent non core files to be executed
		// Check if the file exists, otherwise create it first
		if( !file_exists($wp_includes_file) ) {
			vm_create_htaccess_file($wp_includes_file);
		}
		
		if ( is_writable( $wp_includes_file ) ) {
			// Get current .htaccess content
			$wp_currcontent_includes_file = @file_get_contents( $wp_upload_file );
	
			// Remove the VM Login marker
			$wp_currcontent_includes_file = preg_replace( '/# BEGIN VM SECURITY[\s\S]+?# END VM SECURITY/', '', $wp_currcontent_includes_file );
	
			// Remove empty spacings
			$wp_currcontent_includes_file = str_replace( "\n\n" , "\n" , $wp_currcontent_includes_file );
			
			// Set additional content
			$wp_include_content = '
# BEGIN VM SECURITY
		
<FilesMatch "\.(?i:php)$">
	<IfModule !mod_authz_core.c>
		Order allow,deny
		Deny from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all denied
	</IfModule>
</FilesMatch>

<Files wp-tinymce.php>
	<IfModule !mod_authz_core.c>
		Allow from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all granted
	</IfModule>
</Files>

<Files ms-files.php>
	<IfModule !mod_authz_core.c>
		Allow from all
	</IfModule>
	<IfModule mod_authz_core.c>
		Require all granted
	</IfModule>
</Files>

# END VM SECURITY';
			
			// Write htaccess file in the uploads folder
			vm_put_content($wp_includes_file, $wp_include_content . $wp_currcontent_includes_file);
		}
		
		
	}
	
	
	
	function vm_reset_htaccess_content() {
		// Get .htaccess file
		$file = ABSPATH . '.htaccess';
		$file = str_replace('/wp', '', $file);
		
		if ( is_writable( $file ) ) {
			// Get current .htaccess content
			$currcontent = @file_get_contents( $file );
	
			// Remove the VM Login marker
			$currcontent = preg_replace( '/# BEGIN VM LOGIN REWRITER[\s\S]+?# END VM LOGIN REWRITER/', '', $currcontent );
			
			vm_put_content($file, $currcontent);
		}
		
		// To do, remove lines from htaccess files in other maps... 
	}
	
	
?>