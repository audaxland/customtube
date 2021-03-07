import React from 'react'

/**
 * Handles an individual channel item, from the subscription channels
 * @param id string : channelId of the channel
 * @param titme string : title of the channel
 * @param description string : description of the channel
 * @partam imgUrl string : url of the thumbnail of the channel
 */
const Subscription = ({ id, title, description, imgUrl }) => {
    return (
        <div>
            <a href={'https://www.youtube.com/channel/' + id} target='_blank'>
                <h3>{title}</h3>
                <div>
                    <img src={imgUrl} />
                </div>
                <div>{description}</div>
            </a>
        </div>
    )
}

export default Subscription
