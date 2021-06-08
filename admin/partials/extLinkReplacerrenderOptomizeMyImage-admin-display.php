<div class="wrap plagin-wrapper yanina">
    <h1 id="yanina-menu-title" class="text-center text-primary">Full image optimizer - directory optimizer</h1>
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
            <span id="imageCountTag">Images count: </span>
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
            <th scope="col" id="image" class="manage-column">Image name</th>
            <th scope="col" id="alt" class="manage-column">Old size (Kb)</th>
            <th scope="col" id="src" class="manage-column">New size (Kb)</th>
            <th scope="col" id="error" class="manage-column">Error</th>
        </tr>
        </thead>
        <tbody id="theList"></tbody>
        <tfoot>
        <tr>
            <th scope="col" id="image" class="manage-column">Image name</th>
            <th scope="col" id="alt" class="manage-column">Old size (Kb)</th>
            <th scope="col" id="src" class="manage-column">New size (Kb)</th>
            <th scope="col" id="error" class="manage-column">Error</th>
        </tr>
        </tfoot>

    </table>
</div>
<script>

    const imageCountTag = document.querySelector('#imageCountTag');
    const startButton = document.querySelector('#start');
    const pauseButton = document.querySelector('#pause');

    getImage('/wp-json/ext-link-replacer/v1/getallimage', imageCountTag, [startButton], 'allImages')

    let position = getPosition('optimize') ?? 0;
    let pause = false;

    startButton.addEventListener( 'click', async e => {
        await start(e.target);
    })

    async function start(e) {
        e.setAttribute('disabled', 'disabled');

        const table = document.querySelector('#theList');

        for ( let key in allImages ) {

            if (pause)  {
                position = getPosition('optimize') ?? 0;
                break;
            }

            if (Number(position) >= Number(key) && Number(key) !== 0) {
                continue;
            }

            let data = {
                image: allImages[key],
            };
            await getResult(table, data, '/wp-json/ext-link-replacer/v1/optimizeimages')

            savePosition('optimize', key);

            await sleep(<?php echo $option->getOption('timeDelay') ?>)

            if (allImages.length - 1 === Number(key)) {
                savePosition('optimize', 0);
                e.removeAttribute('disabled');
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