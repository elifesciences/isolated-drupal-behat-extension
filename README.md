Isolated-Drupal Behat Extension
===============================

[![Build Status](https://travis-ci.org/elifesciences/isolated-drupal-behat-extension.svg?branch=master)](https://travis-ci.org/elifesciences/isolated-drupal-behat-extension)

What does the extension do?
---------------------------

The [Drupal extension for Behat](https://github.com/jhedstrom/drupalextension) allows you to use [Behat](https://github.com/behat/behat) to writes scenarios that describe and test the functionality of your Drupal 7 site.

It's flexible, in that you are able to do things such as test remote sites. Because of this, however, it doesn't run each scenario in isolation, which means that they can be influenced by each other, as well as the existing site. When using Behat to purely test your codebase this is dangerous, as scenarios might pass or fail when in fact the opposite should happen.

This extension solves this problem by running each scenario on a freshly-installed site, which allows you to test your site with confidence.

### Isn't that slow?

Yes. To help counter this, the extension actually copies the first install, then copies that back instead of actually installing the site again. This means that the first scenario might take a little while to run, but you won't notice much of a difference with the others.

### Won't this destroy the site that I'm working on?

No. It (mis)uses [Drupal's multi-site feature](https://www.drupal.org/documentation/install/multi-site), so that your site isn't touched. If your site is available at http://localhost/, for example, it installs a site into `sites/localhost` and tests on that (Drupal will serve it instead of your normal site), then removes it when the suite is finished.

### How does it know what modules to enable?

It assumes that your [install profile](https://www.drupal.org/node/306267) will set up your site. That said, you could add steps in your feature to enable modules etc as needed.

Requirements
------------

* [Behat 3.x](https://github.com/behat/behat)
* [Drupal extension for Behat 3.x](https://github.com/jhedstrom/drupalextension)
* A web server locally running your site.

Enabling the extension
----------------------

1. Add the extension to your dependencies (`composer require elife/isolated-drupal-behat-extension`).

2. Add the extension to your Behat configuration:

    ```yaml
    default:
      extensions:
        eLife\IsolatedDrupalBehatExtension:
          db_url: 'mysql://user:password@localhost/db_name'
    ```

    See below for configuration options.

3. Run Behat as normal.

Configuration
-------------

The extension will use configuration options for the [Drupal extension for Behat](https://github.com/jhedstrom/drupalextension) to know where your site is located, what URL it's served on etc.

Its own options are:

### `db_url`

This is **required**. It is a connection string to a database that the isolated sites can be installed into.

This doesn't have to be the same type as your production setup, so you could use SQLite rather than MySQL. (Though bear in mind that if you're not using Drupal's database abstraction layer at any point you will need to use the same database type.)

#### Examples 

* `mysql://user:password@localhost/db_name`
* `sqlite:/db_name.sqlite`

### `profile`

This is the name of the install profile to use. The default value is `standard`.

### `settings_file`

This is a path to a `settings.php` file to use. This is empty by default.

Note that the following values will be overwritten to ensure isolation:

* `$databases`
* `$drupal_hash_salt`
* `$conf["file_public_path"]`
* `$conf["file_private_path"]`
* `$conf["file_temporary_path"]`

### `clean_up`

By default, the extension will remove the sites it created after the suite has finished. If you're running the suite a few times in quick succession, this means it will be installing a site each time, which can be quite slow. Setting this value to `false` (the default is `true`) which cause it *not* clean up the filesystem, so the second (and subsequent) runs will use the copy of the installed site from the first run. This will be quicker, but it's up to the developer to make sure that no changes have been made to the install process (if required, the master site will need to be manually removed).

Running the extension's tests
-----------------------------

```bash
$ composer install
$ ./vendor/bin/phpunit
$ ./vendor/bin/behat
```

Extending the extension
-----------------------

You can write your own Behat extension and listen for the following events:

* `elife_drupal.installing_site`
* `elife_drupal.mirroring_path`
* `elife_drupal.site_installed`
* `elife_drupal.site_settings`

This will allow you to interact with/extend the extension.
