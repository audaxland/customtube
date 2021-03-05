import React, { useState } from 'react';
import ReactDOM from 'react-dom';

function ReactRoot() {
    const [query, setQuery] = useState('');
    const [response, setResponse] = useState({});

    const sendRequest = e => {
        console.log('Sending request for: ' + query);
        window.axios.get('/search', { query })
            .then(response => {
                setResponse(response.data);
                console.log('search response data: ', response.data);
            })
            .catch(error => console.log('search error: ', error));
    };

    return (
        <div>
            <h1>React Frontend</h1>
            <div style={{ background: 'silver', padding: '1em' }}>
                <h2>Search for youtube videos</h2>
                <div>
                    <input value={query} onChange={e => setQuery(e.target.value)} />
                    <button onClick={sendRequest} >Go</button>
                </div>
            </div>
            <div>
                <h2>Search Results</h2>
                <div>None yet</div>
            </div>
        </div>
    );
}

export default ReactRoot;

if (document.getElementById('react-root')) {
    ReactDOM.render(<ReactRoot />, document.getElementById('react-root'));
}
