<?php
Yii::import('zii.widgets.jui.CJuiWidget');
class searchTourTabs extends CJuiWidget
{


    /**
     * @var строковое название контейнера для виджета. По умолчанию 'div'.
     */
    public $tagName='div';

    /**
     * Вид вкладки
     */
    public $headerTemplate='<li><a href="{url}" title="{id}">{title}</a></li>';

    /**
     * @var searchTourVM модель
     */
    protected $model;

    /**
     * @var string the template that is used to generated every tab content.
     * The token "{content}" in the template will be replaced with the panel content
     * and the token "{id}" with the tab ID.
     */
//    public $contentTemplate='<div id="{id}">{content}</div>';
    public $contentTemplate='<div id="{id}"></div>';


    /**
     * Запуск виджета, отрабатывает при вызове endWidget
     */
    public function run()
    {
        //идентификация общего контейнера id для css
//        $id = $this->getId();
        $id = 'ftw';
        $this->setId($id);
        $this->htmlOptions['id'] = $id;
        //открывающий тэг
        echo CHtml::openTag($this->tagName,$this->htmlOptions)."\n";
        $tabsOut="";
        //счетчик вкладок для выделения выбранной
        $tabCount=0;
        $contentOut = '';
        foreach($this->model->filters as $param => $tab)
        {
            if($param === $this->model->filterId)
            {
                $this->options['selected'] = $tabCount;
                $tabId = $id.'_'.$param;
                $url = $this->controller->createUrl($this->model->changeAction, array('filter'=>$param));
                $tabsOut .= strtr($this->headerTemplate,array('{title}'=>$tab,'{url}' =>$url ,'{id}'=>$tabId))."\n";
                $contentOut.=strtr($this->contentTemplate,array('{id}'=>$tabId))."\n";
            }
            else
            {
                $tabId = $id.'_'.$param;
                $url = $this->controller->createUrl($this->model->changeAction, array('filter'=>$param));
                $tabsOut .= strtr($this->headerTemplate,array('{title}'=>$tab,'{url}' =>$url ,'{id}'=>$tabId))."\n";
            }
            $tabCount++;
        }
        echo "<ul>\n".$tabsOut."</ul>\n";
        echo $contentOut;
//
        echo CHtml::closeTag($this->tagName)."\n";
        $options=CJavaScript::encode($this->options);
        Yii::app()->getClientScript()->registerScript(__CLASS__.'#'.$id,"jQuery('#{$id}').tabs($options);");
    }

    /**
     * Registers the core script files.
     * This method overrides the parent implementation by registering the cookie plugin when cookie option is used.
     */
    protected function registerCoreScripts()
    {
        parent::registerCoreScripts();
        if(isset($this->options['cookie']))
        {
            Yii::app()->getClientScript()->registerCoreScript('cookie');
        }

    }

    public function getModel()
    {
        if (!(isset($this->model)))
        {
            $this->model = new searchTourVM();
        }
        return $this->model;
    }


    public function setModel(searchTourVM $vModel)
    {
        $this->model = $vModel;
    }



}