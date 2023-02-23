# Load Media From Production for WordPress

A super simple way to load your media files from another server. No need to copy or download gigabytes of media. Use on your local development or to quickly fire up staging area without duplicating thousands of media file.

It was created specifically to work with [Lando](https://docs.lando.dev) and [Pantheon](https://pantheon.io).

### How to use

1. Download your production site and skip the images.
2. Install and activate the plugin.
3. A new Settings page is now in your WP Admin - Load Media From Production.
3. Add the URL of your production site to this page and save. All images are now pulled from this domain.

### Developer Tools

The plugin has two developer focused ways to set the URL:

#### Define a constant to set the URL

You can define a constant, most likely in your wp-config.php, and this will be pulled in to the plugin settings.

`define( 'LOAD_MEDIA_FROM_PRODUCTION_URL', 'https://production-url.com' );`

You could also add this inside a conditional depending on your environment, such as:

```bash
if (WP_DEBUG) {
	define( 'LOAD_MEDIA_FROM_PRODUCTION_URL', 'https://production-url.com' );
}
```

#### Use WP-CLI to interact with the plugin

You may prefer to set the production URL by WP-CLI commands in your local environment.</p>

The following commands are available:

To activate the plugin:

`wp plugin activate load-media-from-production`

To get the current setting if any:

`wp load-media-from-production get`

To set the production URL:

`wp load-media-from-production set https://production-url.com`

To reset/remove the plugin option:

`wp load-media-from-production reset`

### What about other solutions?

This plugin was made for a specific use case and may not work with your local development environment.

If you want another solution, check out [BE Media from Production](https://wordpress.org/plugins/be-media-from-production/) from Bill Erickson. However I have found this does not work with all site installs (like Lando) as it works differently and parses URLs to the production URL.

You could just use a [rewrite rule in your .htaccess file](https://gist.github.com/thetwopct/7fef629cf0206cf9642be9c42b28730a), but some local development environments ignore those files (running on nginx). You also have the issue that you could accidentally commit that htaccess file and mess up your live or development site. So this is not an elegant solution.

### Requirements

The plugin is for WordPress, so requires WordPress to be installed.

The plugin will work with sites using Classic Editor, Gutenberg Block Editor, Elementor and more.

### Change Log

See changelog.txt for changelog.

### Issues

Please open an issue in the [GitHub repo](https://github.com/thetwopct/load-media-from-production/issues). Thanks.
