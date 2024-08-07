# WPSites

WPSites is a CLI that lets you quickly spin up WordPress sites based on templates that you've defined.

As a full-time plugin developer, I found I'd often create a new WordPress site just to configure it with the same tools I always use. I'd symlink my local plugin folder. I'd install `woocommerce`. I'd enable debugging mode. The list goes on.

Now I have a robot do all that for me.

I run `wpsites create`, select the template I want to use, and 6 seconds later I'm looking at the admin dashboard for my new site.

If that sounds interesting to you, give WPSites a try and let me know what you think.

## What you'll need

WPSites doesn't include a web server or database.

It's designed to work with your existing localhost tools. This might be Herd, MAMP PRO, Local, or something else entirely. Regardless of what you use, make sure you have a way to server up a localhost WordPress site.

## What exactly is it?

WPSites is a wrapper around [WP-CLI](https://wp-cli.org/). WP-CLI is bundled with WPSites, so there's no need to have it installed.

WP-CLI is a powerful tool that allows you to interact with a WordPress sites using a set of commands. These are pretty specific actions such as downloading WordPress core, installing a plugin, or checking a database connection.

WPSites puts these together to do something high-level.

## Installing

You can install WPSites as a global package. 

```
composer global require andrewmead/wpsites
```

This will install the executable into `~/.composer/vendor/bin/`, so make sure that folder is part of your path. You can run `wpsites --version` to check if it's accessible or not.

## Getting started

To get started, run `wpsites create`. You won't have a configuration file yet, but this command will detect that and will set up the default config file at `~/.wpsites`.


