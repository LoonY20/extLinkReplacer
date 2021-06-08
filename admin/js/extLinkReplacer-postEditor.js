var performance = window.performance || window.mozPerformance || window.msPerformance || window.webkitPerformance || {};
var requests = performance.getEntriesByType('resource');
const select = wp.data.select('core/editor');
performance.clearResourceTimings();

const unsubscribe = wp.data.subscribe(function () {
    let select = wp.data.select('core/editor');
    var isSavingPost = select.isSavingPost();
    var isAutosavingPost = select.isAutosavingPost();
    var didPostSaveRequestSucceed = select.didPostSaveRequestSucceed();

    if (isSavingPost && !isAutosavingPost && didPostSaveRequestSucceed) {
        console.log(performance.getEntriesByType('resource'));
        if (performance.getEntriesByType('resource').length > 1) {
            performance.clearResourceTimings();
        } else if (performance.getEntriesByType('resource').length === 1) {
            let url = performance.getEntriesByType('resource')[0].name;
            let type = performance.getEntriesByType('resource')[0].initiatorType;
            let match = url.match(/https?:\/\/wordpress\/wp-json\/wp\/v2\/posts\/[0-9]{1,6}\?_locale=user/);
            console.log(match);
            if (match && type === 'fetch') {
                let postId = select.getCurrentPostId();
                if (!postId) {
                    postId = match[1];
                }
                sendMyRequest(postId);
                performance.clearResourceTimings();
            } else {
                performance.clearResourceTimings();
            }

        }

    }

});

function sendMyRequest(postId) {
    console.log(1)
    let formData = new FormData();
    formData.append('id', postId);

    fetch('/wp-json/ext-link-replacer/v1/optimizePosts', {
        method: 'POST',
        body: formData,
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce,
        }
    })
        .then((response) => {

            if (!response.ok) {
                return response.json()
                    .catch(() => {
                        wp.data.dispatch('core/notices').createNotice(
                            'error',
                            'Плагин ExtLinkReplacer совершил ошибку - Но ваши действия над постом сохранились status: '+response.status,
                        )
                    })
                    .then(({message}) => {
                        wp.data.dispatch('core/notices').createNotice(
                            'error',
                            'Плагин ExtLinkReplacer совершил ошибку - '+message+' - Но ваши действия над постом сохранились status: '+response.status,
                        )
                    });
            }

            return response.json();


        })
        .then((data) => {
            if (data.postUpdate) {
                wp.data.dispatch('core/notices').createNotice(
                    'success',
                    'Плагин ExtLinkReplacer произвел действия над постом - что бы увидеть изменения страница перезагрузиться через 5 секунд',
                    {
                        isDismissible: true, // Whether the user can dismiss the notice.
                        // Any actions the user can perform.
                        actions: [
                            {
                                url: window.location.href,
                                label: 'Reload post',
                            },
                        ],
                    }
                )

                setTimeout(() => {location.reload();}, 5000 )
            } else {
                wp.data.dispatch('core/notices').createNotice(
                    'info',
                    'Плагин ExtLinkReplacer ничего не заменил',
                )
            }


        })

}