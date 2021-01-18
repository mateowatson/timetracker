# Timetracker

This is a timetracker web app. It has users, projects, and tasks information for each timelog started/submitted. It also features teams and a few site admin options, like closed/open registration.

It does not require JavaScript to run in the browser, but it has JavaScript sprinkled on top for browsers with JavaScript enabled.

## System requirements (more or less)

This app has been tested on the following server setup, but there still might be problems depending on specific configurations. Also, your environment might be slightly different and still be OK.

- Apache 2.4
- PHP 7.2 or 7.3
- MariaDB 10.3 or MySQL 5.7

## Installing

There are two general options for installing this on a server.

### Non-developer download

Download the `dist.zip` file from the latest [release](https://github.com/mateowatson/timetracker/releases) (available as of version 1.0.1). Then unzip the files and put them on your server via (s)ftp with something like [FileZilla](https://filezilla-project.org/) or [Transmit](https://www.panic.com/transmit/). Stable commits for production purposes are tagged with semver tags, as well as added in Github under "Releases."

### Developer download

Clone the [repo](https://github.com/mateowatson/timetracker) or download a [release](https://github.com/mateowatson/timetracker/releases), opting for the source code link (usually named after the semver, such as 1.0.0.zip). Run `composer install` from the `protected` directory, assuming you have Composer installed on your computer.

Run `npm install`, from the `public` directory, assuming you have npm installed. Transpile the sass and js code in `public` by running `npm run prod`. We're currently using [laravel-mix](https://laravel-mix.com/docs/4.0/installation) and [stimulus](https://stimulusjs.org/) for frontend functionality.

### Create the setup.cfg file

Copy `setup-example.cfg` in the `protected` directory to a file named `setup.cfg` and add the database creds for your SQL instance. It needs just an empty database. Also add site name, site url, and, optionally, timezone and email service features like [Mailgun](https://www.mailgun.com/). The example cfg file has further explanations for each of the variables you can set.

### Upload to a server

Upload the files to your server. If you downloaded or cloned the source code following the developer method mentioned above, be sure not to upload any npm modules directories, .gitignore files, etc.

The files in `public` need to be served from your web root, or a subdirectory in web root (subdirectory installation has not been tested at this time). This is called `public` or `public_html` on many hosting providers. The `protected` directory should be placed one level above the publicly accessible web directory. If you put it in a directory other than that and/or rename `protected`, you will need to change the require statement in `public/index.php` to match. (The name "protected," for what it's worth, comes from the fact that we are [NearlyFreeSpeech.Net](https://www.nearlyfreespeech.net/) (NFSN) fans, and that is the directory they provide to place files protected from web access.)

When you go to the website in the browser for the first time, it will prompt you to enter your username, password and optional email (if configured in `setup.cfg`), which will be used as the "admin" user, who has the ability to open and close registration, as well as add other users.

If you want to develop on this project or modify the app, you follow a similar process for local development, but instead of uploading to a server, you would serve the site locally, from the `public` directory. As of version 1.1.1, there is a [lando file](https://docs.lando.dev/), which can simplify setting up a local development server.

## Built With

- [Fat Free Framework](https://fatfreeframework.com)
- [Laravel Mix](https://laravel-mix.com/)
- [Stimulus](https://stimulusjs.org/)

## Contributors

- [Matt Watson](https://github.com/mateowatson)
- [Blake Watson](https://github.com/blakewatson)

[Become a contributor!](CONTRIBUTING.md)