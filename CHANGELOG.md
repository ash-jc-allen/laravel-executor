# Changelog

**v2.2.2 (released 2020-12-06):**
- Added support for PHP 8. [#18](https://github.com/ash-jc-allen/laravel-executor/pull/18)

**v2.1.1 (released 2020-10-08):**
- Fixed a bug affecting Laravel 7 and newer that prevented the generator command from replacing the ``` handle() ``` method correctly. [#17](https://github.com/ash-jc-allen/laravel-executor/pull/17)

**v2.1.0 (released 2020-10-05):**
- Added a timeOut option for the Artisan and external commands.  [#14](https://github.com/ash-jc-allen/laravel-executor/pull/14)

**v2.0.0 (released 2020-09-16):**
- Added the functionality to run interactive commands. [#9](https://github.com/ash-jc-allen/laravel-executor/pull/9) [#10](https://github.com/ash-jc-allen/laravel-executor/pull/10)
- Added support for Laravel 8 and Guzzle 7. [#12](https://github.com/ash-jc-allen/laravel-executor/pull/12)

**v1.1.0 (released 2020-07-07):**
- Added a new ``` ->ping() ``` method to the Executors that can be used to ping a URL.

**v1.0.1 (released 2020-06-26):**
- Fixed a bug that was preventing any console error messages from being added to the output variable.

**v1.0.0 (released 2020-06-21):**
- Production release.

**v0.3.0:**
- Removed the ``` ->definition() ``` method from the Executors.
- Code refactoring and simplification.
- Added unit tests.

**v0.2.2:**
- Updated documentation.

**v0.2.1:**
- Added the Laravel Executor logo to the desktop notifications and README.

**v0.2.0:**
- Added desktop notifications that can be manually triggered.
- Added desktop notifications that are triggered when the Executor finishes running in console mode.
- Removed Laravel 5.8 support.

**v0.1.0:**
- Pre-release development.