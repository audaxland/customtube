<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\YoutubeData\SearchQuery;
use App\YoutubeData\GoogleAuth;
use App\YoutubeData\Exceptions\YoutubeDataException;
use App\YoutubeData\Exceptions\UnautherizedException;


class SearchController extends Controller
{
   
    /**
     * Handles a GET request for a search for youtube videos
     */
    public function searchYoutubeApi() {
        request()->validate([
            'query'         => 'required',
            'page'          => 'integer',
            'itemsPerPage'  => 'integer',
        ]);
        $searchQuery = new SearchQuery();
        return $searchQuery->searchFor(strip_tags(request('query')), request('page', 1), request('itemsPerPage', 10));
    }


    /**
     * Returns an array of status values, to tell the user if they are logged in or not
     * @return array
     */
    public function authStatus() {
        $return = [
            'authenticated'     => false,
            'oauthAvailable'    => false,
            'oauthUrl'          => null,
            'error'             => null,
        ];
        try {
            if ((new GoogleAuth())->isUserAuthenticated()) {
                return array_merge($return, [
                    'authenticated'     => true,
                    'oauthAvailable'    => true,
                ]);
            }

            return array_merge($return, [
                'oauthAvailable'    => true,
                'oauthUrl'          => (new GoogleAuth())->getGoogleAuthUrl(),
            ]);

        } catch (\Exception $e) {
            (new GoogleAuth())->destroyAuthentication();
            $errorMessage = ($e instanceof YoutubeDataException) ? $e->getMessage() : 'Unknown error';
            return array_merge($return, [
                'error' => $errorMessage,
                'e'=>$e->getMessage(),
            ]);
        }
    }

    /**
     * Handles the redirection immedialty after the user has been authenticated by google
     * @redirect to /user
     */
    public function authenticateOauth() 
    {
        request()->validate([
            'code'  => 'required',
        ]);
        (new GoogleAuth())->authenticateUser(request('code'));
        return redirect('/user');
    }

    /**
     * Logout the user
     * @return array
     */
    public function closeAuthentication() 
    {
        (new GoogleAuth())->destroyAuthentication();
        return ['message' => 'session destroyed'];
    }

    /**
     * Returns the list of subscriptions that the authenticated user has
     * @return array
     */
    public function getUsersSubscriptions() {
        try {
            $googleAuth = new GoogleAuth();
            if (!($googleAuth->isUserAuthenticated())) throw new UnautherizedException();

            $youtube = new \Google_Service_YouTube($googleAuth->getGoogleClient());
            $channels = $youtube->subscriptions->listSubscriptions('snippet,contentDetails', array('mine' => true));

            $items = [];

            foreach($channels as $channel) {
                $items[] = [
                    'id'            => $channel->snippet->resourceId->channelId,
                    'title'         => $channel->snippet->title,
                    'description'   => $channel->snippet->description,
                    'imgUrl'        => $channel->snippet->thumbnails->default->url,
                ];
            }

            return [
                'items' => $items
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

}

