# Timetracker

This is a simple timetracker web app. It has users, projects, and tasks information for each timelog started/submitted. It also features teams and a few site admin options, like closed/open registration.

It does not require JavaScript to run in the browser, but it has JavaScript sprinkled on top for browsers with JavaScript enabled.

## Installing

To install:

After cloning the repo, run `composer install` from the project root, assuming you have Composer installed on your computer.

Copy `setup-example.cfg` in the `protected` directory to a file named `setup.cfg` and add the database creds for your SQL instance. It needs just an empty database. Also add site name, site url, and, optionally, timezone and email service features like [Mailgun](https://www.mailgun.com/). The example cfg file has further explanations for each of the variables you can set.

Run `npm install`, from the `public` directory, assuming you have npm installed.

Transpile the sass and js code in `public` by running `npm run prod`. We're currently using [laravel-mix](https://laravel-mix.com/docs/4.0/installation) and [stimulus](https://stimulusjs.org/) for frontend stuff.

Upload the files to your server except any npm modules directories, .gitignore files, etc. The files in `public` need to be served from your web root, or a subdirectory in web root (**subdirectory installation has not been tested at this time**). The `protected` directory should ideally be placed outside of any publically acessible web directories. If you put it in a directory other than one level up from your public directory or rename it, you will need to change the require statement in `public/index.php` to match. (The name "protected," for what it's worth, comes from the fact that we are NearlyFreeSpeech.Net (NFSN) fans, and that is the directory they provide to place files protected from web access.)

When you go to the website in the browser for the first time, it will prompt you to enter your username, password and optional email, which will be used as the "admin" user, who has the ability to open and close registration, as well as add other users.

## Built With

- [Fat Free Framework](https://fatfreeframework.com)
- [Laravel Mix](https://laravel-mix.com/)
- [Stimulus](https://stimulusjs.org/)