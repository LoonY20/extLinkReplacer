<div class="wrap plagin-wrapper yanina">
    <h1 id="yanina-menu-title" class="text-center text-primary">Full image optimizer - cleaner</h1>
    <?php
    require_once plugin_dir_path(__FILE__) . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Options.php';
    $option = new Options();
    $options = $option->getOptions();
    if (!extension_loaded('imagick')) {
        ?>
        <div class="imagickAlert" role="alert">
            Your php didn't have 'Imagick' extension, compress function won't work!
        </div>
        <?php
    } ?>

    <div class="row between">

        <div class="col-2 infoBlock">
            <span id="imageCountTag">Images count in uploads(month): </span>
            <span id="imageCountInDatabaseTag">Images count in database: </span>
            <span id="allSavedImages">Saved images count in database: </span>
            <span id="postCountTag">Post count: </span>
        </div>
        <div class="buttonBlock">
            <input type="button" name="pause" id="pause" class="button button-primary" value="Pause">
            <input type="button" name="checkImages" id="checkImages" class="button button-primary" value="Check images">
            <input type="button" name="start" id="start" class="button button-primary" value="Start">
        </div>

    </div>
    <h2 id="success-operation" class="success-operation">Operation success</h2>
    <h2 class="failed-operation" id="fail"></h2>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
        <tr>
            <th scope="col" id="image" class="manage-column">Image path</th>
            <th scope="col" id="error" class="manage-column">Action</th>
        </tr>
        </thead>
        <tbody id="theList"></tbody>
        <tfoot>
        <tr>
            <th scope="col" id="image" class="manage-column">Image path</th>
            <th scope="col" id="error" class="manage-column">Action</th>
        </tr>
        </tfoot>

    </table>
</div>
<script>

    const imageByMonthCountTag = document.querySelector('#imageCountTag');
    const postCountTag = document.querySelector('#postCountTag');
    const allSavedImagesTag = document.querySelector('#allSavedImages');
    const imageCountInDatabaseTag = document.querySelector('#imageCountInDatabaseTag');
    const startButton = document.querySelector('#start');
    const checkButton = document.querySelector('#checkImages');
    const pauseButton = document.querySelector('#pause');
    const table = document.querySelector('#theList');

    let pause = false;
    let position = getPosition('optimize') ?? 0;

    getImage('/wp-json/ext-link-replacer/v1/getallimagebymonth', imageByMonthCountTag, [startButton], 'allImages')
    getImage('/wp-json/ext-link-replacer/v1/getallsavedimages', allSavedImagesTag, [startButton], 'allSavedImages')
    getImage('/wp-json/ext-link-replacer/v1/getallimagesfromdatabase', imageCountInDatabaseTag, [startButton], 'allImagesFromDatabase')
    getPostIds(postCountTag, [startButton])


    startButton.addEventListener( 'click', async e => {await start(e.target);});

    checkButton.addEventListener('click', checkImage);

    async function start(e) {
        e.setAttribute('disabled', 'disabled');
        for ( let key in allImages ) {

            if (pause)  {
                position = getPosition('optimize') ?? 0;
                break;
            }
            if (Number(position) >= Number(key) && Number(key) !== 0) {
                continue;
            }
            let data = {
                path: allImages[key],
            };
            await getResult(table, data, '/wp-json/ext-link-replacer/v1/cleartrashimages')
            savePosition('optimize', key);
            await sleep(<?php echo $option->getOption('timeDelay') ?>)
            if (allImages.length - 1 === Number(key)) {
                savePosition('optimize', 0);
                e.removeAttribute('disabled');
            }
        }
    }

    async function checkImage() {
        checkButton.setAttribute('disabled', 'disabled');

        for (const allImagesKey in allImagesFromDatabase) {

            let data = {
                path: allImagesFromDatabase[allImagesKey]
            }

            await getResult(null, data, '/wp-json/ext-link-replacer/v1/saveimage');

            await sleep(<?php echo $option->getOption('timeDelay') ?>)
        }

        await checkPosts();
    }

    async function checkPosts() {

        for (const allPostsKey in allPosts) {

            let data = {
                id: allPosts[allPostsKey]
            }
            await getResult(null, data, '/wp-json/ext-link-replacer/v1/saveimagefrompost');

            await sleep(<?php echo $option->getOption('timeDelay') ?>)
        }

        checkButton.removeAttribute('disabled');
    }

    pauseButton.addEventListener('click', e => {

        if (pause) {
            pause = false;
            e.target.value = 'Pause';
            start(startButton);

        } else {
            pause = true;
            e.target.value = 'Continue'
        }

    })

</script>