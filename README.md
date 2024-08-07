# WPSites

WPSites is a CLI that lets you quickly spin up WordPress sites based on templates that you've defined.

I built WPSites for myself. I'm a full-time WordPress plugin developer and during development, I found I was creating new WordPress sites via Local/Mamp/Etc and then configuring them the same way.

It was getting a bit old.

Now I can run `wpsites create`, select the template to use, and get access to my configured WordPress site about 8 seconds later.

## It's not a server

WPSites does not include a web server or database server. It's designed to work with your existing localhost tooling. This could be Herd, MAMP PRO, Local, or something else.

## What is it?

WPSites is a wrapper around [WP-CLI](https://wp-cli.org/). WP-CLI is bundeled with WPSites, so there's no need to have it installed.

## Installing

You can install WPSites as a global package. This will install the executable into `~/.composer/vendor/bin/`, so make sure that folder is part of your path.

```
composer global require andrewmead/wpsites
```

```
wpsites --version
```

## Getting started

To get started, run `wpsites create`. You won't have a configuration file yet, but this command will detect that and will setup the default config file at `~/.wpsites`.


