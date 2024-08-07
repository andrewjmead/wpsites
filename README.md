# WPSites

WPSites is a CLI that lets you quickly spin up WordPress sites based on templates that you've defined.

As a full-time plugin developer, I found I'd often create a new WordPress site just to configure it with the same tools I always use. I'd symlink my local plugin folder. I'd install `woocommerce`. I'd enable debugging mode. The list goes on.

Now I have a robot do all that for me.

I run `wpsites create`, select the template I want to use, and 6 seconds later I'm looking at the admin dashboard for my new site.

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


