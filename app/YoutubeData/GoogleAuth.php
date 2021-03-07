<?php

namespace App\YoutubeData;

use App\YoutubeData\Exceptions\GoogleSecretException;
use App\YoutubeData\Exceptions\FailedAuthenticationException;

class GoogleAuth
{

    /**
     * @var \Google_Client : instance of the google client
     */
    protected $googleClient;

    /**
     * Constructor, instanciates the Google_Client with the applicable credentials
     */
    public function __construct() {
        $this->googleClient = new \Google_Client();
        $this->googleClient->setAuthConfig($this->findClientSecretFilePath());
        if ($this->isUserAuthenticated() ) {
            $this->googleClient->setAccessToken($_SESSION['google_auth']['access_token']);
        }
    }

    /**
     * Resolves the location of the secret_client.json file from the configuration setting
     * That file can be placed anywhere and is likely to be placed in a secure directory
     * the recomended location id in th storage/secret directory
     * @return string : full path to the secret_client.json file
     * @throws GoogleSecretException
     */
    protected function findClientSecretFilePath() 
    {
        $settingValue = config('googleapi.client_secret_file');

        if (empty($settingValue)) throw new GoogleSecretException();
        $possiblePaths = [
            $settingValue,
            base_path(ltrim($settingValue, '/')),
            storage_path(ltrim($settingValue),'/'),
            storage_path('/secret/' . ltrim($settingValue, '/'))
        ];

        foreach($possiblePaths as $possiblePath) {
            if (file_exists($possiblePath)) {
                if (!is_readable($possiblePath)) {
                    throw new GoogleSecretException('The google client secret file is not readable, please check permissions.');
                }
                return $possiblePath;
            }
        }

        throw new GoogleSecretException();
    }


    /**
     * Getter to get the instance of the google client
     * @return \Google_Client
     */
    public function getGoogleClient() 
    {
        return $this->googleClient;
    }

    /**
     * Generates the url of the google service where the user will be sent to authenticate with google
     * @return string
     */
    public function getGoogleAuthUrl()
    {
        $client = $this->getGoogleClient();
        $client->addScope(\Google_Service_YouTube::YOUTUBE_FORCE_SSL);
        $client->addScope(\Google_Service_YouTube::YOUTUBE_READONLY);
        $client->setRedirectUri(request()->getSchemeAndHttpHost() . '/oauth');
        $client->setAccessType('offline');
        return $client->createAuthUrl();
    }


    /**
     * Once the user comes back from beeing authenticated by google, they provide the 'code' to authenticate them with the app
     * This method completes the authentication and stores the access_token in the session
     * @param string $code
     * @return $this
     */
    public function authenticateUser($code) 
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        $google_auth = $this->getGoogleClient()->authenticate($code);
        if (!empty($google_auth['error'])) {
            unset($_SESSION['google_auth']);
            throw new FailedAuthenticationException($google_auth['error']);
        }
        $_SESSION['google_auth'] = $google_auth;
        return $this;
    }

    /**
     * Test if the user has a valid authentication token in the session (is authenticated)
     * @return bool
     */
    public function isUserAuthenticated() 
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['google_auth']['access_token'])) return false;
        return true; 
    }

    /**
     * removes the access token from the session (logs out the user)
     * @return $this
     */
    public function destroyAuthentication() 
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['google_auth'] = null;
        session_destroy();
        return $this;
    }


}
