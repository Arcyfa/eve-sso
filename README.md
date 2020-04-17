# Intro

This package provides a EVE SSO OAuth 2.0 driver for Laravel/Socialite and assists with some small things like updating User database table and creating routes for Socialite.

## Get application registered
Create your application on https://developers.eveonline.com/. While here note the application ID and application secret. As well as registering the callback URL used for the login process

>https://<your-domain>/login/esi/callback

The URL can be anything as it will be sent back to you by the CCP server. So we must be able to catch it in routes. Its set to `login/esi/callback`.

## Environment settings
To `.env` file add

>ESI_CLIENT_ID={eve-application-id}
>ESI_CLIENT_SECRET={eve-application-secret}
>ESI_CALLBACK_URL="https://<your-domain>/login/esi/callback"

these configuration values will be pulled in by `config(eve-sso)` to configure Socialite.

## Adding the service provider
Add to `config/app.php`

>Arcyfa\EveSso\EveSsoServiceProvider::class,

## Migrate database
When the package is added to Laravel ensure to migrate the database to update the user table. A artisan vendor publish might be needed for the migration to become available.

>php artisan migrate

## Updating the user model
Update `app/User.php` to make the newly implemented columns fillable assignable

>protected $fillable = [
>    'name',
>    'email',
>    'password',
>    'provider',
>    'provider_id',
>    'avatar',
>    'token',
>    'refresh_token',
>    'token_type',
>    'expires_in' ,
>    'expires_on',
>    'character_owner_hash'
>];

I am still looking for a good way to extend the User $fillable variable from this package so that we do not need to change the installation but sadly no joy yet.

## Manual installation of the packages

### Update Composer.json
I am not even sure if this is needed. I just do it for my packages so included it here.

>"extra": {
>    "laravel": {
>        "providers": [
>            "Arcyfa\\EveSso\\EveSsoServiceProvider"
>        ]
>    }
>}
>[...]
>"autoload": {
>    "psr-4": {
>        "Arcyfa\\EveSso\\": "packages/arcyfa/eve-sso/src/"
>    }
>}
