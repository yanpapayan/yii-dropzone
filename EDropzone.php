<?php
/**
 *
 * Yii extension to the drag n drop HTML5 file upload Dropzone.js
 * For more info, see @link http://www.dropzonejs.com/
 *
 * @link https://github.com/subdee/yii-dropzone
 *
 * @author Konstantinos Thermos
 *
 * @copyright
 * Copyright (c) 2013 Konstantinos Thermos
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT
 * LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
 * NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */
class EDropzone extends CWidget {
    /**
     * @var string The name of the file field
     */
    public $name = false;
    /**
     * @var CModel The model for the file field
     */
    public $model = false;
    /**
     * @var string The attribute of the model
     */
    public $attribute = false;
    /**
     * @var array An array of options that are supported by Dropzone
     */
    public $options = array();
    /**
     * @var string The URL that handles the file upload
     */
    public $url = false;
    /**
     * @var array An array of supported MIME types
     */
    public $mimeTypes = array();
    /**
     * @var array The Javascript to be called on any event
     */
    public $events = array();

    /**
     * @var array The HTML options using in the tag div
     */
    public $htmlOptions = array();

    /**
     * @var string The path to custom css file
     */
    public $customStyle = false;


    /**
     * Create a div and the appropriate Javascript to make the div into the file upload area
     */
    public function run() {
        if (!$this->url || $this->url == '')
            $this->url = Yii::app()->createUrl('site/upload');

        if (!$this->name && ($this->model && $this->attribute) && $this->model instanceof CModel)
            $this->name = CHtml::activeName($this->model, $this->attribute);

        echo CHtml::openTag('div', CMap::mergeArray(array('class' => 'dropzone', 'id' => $this->name), $this->htmlOptions));
        echo CHtml::closeTag('div');


        $this->mimeTypes = CJavaScript::encode($this->mimeTypes);

        $onEvents = '';
        foreach($this->events as $event => $func){
                $onEvents .= "this.on('{$event}', function(param, param2, param3){{$func}} );";

        }


        $options = CMap::mergeArray(array(
                'url' => $this->url,
                'parallelUploads' => 5,
                'paramName' => $this->name,
                'accept' => "js:function(file, done){if(jQuery.inArray(file.type,{$this->mimeTypes}) == -1 ){done('File type not allowed.');}else{done();}}",
                'init' => "js:function(){{$onEvents}}"
                ), $this->options);

        $options = CJavaScript::encode($options);

        $script = "Dropzone.options.{$this->name} = {$options}";

        $this->registerAssets();
        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), $script, CClientScript::POS_LOAD);
    }

    private function registerAssets() {
        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $baseUrl = Yii::app()->getAssetManager()->publish($basePath, false, 1, YII_DEBUG);
        Yii::app()->getClientScript()->registerCoreScript('jquery');
        Yii::app()->getClientScript()->registerScriptFile("{$baseUrl}/js/dropzone.js", CClientScript::POS_BEGIN);
        Yii::app()->getClientScript()->registerCssFile("{$baseUrl}/css/dropzone.css");
        if(!$this->customStyle || $this->customStyle !== '')
            Yii::app()->getClientScript()->registerCssFile($this->customStyle);
    }

}
?>
