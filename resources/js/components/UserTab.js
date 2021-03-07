import React, { useState, useEffect } from 'react';
import Loader from './Loader';

import Subscription from './Subscription';

/**
 * Handles the User Subscriotuibs tab
 */
const UserTab = () => {
    /**
     * User Status, status variable that indicates if the user is authenitcated
     * and the url to get authenticated
     */
    const [status, setStatus] = useState({});

    /**
     * Loading flag
     */
    const [loading, setLoading] = useState(false);

    /**
     * Array of channels that the user is subscribed to
     */
    const [subscriptions, setSubscriptions] = useState([]);

    /**
     * Get the authenticated/not-authenticated status from the server
     */
    useEffect(() => {
        fetchStatus();
    }, []);

    /**
     * Once authenticated, get the users' subscriptions
     */
    useEffect(() => {
        if (status && status.authenticated) {
            setLoading(true);
            window.axios.get('/subscriptions')
                .then(response => {
                    if (response.data && response.data.items) {
                        setSubscriptions(response.data.items);
                    }
                    setLoading(false);
                })
                .catch(error => {
                    setStatus({ error: 'Server unreachable' });
                    setLoading(false);
                });
        }
    }, [status]);

    /**
     * Fetches the status (authenticated or not...)
     */
    const fetchStatus = () => {
        setLoading(true);
        window.axios.get('/status')
            .then(response => {
                setStatus(response.data);
                setLoading(false);
            })
            .catch(error => {
                setStatus({ error: 'Server unreachable' });
                setLoading(false);
            })
    };

    /**
     * Logs the user out
     * @param e click event 
     */
    const logout = e => {
        setSubscriptions([]);
        window.axios.delete('/oauth')
            .then(response => {
                fetchStatus();
            });
    }

    const {
        authenticated = false,
        error = null,
        oauthAvailable = true,
        oauthUrl = null
    } = status;

    return (
        <div>
            <div style={{ background: 'silver', padding: '1em' }}>
                <h2>View Your Youtube Subscriptions</h2>
                {!authenticated && (
                    <div>
                        <p>To use this tool, you must first be autheticated with your Google account.</p>

                        { oauthAvailable && (!!oauthUrl) && (
                            <p style={{ background: '#ddd', padding: '0.5em', borderRadius: '0.5em' }}>
                                <a href={oauthUrl}>Click Here to Authenticate</a>
                            </p>
                        )}
                        {!oauthAvailable && (
                            <p>Howerver Google OAuth is not configured on the server, so this section cannot be used currently.</p>
                        )}
                    </div>
                )}
                {authenticated && (
                    <div><button onClick={logout}>Log out</button></div>
                )}
            </div>
            {(!!error) && (
                <div>
                    <h4>Error:</h4>
                    {error}
                </div>
            )}
            {loading && <Loader />}
            {!loading && authenticated && (
                <div>
                    {(!subscriptions.length) && (
                        <div>You do not have any channel subscriptions</div>
                    )}
                    {(!!subscriptions.length) && (
                        <div>
                            <div>Your have the following subscriptions</div>
                            {subscriptions.map(item => (
                                <Subscription key={item.id} {...item} />
                            ))}
                        </div>
                    )}
                </div>
            )}
        </div>
    )
}

export default UserTab
