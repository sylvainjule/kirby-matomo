# Kirby Matomo

This plugin helps you generate a tracking code for Matomo, and displays some useful metrics directly within the panel.

![screenshot-dashboard-faded](https://user-images.githubusercontent.com/14079751/47715079-f3226200-dc3e-11e8-80c6-933c9f3183d0.jpg)

<br/>

## Overview

> This plugin is completely free and published under the MIT license. However, if you are using it in a commercial project and want to help me keep up with maintenance, please consider [making a donation of your choice](https://www.paypal.me/sylvainjl).

- [1. Why Matomo?](#1-why-matomo)
- [2. Installation](#2-installation)
- [3. Options](#3-options)
- [4. Template usage](#4-template-usage)
- [5. Panel display: Area](#5-panel-display-area)
- [6. Panel display: Sections](#6-panel-display-sections)
  * [6.1. Basic blueprint example](#61-basic-blueprint-example)
  * [6.2. Options](#62-options)
  * [6.3. Complete blueprint example](#63-complete-blueprint-example)
- [7. Panel page widget](#6-panel-page-widget)
  * [7.1. Basic blueprint example](#71-basic-blueprint-example)
  * [7.2. Options](#72-options)
  * [7.3. Potential pitfalls](#73-potential-pitfalls)
- [8. License](#8-license)
- [9. Credits](#9-credits)


#### TLDR – Just get me started 👀

1. Install Matomo on your server.
2. Download and copy this repository to ```/site/plugins/matomo```
3. Set the following values in `site/config/config.php` :
```php
return array(
    'sylvainjule.matomo.url'        => 'http://your-matomo.url',
    'sylvainjule.matomo.id'         => 'mywebsite',
    'sylvainjule.matomo.token'      => 'token_auth',
);
```
4. Add this code to your footer snippet: `<?php echo snippet('matomo') ?>`
5. Visit the Matomo panel area or copy [this blueprint](#51-basic-blueprint-example) under a dedicated tab / page in the panel.

You're all set.

<br/>

## 1. Why Matomo?

- It's open-source (who doesn't like open-source?)
- It's free (like, really free. You don't pay with your data)
- It's self-hosted (which means more control for you over your data)
- It respects your visitors privacy (IP Anonymization, automated logs deletion, data ownership)
- It now integrates smoothly with Kirby 4 ✌️

<br/>

## 2. Installation

> Kirby 3: up to 1.0.7 Kirby 4: 2.0.0+

Download and copy this repository to ```/site/plugins/matomo```

Alternatively, you can install it with composer: ```composer require sylvainjule/matomo```

<br/>

## 3. Options

Here is an overview of the available options with their default values:

```php
return array(
    'sylvainjule.matomo.url'            => false, #required
    'sylvainjule.matomo.id'             => false, #required
    'sylvainjule.matomo.token'          => false, #required for the panel integration
    'sylvainjule.matomo.active'         => true,
    'sylvainjule.matomo.debug'          => false,
    'sylvainjule.matomo.trackUsers'     => false,
    'sylvainjule.matomo.disableCookies' => false,
    'sylvainjule.matomo.blacklist'      => ['127.0.0.1', '::1'],
    'sylvainjule.matomo.basicAuth'      => null,
    'sylvainjule.matomo.area'           => true
    'sylvainjule.matomo.area.label'     => 'Matomo',
    'sylvainjule.matomo.area.headline'  => null,

);
```

#### 3.1. `url` (required)

Where your matomo install is:

```php
'sylvainjule.matomo.url' => 'https://analytics.yourdomain.com'
```

#### 3.2. `id` (required)

A single Matomo install can host multiple websites. The plugin needs to know the `id` of the one to look for:

```php
'sylvainjule.matomo.id' => 'mywebsite'
```

#### 3.3. `token` (required for the panel integration)

The panel sections will need to make calls to your Matomo API. A `token_auth` is required, you will find it in `Settings > API` in the control panel. Copy-paste the string without the `&token_auth=` prefix.

> Below is an example token. Please note that this token is private and shouldn't be made public. Once added, if you need to publish your code please create something like a duplicated `config.github.php` which will contain the non-sensitive informations, and add your real `config.php` to your `.gitignore`.

```php
'sylvainjule.matomo.token' => 'gQ7TQgg8EFe3h29F9t4aJqC33VQdPfFP4M'
```

#### 3.4. `active`

You can deactivate the snippet by setting this value to `false`.

```php
'sylvainjule.matomo.active' => true
```

#### 3.5. `blacklist`

Localhost is in the blacklist as default. You can change it.

```php
'sylvainjule.matomo.blacklist' => ['127.0.0.1', '::1']
```

#### 3.6. `trackUsers`

The script is only active for not logged in users by default. If you want to change it, set this option to `true`.

```php
'sylvainjule.matomo.trackUsers' => false
```

#### 3.7. `debug`

If you want to always run the script (even on localhost or if you are logged in), set this option to `true`.

```php
'sylvainjule.matomo.debug' => false
```

#### 3.8. `disableCookies`

If you want to use Matomo without any tracking cookies on the user side, set this option to `true`. You can read more about this setting in the [Matomo FAQ](https://matomo.org/faq/general/faq_157/).

```php
'sylvainjule.matomo.disableCookies' => false
```

#### 3.9. `basicAuth`

If your Matomo instance is additionally secured by Basic Authentication, you can configure these credentials in the format `USERNAME:PASSWORD`.

```php
'sylvainjule.matomo.basicAuth' => null
```

#### 3.11. `area`

If you want to hide the Matomo Panel area, set this option to `false`.

```php
'sylvainjule.matomo.area' => true
```

#### 3.11. `area.label`

If you want to change the label for the Matomo Panel area (displayed in the menu and the breacrumb), you can configure it:

```php
'sylvainjule.matomo.area.label' => 'Matomo'
```

#### 3.12. `label`

If you also want to change the headline for the Matomo Panel area, you can set it with the `area.headline` option. If not specified, the label will be used as fallback.

```php
'sylvainjule.matomo.area.headline' => 'Custom area headline'
```


<br/>

## 4. Template usage

You only need to include the snippet in your code somewhere:

```php
<?php snippet('matomo'); ?>
```

<br/>

### 4.1 Opt-out block

The plugin provides a Matomo opt-out block that allows visitors to control their tracking preferences. To use it in your Kirby blocks field:

```yaml
fields:
  blocks:
    type: blocks # or layout
    fieldsets:
      - matomo-opt-out
```

The opt-out block will display a checkbox that allows visitors to opt-out of Matomo tracking. The block includes:
- A description of Matomo tracking
- Instructions for using the opt-out feature
- A toggle button to opt-out/opt-in

You can customize the text through Kirby's language files using these translation keys:
- `matomo.optout.description`
- `matomo.optout.instructions`
- `matomo.optout.optin`
- `matomo.optout.optout`

You can also replace the default block snippet by creating a snippet named `blocks/matomo-opt-out.php` in your site's `snippets` folder or in a plugin.

<br/>

## 5. Panel display: Area

The panel area displays metrics for the whole website. It is available at the `{ site.url }/panel/matomo` route.
Alternatively, you can configure it in a custom tab / blueprint (see below). In such a case, you might want to disable the area completely:

```php
'sylvainjule.matomo.area' => false
```

You can also customize its title with the `area.label` option:

```php
'sylvainjule.matomo.area.label' => 'Analytics'
```

<br/>

## 6. Panel display: Sections

The panel dashboard can be configured in a custom tab / blueprint with sections.

#### 6.1. Basic blueprint example

> Please make sure that you have included your `token_auth` in your config.

Place this snippet in a dedicated tab / blueprint:

```yaml
columns:
  - width: 3/4
    sections:
      main:
        type: matomo-main
  - width: 1/4
    sections:
      sidebar:
        type: matomo-sidebar
```

#### 6.2. Options

##### Hiding components

There are a bunch of options to help you adjust this default panel view.
Both sections have three components :

- **The mainview** (`matomo-main`) includes `chart`, `overview` and `widgets`
- **The sidebar** (`matomo-sidebar`) includes `link`, `realtime` and `summary`

You can chose to hide them like this:

```yaml
columns:
  - width: 3/4
    sections:
      main:
        type: matomo-main
        chart: false
        overview: false
        widgets: false
  - width: 1/4
    sections:
      sidebar:
        type: matomo-sidebar
        link: false
        realtime: false
        summary: false
```

(Ok, there would be nothing left to work with here, but you get the idea).

##### Hiding and sorting widgets

Widgets can be sorted to your taste. You can also choose to display only some of them. Here's a glimpse of the option with all widgets explicitely set, change the order of its list or remove entries to filter widgets.

```yaml
columns:
  - width: 3/4
    sections:
      main:
        type: matomo-main
        widgets:
          - referrerType
          - websites
          - socials
          - devicesType
          - keywords
          - popularPages
```

##### Hiding period ranges

By default, all 4 ranges are displayed (_This year_, _This month_, _Last 7 days_, _Today_).
You can choose to hide some of them by explicitely excluding them from the `periods` list:

```yaml
columns:
  - width: 3/4
    sections:
      main:
        type: matomo-main
        periods:
          - year
          - month
          - week
          - day
```

##### Changing defaults

You have acces to two default options, the `period` and the widgets `limit`.

- `period` is the default active period on first load of the section. It is a string to chose from one of the periods above. Default is `month`.
- `limit` is the number of entries displayed within the widgets. Default is `5`.

```yaml
columns:
  - width: 3/4
    sections:
      main:
        type: matomo-main
        defaults:
          period: month
          limit: 5
```

#### 6.3. Complete blueprint example

Here's a glimpse of how the blueprint might look with all options explicitely set:

```yaml
columns:
  - width: 3/4
    sections:
      main:
        type: matomo-main
        chart: true
        overview: true
        defaults:
          period: month
          limit: 5
        periods:
          - year
          - month
          - week
          - day
        widgets:
          - referrerType
          - websites
          - socials
          - devicesType
          - keywords
          - popularPages
  - width: 1/4
    sections:
      sidebar:
        type: matomo-sidebar
        link: true
        realtime: true
        summary: true
```

<br/>

## 7. Panel page widget

The panel page widget displays metrics for a given page, both in single-language or multi-language websites.

> Any feedback regarding this widget's behaviour is welcome. It might still need to be refined, inputs from a larger variety of live websites would be a great help. Check the [potential pitfalls](#63-potential-pitfalls) below.

![screenshot-page](https://user-images.githubusercontent.com/14079751/47636477-b9772b80-db58-11e8-94d4-1f8fee8ad0aa.jpg)


#### 7.1. Basic blueprint example

> Please make sure that you have included your `token_auth` in your config.

Place this snippet in your page blueprint:

```yaml
columns:
  - width: 1/3
    sections:
      matomo:
        type: matomo-page
```

The section will automatically detect the page uri, and fetch its metrics for the given period.

#### 7.2. Options

##### Period

You can choose the period displayed, which can either be `year`, `month`, `week` or `day`. Default is `month`.

```yaml
matomo:
  type: matomo-page
  period: month
```

##### Multi-language overview

The plugin will automatically detect if multi-language is switched on, and fetch the metrics of the current language.

Optionally, you can also display the metrics of all languages combined with the `overview` option. Default is `false`.

```yaml
matomo:
  type: matomo-page
  overview: false
```

#### 7.3. Potential pitfalls

- Matomo receives public urls, which means that its URIs are fetched **once routes have been applied**. Therefore, the plugin filters Matomo's responses with a uri created from the public url of the page. If you have set up custom routes, to skip subfolders for example, please make sure to overwrite the `url` method for the template with a page model, otherwise the uri won't be correct.
- The metrics shown might not be accurate / complete if you have changed the default language after Matomo started its data collection.


<br/>

## 8. License

MIT

<br/>

## 9. Credits

Kirby 2 had some Piwik integration plugins:

- [kirby-piwik](https://github.com/schnti/kirby-piwik) by [@schnti](https://github.com/schnti)
- [kirby-analytics](https://github.com/l4ci/kirby-analytics-plugin) by [@l4ci](https://github.com/l4ci)
- [kirby-piwik-widget](https://github.com/mauricerenck/getkirby-piwik-widget) by [@mauricerenck](https://github.com/mauricerenck)
- A promising [Piwik Suite](https://forum.getkirby.com/t/planning-comprehensive-piwik-plugin/954) that sadly has never been released, and was an inspiration to get started on a comprehensive and thorough plugin.

The snippet integration has been shamelessly adapted from [@jenstornell](https://github.com/jenstornell/)'s [kirby-ga](https://github.com/jenstornell/kirby-ga/tree/kirby-3). 👏
