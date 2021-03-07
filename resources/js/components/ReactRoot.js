import React, { useState } from 'react';
import ReactDOM from 'react-dom';

import VideoItem from './VideoItem';
import Loader from './Loader';

/**
 * Basic React frontend to search for videos
 * @returns 
 */
function ReactRoot() {
    /**
     * Search text, in the input field
     */
    const [queryString, setQueryString] = useState('');
    const [response, setResponse] = useState({});
    const [loading, setLoading] = useState(false);

    const sendRequest = ({ query, page = 1, itemsPerPage = 10 }) => {
        setLoading(true);
        window.axios.get('/search', { params: { query, page, itemsPerPage } })
            .then(res => {
                setResponse(res.data);
                setLoading(false);
            })
            .catch(error => {
                setLoading(false);
            });
    };

    const {
        searchQuery = '',
        page = 0,
        itemsPerPage = 10,
        totalResults = 0,
        videos = undefined,
    } = response;

    /**
     * boolean: flag to display or not the 'next page' link
     */
    const hasMoreVideos = (page * itemsPerPage) < totalResults;

    return (
        <div>
            <h1>React Frontend</h1>
            <div style={{ background: 'silver', padding: '1em' }}>
                <h2>Search for youtube videos</h2>
                <div>
                    <input value={queryString} onChange={e => setQueryString(e.target.value)} />
                    <button onClick={e => sendRequest({ query: queryString })} disabled={loading}>Go</button>
                </div>
            </div>
            {loading && <Loader />}
            {!loading && (
                <div>
                    <h2>Search Results</h2>
                    {(videos === undefined) && (
                        <div>None yet</div>
                    )}
                    {videos && (!videos.length) && (
                        <div>No video found for your search query</div>
                    )}
                    {videos && (!!videos.length) && (
                        <div>
                            <div>Total results: {totalResults}</div>
                            <div>
                                {videos.map(item => (
                                    <VideoItem key={item['video_id']} item={item} />
                                ))}
                            </div>
                        </div>

                    )}
                    {hasMoreVideos && (
                        <div style={{ padding: '2em', textAlign: 'center' }}>
                            <button
                                onClick={e => sendRequest({ query: searchQuery, page: parseInt(page) + 1, itemsPerPage })}
                            >
                                Next Page
                            </button>
                        </div>
                    )}
                </div>

            )
            }
        </div >
    );
}

export default ReactRoot;

if (document.getElementById('react-root')) {
    ReactDOM.render(<ReactRoot />, document.getElementById('react-root'));
}
