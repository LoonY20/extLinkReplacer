'use strict';

var allImages;
var allImagesFromDatabase;
var allSavedImages;
let allPosts;

function getImage(url, block = null, disButton = [], variable) {

    disButton.forEach(e => {
        e.setAttribute('disabled', 'disabled');
    })


    fetch(
        url,
        {
            headers: {
                'X-WP-Nonce': wpApiSettings.nonce,
            }
        })
        .then((response) => (response.json()))
        .then(data => {
            if (block) {
                block.innerText += ' ' + data.length;
            }

            disButton.forEach(e => {
                e.removeAttribute('disabled');
            })
            window[variable] = data

        });
}

function getImageFromDatabase(block = null, disButton = []) {

    disButton.forEach(e => {
        e.setAttribute('disabled', 'disabled');
    })
    let url = '/wp-json/ext-link-replacer/v1/getallimagesfromdatabase';

    fetch(url)
        .then((response) => (response.json()))
        .then(data => {
            if (block) {
                block.innerText += ' ' + data.length;
            }

            disButton.forEach(e => {
                e.removeAttribute('disabled');
            })

            allImagesFromDatabase = data
        });
}

function getPostIds(block, disButton = []) {

    disButton.forEach(e => {
        e.setAttribute('disabled', 'disabled');
    })

    fetch(
        '/wp-json/ext-link-replacer/v1/getallposts',
        {
            headers: {
                'X-WP-Nonce': wpApiSettings.nonce,
            }
        })
        .then((response) => (response.json()))
        .then(data => {
            block.innerText += ' ' + data.length;
            disButton.forEach(e => {
                e.removeAttribute('disabled');
            })
            allPosts = data
        })


}

function getResult(block = null, data, url, object = false) {

    let formData = new FormData();

    for (const name in data) {
        formData.append(name, data[name]);
    }

    if (object) {
        formData = data
    }

    return fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce,
        }
    },)
        .then((response) => (response.json()))
        .then(data => {
            if (block) {
                let td;
                let tableRow = document.createElement('tr');
                tableRow.classList.add(`post-${data.postId}`);
                tableRow.id = `post-${data.postId}`;
                for (const dataKey in data) {
                    td = document.createElement('td');
                    td.innerHTML = data[dataKey].toString();
                    if (dataKey === 'postid') {
                        td.classList.add('column-id');
                    }
                    tableRow.appendChild(td);

                }

                block.appendChild(tableRow);
            }

        })
        .catch(function (error) {
            if (block) {
                let td;
                let tableRow = document.createElement('tr');
                td = document.createElement('td');
                td.innerHTML = error;
                tableRow.appendChild(td);
                block.appendChild(tableRow);
            }
        });

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function savePosition(name, value) {
    localStorage.setItem(name, value);
}

function getPosition(name) {
    return localStorage.getItem(name);
}