import React from 'react'

const VideoItem = ({ item }) => {
    const url = "https://www.youtube.com/watch?v=" + item['video_id'];
    const styles = {
        margin: '1em',
        padding: '0 1em 1em',
        background: '#eee',
        display: 'block',
        borderRadius: '0.3em',
    }
    return (
        <a href={url} target="_blank" style={styles}>
            <h3>{item.title}</h3>
            <img src={item.thumbnails.default.url} />
            <div>{item.description}</div>
        </a>
    )
}

export default VideoItem
