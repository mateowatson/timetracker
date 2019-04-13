# Timetracker

**WARNING: This project is in active development. Do not use.**

This is a simple timetracker meant to run on a LAMP server. It will have users, projects, and tasks information for each timelog started/submitted. I eventually want to add user roles (admin, at least) and possibly some concept of teams.

Another goal is to have it not require JavaScript to run in the browser, but to have some JavaScript sprinkled on top for browsers with JavaScript enabled. Or perhaps the user would need to intentionally enable JavaScript as a user setting. I realize this is a niche feature and that JavaScript is not evil, but it's just something I wanna do...

## Installing

To install this for local development (I'm not sure if it works on anyone else's machine other than mine at the moment, so good luck!):

After cloning the repo, run `composer install` from the project root, assuming you have Composer installed on your computer.

Copy `setup-example.cfg` in the root directory to a file named `setup.cfg` and the database creds for your localhost SQL instance. It needs just an empty database.

Run `npm install`, from the `public` directory, assuming you have npm installed.

Serve the site locally from the `public` directory.

To transpile your sass and js code in `public`, run `npm run watch` or `npm run build`. I'm currently using [laravel-mix 2.1](https://laravel-mix.com/docs/2.1/installation) for frontend stuff.

In order to migrate the database schema into your database, go to `localhost/migration` (or whatever hostname you have set up) in your browser and click the Run Migration button. This also currently adds sample users, which you can see in `src/classes/Migration.php`. This is just for convenience sake for testing, so you don't have to go through the registration flow everytime you install it.

## Apologies in advance

I am new to trying to run a serious (at least kind of serious) open-source project, but I really want to learn the GitHub features (issues, pull requests, project boards, etc.). I'm going to try to use [GitFlow](https://nvie.com/posts/a-successful-git-branching-model/) as best I can. Bear with me if you get involved!

## Built With

- [Fat Free Framework](https://fatfreeframework.com)
- [Laravel Mix](https://laravel-mix.com/)