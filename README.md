# Authorizer

## MAAP Developer Notes

This WordPress plugin is forked from [authorizer](https://wordpress.org/plugins/authorizer/), modified to use a CAS proxy login instead direct authentication. Also included in this project is a custom [MAAP profile view](maapprofile.php) that references a CAS proxy granting ticket. To use this custom view, add it in the `page-templates` folder of a WordPress theme, and then create a new WordPress page that references the template.

The modified authentication code relies on the use of sessions. If the Wordpress site does not have sessions configured, one tested solution is to add the following code to the `/wp-includes/functions.php` file:

```php
if (!session_id()) {
    session_start();
}
```

## Plugin Description

* Original WordPress Plugin: [authorizer](https://wordpress.org/plugins/authorizer/)
* Changelog: [changelog](https://github.com/uhm-coe/authorizer/blob/master/readme.txt)

*Authorizer* is a WordPress plugin that restricts access to specific users, typically students enrolled in a university course. It maintains a list of approved users that you can edit to determine who has access. It also replaces the default WordPress login/authorization system with one relying on an external server, such as Google, CAS, or LDAP. Finally, *Authorizer* lets you limit invalid login attempts to prevent bots from compromising your users' accounts.

*Authorizer* requires the following:

* **CAS server** or **LDAP server** (plugin needs the URL)
* PHP extensions: php5-mcrypt, php5-ldap, php5-curl

*Authorizer* provides the following options:

* **Authentication**: WordPress accounts; Google accounts; CAS accounts; LDAP accounts
* **Login Access**: All authenticated users (all local and all external can log in); Only specific users (all local and approved external users can log in)
* **View Access**: Everyone (open access); Only logged in users
* **Limit Login Attempts**: Progressively increase the amount of time required between invalid login attempts.

## Screenshots

![](assets/screenshot-1.png?raw=true "WordPress Login screen with Google Logins and CAS Logins enabled.")
![](assets/screenshot-2.png?raw=true "Authorizer Dashboard Widget.")
![](assets/screenshot-3.png?raw=true "Authorizer Options: Access Lists.")

[wp]: https://wordpress.org/plugins/authorizer/
[changelog]: https://github.com/uhm-coe/authorizer/blob/master/readme.txt
