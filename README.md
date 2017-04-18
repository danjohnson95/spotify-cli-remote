# spotify-cli-remote
A CLI tool for controlling Spotify playback on **any** platform

## Requirements

* [PHP](https://php.net)
* [Spotify Premium](https://www.spotify.com/uk/premium)
* [Composer](https://getcomposer.org)

## Installation

### Composer

    $ composer global require danj/spotify-cli-remote

Oh, and ensure that your composer vendor/bin directory is in your PATH:

	$ export PATH=~/.composer/vendor/bin:$PATH

### Build from source

    $ git clone https://github.com/danjohnson95/spotify-cli-remote

    $ cd spotify-cli-remote

    $ composer install

	$ chmod +x spotify

And now put this directory in your PATH.

## Usage

### Authentication

First, you must authenticate the CLI remote with your Spotify account.

    $ spotify login

You'll need to visit [http://spotify-cli-remote.danjohnson.xyz]() to grant access, and you'll be redirected to a page displaying an authorisation code.

You will need to copy and paste this authorisation code into the CLI when requested.

Once authenticated, you can now control Spotify using your CLI!

### Commands

    spotify play      # Resumes/starts playback
    spotify pause     # Pauses playback
    spotify next      # Skips the current track
    spotify prev      # Plays the previous track

## What's the point?

I built this because I primarily work in a Windows environment with no media hotkeys. Switching over to the Spotify window was time consuming, and since I do pretty much everything else in the terminal, I figured controlling Spotify in the CLI would save me time. Hope it saves you time too.
