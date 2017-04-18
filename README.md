# spotify-cli-remote
A CLI tool for controlling Spotify playback on any platform

## Requirements

* Spotify Premium
* PHP >=5.6
* Composer

## Installation

### Composer

    $ composer global require danj/spotify-cli-remote

### Manual (from source)

    $ git clone https://github.com/danjohnson95/spotify-cli-remote
    $ cd spotify-cli-remote
    $ composer install

And now put this directory in your `$PATH`.

## Usage

### Authentication

First, you must authenticate the CLI remote with your Spotify account.

    $ spotify login

You'll need to visit [http://spotify-cli-remote.danjohnson.xyz/login]() to grant access, and you'll be redirected to a page displaying an authorisation code.

You will need to copy and paste this authorisation code into the CLI when requested.

Once authenticated, you can now control Spotify using your CLI!

### Commands

    spotify play      # Resumes/starts playback
    spotify pause     # Pauses playback
    spotify next      # Skips the current track
    spotify prev      # Plays the previous track
