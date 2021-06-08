<?php


class Options
{

    private $options;

    public function __construct()
    {

        $this->options = get_option('extLinkReplacerOption');

    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getOption($name)
    {
        if (array_key_exists($name, $this->options)) return $this->options[$name];
        return false;
    }

    public function updateOptions()
    {

        $option = [
            'postType' => $_POST['postType'],
            'download' => $_POST['download'],
            'optimize' => $_POST['optimize'],
            'delete' => $_POST['delete'],
            'width' => $_POST['width'],
            'height' => $_POST['height'],
            'maxSize' => $_POST['maxSize'],
            'quality' => $_POST['quality'],
            'timeDelay' => $_POST['timeDelay'],
            'dirPath' => stripslashes($_POST['dirPath']),
            'hookEnable' => $_POST['hookEnable'],
            'cleanMonth' => $_POST['cleanMonth'],
            'cleanYear' => $_POST['cleanYear'],
        ];
        update_option('extLinkReplacerOption', $option);

    }

}