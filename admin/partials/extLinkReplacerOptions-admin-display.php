<div class="wrap plagin-wrapper yanina">
    <div class="load-field" id="load-field">
        <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <h1 id="yanina-menu-title" class="text-center text-primary">Full image optimizer - options</h1>
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
    <div id="options">
        <form id="formOptions">
            <table class="form-table">

                <tbody>
                <tr>
                    <th scope="row"><label for="blogname">Выбрать посттип:</label></th>
                    <td>
                        <fieldset>
                            <?php
                            $postTypeOptions = $options['postType'];
                            $post_types = get_post_types(['publicly_queryable' => 1]);
                            $post_types['page'] = 'page';
                            unset($post_types['attachment']);
                            foreach ($post_types as $post_Type) {
                            ?>

                            <label for="<?php echo $post_Type; ?>">
                                <input name="postType[]" type="checkbox" class="postType" id="<?php echo $post_Type; ?>" value="<?php echo $post_Type; ?>"
                                    <?php if (in_array($post_Type, $postTypeOptions)) {
                                        echo 'checked';
                                    } ?>>
                                <?php echo $post_Type; ?>
                            </label></fieldset>
                        <br>
                        <?php
                        }
                        ?>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
            <h2 class="title">Настройки Imagick</h2>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label for="widthImagick">Ширина картинки</label></th>
                    <td><input name="width" type="number"
                               aria-describedby="tagline-description"
                               value="<?php if (key_exists('width', $options)) echo $options['width'] ?>"
                               class="regular-text">
                        <p class="description" id="tagline-description">Ширина картинки</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="heightImagick">Высота картинки</label></th>
                    <td><input name="height" type="number"
                               aria-describedby="tagline-description"
                               value="<?php if (key_exists('height', $options)) echo $options['height'] ?>"
                               class="regular-text">
                        <p class="description" id="tagline-description">Высота картинки</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="maxSizeImage">Размер картинки</label></th>
                    <td><input name="maxSize" type="number"
                               aria-describedby="tagline-description"
                               value="<?php if (key_exists('maxSize', $options)) echo $options['maxSize'] ?>"
                               placeholder="150000"
                               class="regular-text">
                        <p class="description" id="tagline-description">Максимальный размер картинки а байтах</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="qualityImagick">Качество картинки</label></th>
                    <td><input name="quality" type="number"
                               aria-describedby="tagline-description"
                               value="<?php if (key_exists('quality', $options)) echo $options['quality'] ?>"
                               class="regular-text">
                        <p class="description" id="tagline-description">Высота картинки</p>
                    </td>
                </tr>
                </tbody>
            </table>
            <h2 class="title">Опции для очистки картинок</h2>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">Месяц</th>
                    <td><input name="cleanMonth" type="number"
                               aria-describedby="tagline-description"
                               placeholder="01"
                               value="<?php if (key_exists('cleanMonth', $options)) echo $options['cleanMonth'] ?>"
                               class="regular-text">
                        <p class="description" id="tagline-description">Месяц для удаление картинок</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Год</th>
                    <td><input name="cleanYear" type="number"
                               aria-describedby="tagline-description"
                               placeholder="2021"
                               value="<?php if (key_exists('cleanYear', $options)) echo $options['cleanYear'] ?>"
                               class="regular-text">
                        <p class="description" id="tagline-description">Год для удаление картинок</p>
                    </td>
                </tr>

                </tbody>
            </table>
            <h2 class="title">Основыне опции</h2>
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">Скачать картинки?</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Скачать картинки?</span></legend>
                            <label for="downloadImage">
                                <input id="downloadImage" name="download" type="checkbox"
                                    <?php if (key_exists('download', $options)) if ($options['download'] === 'on') echo 'checked' ?>>
                                Скачивание и замена картинок на постах</label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Ужимать картинки?</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Надо ли оптимизировать картинки?</span></legend>
                            <label for="optimizeImage">
                                <input id="optimizeImage" name="optimize" type="checkbox"
                                    <?php if (key_exists('optimize', $options)) if ($options['optimize'] === 'on') echo 'checked' ?>>
                                Ужимать ли картинки?</label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Удялать битые картинки?</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Удялать битые картинки?</span></legend>
                            <label for="deleteImage">
                                <input id="deleteImage" name="delete" type="checkbox"
                                    <?php if (key_exists('delete', $options)) if ($options['delete'] === 'on') echo 'checked' ?>>
                                Удаление битых картинок на постах</label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Включить фильтр?</th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Включить фильтр?</span></legend>
                            <label for="hook">
                                <input id="hook" name="hookEnable" type="checkbox"
                                    <?php if (key_exists('hookEnable', $options)) if ($options['hookEnable'] === 'on') echo 'checked' ?>>
                                Включает плагин при обновлении или создании поста</label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dirPath">Папка где оптимизировать картинки</label></th>
                    <td style="width: 500px;"><input name="dirPath" style="width: 500px;" type="url"
                                                     aria-describedby="tagline-description"
                                                     value="<?php echo $options['dirPath'] ? $options['dirPath'] : wp_upload_dir()['basedir'];  ?>"
                                                     class="regular-text">
                        <p class="description" id="dir-description"></p>
                    </td>
                    <td>
<!--                        <button id="testDirPath">Тест пути</button>-->
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="timeDelay">Задержа между запросами</label></th>
                    <td><input name="timeDelay" type="text"
                               aria-describedby="tagline-description"
                               value="<?php echo $options['timeDelay'] ?>"
                               class="regular-text">
                        <p class="description" id="tagline-description">Задержка между обработкой постов?</p>
                    </td>
                </tr>
                </tbody>
            </table>

            <p class="submit"><input type="button" name="save" id="save" class="button button-primary"
                                     value="Сохранить изменения"></p>
        </form>
    </div>


</div>

<script>


    const form = document.querySelector('#formOptions');
    const loadField = document.getElementById('load-field');
    const start = document.querySelector('#save');
    const testDirPath = document.getElementById('testDirPath');




    start.addEventListener('click', e => {

        e.preventDefault()
        loadField.style.display = 'block';
        start.setAttribute('disabled', 'disabled');

        let formData = new FormData(form);

        getResult(null, formData, '/wp-json/ext-link-replacer/v1/updateoptions', true)

        setTimeout(
            function () {
                loadField.style.display = 'none';
                start.removeAttribute('disabled');
            }, 100
        );
    }, false)


    // function testDir() {
    //     pathDir = document.getElementById('dirPath').value;
    //
    //     let data = JSON.stringify({
    //         "action": 'testDir',
    //         "pathDir": pathDir,
    //         "extLinkReplacer": "extLinkReplacer",
    //     });
    //     xhr.open('POST', '?page=extLinkReplacer');
    //     xhr.setRequestHeader("Content-Type", "application/json");
    //     xhr.send(data);
    //
    //     xhr.onload = function () {
    //
    //         if (xhr.status != 200) { // анализируем HTTP-статус ответа, если статус не 200, то произошла ошибка
    //
    //
    //         } else { // если всё прошло гладко, выводим результат
    //             let response = xhr.responseText;
    //
    //             dirDescription.innerText = response;
    //         }
    //     }
    // }
    //
    // saveManual.addEventListener('click', saveOptions);
    // testDirPath.addEventListener('click', testDir);

</script>