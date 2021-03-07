# CustomTube app

This is s small laravel app that calls the youtube api to perform search requests and caches the results in the database

## Install

### Clone from the repo

```bash
git clone https://github.com/audaxland/customtube.git
cd customtube
```

### Copy .env file

```bash
cp .env.example .env
```
Note: if you have a different setup you may have to update the .env file to fit your needs


### Run composer install and install sail

Run composer install and install sail with docker

```bash
docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    bash -c "composer install"
```

### Run sail

```bash
./vendor/bin/sail up
```

### Run npm install, generate app keys and run migrations

```bash
./vendor/bin/sail npm install \
 && ./vendor/bin/sail npm run dev \
 && ./vendor/bin/sail php artisan key:generate \
 && ./vendor/bin/sail php artisan migrate
```

### Set the GOOGLE_API_KEY value in the .env file

To use the application you need a google api key.

go to: 
<a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">https://developers.google.com/youtube/v3/getting-started</a>

and set up your account and api key following the instructions

Then copy your api key in the .env file

```
GOOGLE_API_KEY=XXXXXXXXXXXXXXX
```

### Set up the client_secret.json file

For OAuth querys you need to set up the OAuth keys, this is done on the google console 
and it provides you with a client_secret.json file that you download

go to: 
<a href="https://developers.google.com/youtube/v3/getting-started" target="_blank">https://developers.google.com/youtube/v3/getting-started</a>


Than file must be placed in a secure place on the server, and you need to indicate the path to that file in the .env file
 using the setting

```
GOOGLE_CLIENT_SECRET_FILE
```

This path can be any of the following:

- an absolute path
- a relative path to the root of the app
- a relative path to the storage directory of the app
- a relative path to the storage/secret directory of the app

## View in browser

<a href="http://localhost/">http://localhost/</a>