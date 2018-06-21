# Gitpab

<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>

## Who I am?

I am calculator of time spent in Gitlab by every user in period.
Just mark time spent in Gitlab and build report using me.

## Installation

Create empty database in PostgreSQL.

```bash
git clone git@github.com:zubroide/gitpab.git
cd gitpab
composer install
php artisan key:generate
php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\ServiceProvider" --tag=assets
cp .env.example .env
```

Edit environment variables in `.env`:

- `GITLAB_PRIVATE_TOKEN` - is your private token from Gitlab.
- `GITLAB_RESTRICTIONS_PROJECT_IDS` - not necessary, project ids, that you need to monitor.
You are can find project ids using next command: `php artisan look:projects`.
- APP_URL
- DB_DATABASE
- DB_USERNAME

Run migrations:
```bash
php artisan migrate
```

## Usage

Run next command for import projects, issues and comments from Gitlab:

```bash
php artisan import:all
```

You can run it in schedule (every hour by defaults): `php artisan schedule:run`.

Now you can build report about spent time using command

```bash
php artisan stat:spent-time --start=2018-05-01 --finish=2018-06-01
```

Filter:

 - `start` - start date,
 - `finish` - finish date,
 - `user-id` - by assignee,
 - `project-id` - by project,
 - `issue-id` - by issue,
 - `order` - for example: `issue.iid`, `project.path_with_namespace`
 