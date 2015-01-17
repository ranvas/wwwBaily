<?php
Yii::import('zii.widgets.jui.CJuiDatePicker');

class RangeDatePicker extends CJuiDatePicker
{
    public $attributeFrom;
    public $attributeTo;
    public $nameFrom;
    public $nameTo;
    public $valueFrom;
    public $valueTo;



    public $language;
    //js для локализации виджета
    public $i18nScriptFile='jquery-ui-i18n.min.js';


    function run()
    {



        if (($this->attributeFrom!==null)&&(($this->attributeTo!==null))&&($this->model instanceof CModel))
        {
            $this->attribute = true;
            $this->brushDatePicker($this->model, $this->attributeFrom);
            echo ' &rarr;  ';
            $this->brushDatePicker($this->model, $this->attributeTo);
        }
        else
        {
            $this->brushDatePicker($this->nameFrom, $this->valueFrom);
            echo ' &rarr;  ';
            $this->brushDatePicker($this->nameTo, $this->valueTo);
        }
    }

    function brushDatePicker($model, $attribute)
    {
        //сгенерировать id для скриптов
        $id=$this->resolveID();
        //отрисовать форму
        echo CHtml::activeTextField($model,$attribute,$this->htmlOptions);
        $options=CJavaScript::encode($this->options);
        //для ru версии
        if($this->language!='' && $this->language!='en')
        {
            $this->registerScriptFile($this->i18nScriptFile);
            $js = "jQuery('#{$id}').datepicker(jQuery.extend({showMonthAfterYear:false},jQuery.datepicker.regional['{$this->language}'],{$options}));";
        }
        //для en-версии
        else
        {
            $js = "jQuery('#{$id}').datepicker($options);";
        }
        //регистрация js и css
        $cs = Yii::app()->getClientScript();
        $cs->registerScript(__CLASS__.'#'.$id,$js);
    }

    function resolveID()
    {
        $id = 'dp_'.functions::generateString(2);
        $this->id = $id;
        $this->htmlOptions['id'] = $id;
        return $id;
    }




}