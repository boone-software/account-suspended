<p align="center">
  <h1>Account Suspension</h1>
</p>

<p align="center">
  A WordPress Plugin
</p>

---

For use on client websites that use payment plans. Make sure to clarify in any contracts that you *will* use software to disable their website in the event of non-payment.

## Features

* **Bypass Search Bots:** To prevent a major impact on SEO, you can determine whether the suspension page can be bypassed by search bots.
* **Login Notice:** Display an alphanumeric notice to site owners when they attempt to login.
* **HTTP Status Code:** Determine what status code to send in the response object.
* **Disabled Admin Panel:** Once activated, only administrators will be able to access the admin panel.
* **Customizable:** Use your own splash screen by adding `account-suspended.php` to your template directory.

## Compatibility

* Tested on WordPress 4.8, but should work just fine on WordPress 4.0 and above.
* Tested on PHP 5.6 and PHP 7.1

## Installing

Simply upload the plugin and *Activate* to get started. The admin menu is located in **Settings**.

## Developer Hooks

### Actions

* `bsas_activate` - (Admin only) Activates account suspension splash
* `bsas_deactivate` - (Admin only) Deactivates account suspension splash

## Recommended Plugins

* [Advanced Access Manager](https://wordpress.org/plugins/advanced-access-manager/) - Allows you to fully manipulate user roles and permissions in WordPress, as well as hide or show functionality depending on each role.

## Credits

This WordPress plugin developed by [Boone Software](https://boone.io) on a code base developed by [Designmodo](http://designmodo.com).
