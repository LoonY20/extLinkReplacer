<div class="wrap plagin-wrapper yanina">
    <h1 id="yanina-menu-title" class="text-center text-primary">Full image optimizer - posts</h1>
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
        <div class="counter">
            <div class="col-2" id="postCountTag">Post count: </div>
            <div class="col-2" id="postProcessedTag">Processed posts: <span id="processedCount"></span></div>
        </div>
        <div class="buttonBlock">
            <input type="button" name="pause" id="pause" class="button button-primary" value="Pause">
            <input type="button" name="start" id="start" class="button button-primary" value="Start">
        </div>
    </div>

    <div class="row mb-3" id="progBar" style="display: none;">
        <label for="prog_bar" class="col-2 offset-2">Downloading progress:</label>
        <progress id="prog_bar" class="col-5" style="height: 30px" value="0" max=""> 32%</progress>
        Time left: ~ <span id="timeLeft"></span>s
    </div>
    <h2 id="success-operation" class="success-operation">Operation success</h2>
    <h2 class="failed-operation" id="fail"></h2>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
        <tr>
            <th scope="col" id="postId" class="manage-column column-id">Post ID</th>
            <th scope="col" id="download" class="manage-column">Download</th>
            <th scope="col" id="alt" class="manage-column">Alt</th>
            <th scope="col" id="src" class="manage-column">Edit src</th>
            <th scope="col" id="delete" class="manage-column">Delete</th>
            <th scope="col" id="delete" class="manage-column">Update</th>
            <th scope="col" id="message" class="manage-column">Message</th>
            <th scope="col" id="error" class="manage-column">Error</th>
        </tr>
        </thead>
        <tbody id="theList"></tbody>
        <tfoot>
        <tr>
            <th scope="col" class="manage-column column-id">Post ID</th>
            <th scope="col" class="manage-column">Download</th>
            <th scope="col" class="manage-column">Alt</th>
            <th scope="col" class="manage-column">Edit src</th>
            <th scope="col" class="manage-column">Delete</th>
            <th scope="col" class="manage-column">Update</th>
            <th scope="col" class="manage-column">Message</th>
            <th scope="col" class="manage-column">Error</th>
        </tr>
        </tfoot>

    </table>
</div>
<script>

    const postCountTag = document.querySelector('#postCountTag');
    const startButton = document.querySelector('#start');
    const pauseButton = document.querySelector('#pause');
    const processedCount = document.querySelector('#processedCount');
    const successOperation = document.querySelector('#success-operation');

    getPostIds(postCountTag, [startButton])

    let pause = false;
    let position = getPosition('main') ?? 0;
    processedCount.innerHTML =  position;

    startButton.addEventListener( 'click', async e => {
        await start(e.target);
    })

    async function start (button){
        button.setAttribute('disabled', 'disabled');
        const table = document.querySelector('#theList');
        for (let key in allPosts) {
            if (pause) {
                position = getPosition('optimize') ?? 0;
                break;
            }
            if (Number(position) >= Number(key) && Number(key) !== 0) {
                continue;
            }
            let data = {
                id: allPosts[key],
            };
            await getResult(table, data, '/wp-json/ext-link-replacer/v1/optimizeposts')
            savePosition('main', key);
            processedCount.innerHTML = Number(key) + 1;
            await sleep(<?php echo $option->getOption('timeDelay') ?>)
            if (allPosts.length - 1 === Number(key)) {
                savePosition('main', 0);
                successOperation.style.display = 'block';
                button.removeAttribute('disabled');
            }

        }
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