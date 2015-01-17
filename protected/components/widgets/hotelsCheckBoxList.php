<?php

class hotelsCheckBoxList extends CWidget
{


    //префикс к id(hotel widget)
    public $prefixId = 'hw';
    //модель и аттрибут и информация
    public $model;
    public $attribute;
    public $data = array();
    //что вернуть, если не выбрано ничего
    public $uncheck = '';
    //разделитель между чекбоксами
    public $separator = "<br/>\n";
    //родительский контейнер
    public $container = 'span';
    //шаблон одного чекбокса
    public $template = '{input} {label}';





    public function run()
    {
        //определить name, id
        $name = MyWidgetHTML::resolveModelName($this->model, $this->attribute, $this->prefixId);
        $baseId = MyWidgetHTML::resolveIdByName($name);
        //генерация hiddenField
        $hidden = MyWidgetHTML::hiddenField($name, $this->uncheck, $baseId.'_hidden');
        //name для чекбоксов должен оканчиваться на []
        if(substr($name,-2)!=='[]')
        {
            $name.='[]';
        }
        //порядковый номер чекбокса
        $countId = 0;
        //массив чекбоксов
        $items = array();
        //массив аттрибутов тэга
        $htmlOptions = array();
        //генерация чекбоксов
        foreach($this->data as $hotelData)
        {
            //значение чекбокса
            $htmlOptions['value'] = $hotelData['entityId'];
            //id чекбокса
            $htmlOptions['id']= $baseId.'_'.$countId++;
            //доп.информация чекбокса
            $htmlOptions['data-region'] = $hotelData['regionId'];
            $htmlOptions['data-category'] = $hotelData['categoryId'];
            //генерация чекбокса
            $input = MyWidgetHTML::checkBox($name, false, $htmlOptions);
            //генерация описания для чекбокса
            $label = MyWidgetHTML::label($hotelData['name'],$htmlOptions['id']);
            $items[] = strtr($this->template, array(
                '{input}'=>$input,
                '{label}'=>$label,
            ));
        }
        //общий контейнер с чекбоксами
        $htmlOptions = array();
        $htmlOptions['id'] = $baseId.'_unchecked';
        $body = MyWidgetHTML::tag($this->container, $htmlOptions, implode($this->separator,$items));
        //поисковый инпут
        $htmlOptions = array();
        $htmlOptions['id'] = $baseId.'_search';
        $search = MyWidgetHTML::tag('input',$htmlOptions);
        //контейнер с выделенными чекбоксами
        $htmlOptions = array();
        $htmlOptions['id'] = $baseId.'_checked';
        $start = 'выбраны все отели';
        $checkedField = MyWidgetHTML::tag($this->container, $htmlOptions, $start);
        //регистрация javascript
        $js=<<<EOD
'//здесь должен быть весь javascript виджета '
EOD;
        $cs=Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $cs->registerScript($baseId, $js);
        echo $hidden.$body.$this->separator.$search.$this->separator.$checkedField;
    }









}