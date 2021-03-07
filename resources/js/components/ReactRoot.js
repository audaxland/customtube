import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter, Route, Link, Switch } from 'react-router-dom';

import SearchTab from './SearchTab';
import UserTab from './UserTab';

/**
 * Style for the navigation tabs
 */
const linkStyle = {
    padding: '0.5em',
    background: '#ddd',
    border: '1px solid #aaa',
    borderRadius: '0.5em 0.5em 0 0',
}

/**
 * Basic React frontend to search for videos
 */
function ReactRoot() {

    return (
        <div>
            <h1>React Frontend</h1>
            <BrowserRouter>
                <div>
                    <Link to='/search' style={linkStyle}>Search</Link>
                    <Link to='/user' style={linkStyle}>User Subscriptions</Link>
                </div>
                <Switch>
                    <Route path='/user'><UserTab /></Route>
                    <Route><SearchTab /></Route>
                </Switch>
            </BrowserRouter>
        </div >
    );
}

export default ReactRoot;

if (document.getElementById('react-root')) {
    ReactDOM.render(<ReactRoot />, document.getElementById('react-root'));
}
