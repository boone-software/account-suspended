=== WP Maintenance Mode ===
Contributors: Nathanael McDaniel
Plugin Name: Account Suspended
Plugin URI: https://boone.io
Author: Boone Software
Author URI: https://boone.io
GitHub Plugin URI: https://github.com/boone-software/account-suspended
GitHub Branch: master
Tags: account, suspended, administrator
Requires at least: 3.5
Tested up to: 4.8.1
Stable tag: 1.0.1
License: GPL-2.0+

Adds a splash page that indicates account suspension.

== Description ==

For use on client websites that use payment plans. Make sure to clarify in any contracts that you *will* use software to disable their website in the event of non-payment.

= Features =

* **Bypass Search Bots:** To prevent a major impact on SEO, you can determine whether the suspension page can be bypassed by search bots.
* **Login Notice:** Display an alphanumeric notice to site owners when they attempt to login.
* **HTTP Status Code:** Determine what status code to send in the response object.
* **Disabled Admin Panel:** Once activated, only administrators will be able to access the admin panel.
* **Customizable:** Use your own splash screen by adding `account-suspended.php` to your template directory.

= Compatibility =

* Tested on WordPress 4.8, but should work just fine on WordPress 4.0 and above.
* Tested on PHP 5.6 and PHP 7.1

= Recommended Plugins =

* [Advanced Access Manager](https://wordpress.org/plugins/advanced-access-manager/) - Allows you to fully manipulate user roles and permissions in WordPress, as well as hide or show functionality depending on each role.

= Bugs, technical hints or contribute =

Please give us feedback, contribute and file technical bugs on [GitHub Repo](https://github.com/boone-software/account-suspended).

== Installation ==

1. Unpack the download package
2. Upload all files to the `/wp-content/plugins/` directory, include folders
3. Activate the plugin through the 'Plugins' menu in WordPress

== Credits ==

This WordPress plugin developed by [Boone Software](https://boone.io) on a code base developed by [Designmodo](http://designmodo.com).

== Changelog ==

= 1.0.1 (08/22/2017) =

* Added ability to customize suspended page title and description
* Added plugin updater

= 1.0.0 (08/20/2017) =

* Code released publicly
