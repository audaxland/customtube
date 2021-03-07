<?php

return [
    /**
     * The google API key is found on google api console
     * see https://developers.google.com/youtube/v3/getting-started for more information
     */
    'apikey' => env('GOOGLE_API_KEY', null),

    /**
     * The client_secret_file setting is the path to the client_secret.json file provided by google for oAuth authentication
     * This can be an absolute path or a path relative to the app root or relative to the storage director 
     * or relative the storage/secret directory
     * see https://developers.google.com/youtube/v3/getting-started for more information
     */
    'client_secret_file' => env('GOOGLE_CLIENT_SECRET_FILE', 'storage/secret/client_secret.json'),

];