# Two Factor SMS
* **Contributors**: extendwings
* **Donate link**: http://www.extendwings.com/donate/
* **Tags**: authentication, two-factor, security, login, sms, mobile, twilio
* **Requires at least**: 4.3
* **Tested up to**: 4.3
* **Stable tag**: 0.1.1
* **License**: AGPLv3 or later
* **License URI**: http://www.gnu.org/licenses/agpl.txt

*Add SMS support to "Two Factor" feature as a plugin*

## Description

This plugin adds SMS support to [Two Factor](https://wordpress.org/plugins/two-factor/) with Twilio.

### Notice
* **Important**: To use this plugin, check following.
	1. Installed and activated the latest version of [Two Factor](https://wordpress.org/plugins/two-factor/)
	2. Twilio account and Twilio number which supports SMS. For details, please contact Twilio support.

### License
* Copyright (c) 2012-2014 [Daisuke Takahashi(Extend Wings)](http://www.extendwings.com/)
* Portions (c) 2010-2012 Web Online.
* Unless otherwise stated, all files in this repo is licensed under *GNU AFFERO GENERAL PUBLIC LICENSE, Version 3*. See *LICENSE* file.

#### The MIT License
* includes/Twilio/
	* Copyright (c) 2011 Twilio, Inc. and Neuman Vong
	* Licensed under [the MIT License](https://raw.githubusercontent.com/twilio/twilio-php/9b83e2f1c480e3fb4e05a833b325c5afa43520fb/LICENSE)
	* Fetched from [twilio/twilio-php](https://github.com/twilio/twilio-php/)
	* Version: 4.3.0

## Installation

1. Upload the `two-factor-sms` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter Twilio API key and your numbers at profile screen.

## Frequently Asked Questions

### This plugin is broken! Thanks for nothing!
First of all, we supports PHP 5.6+, MySQL 5.5+, WordPress 4.3+. Old software(vulnerable!) is not supported.
If you're in supported environment, please create [pull request](https://github.com/shield-9/two-factor-sms/compare/) or [issue](https://github.com/shield-9/two-factor-sms/issues/new).

## Screenshots

Screenshots will be added soon.

## Changelog

### 0.1.1
* Fixed typo.
* Added i18n support.

### 0.1.0
* Initial Beta Release

## Upgrade Notice

### 0.1.1
* None

### 0.1.0
* None
