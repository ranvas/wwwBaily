<?php
class RangeDropDownList extends CInputWidget
{
    public $attributeFrom;
    public $attributeTo;
    public $nameFrom;
    public $nameTo;
    public $valueFrom;
    public $valueTo;
    public $valuesFrom = array();
    public $valuesTo = array();
    public $optionsFrom = array();
    public $optionsTo = array();


    function run()
    {
        if(is_array($this->valuesFrom)&&(is_array($this->valuesTo)))
        {
            if (($this->attributeFrom!==null)&&(($this->attributeTo!==null))&&($this->model instanceof CModel))
            {
                $this->attribute = true;
                echo CHtml::activeDropDownList($this->model, $this->attributeFrom, $this->valuesFrom, $this->optionsFrom);
                echo ' &rarr;  ';
                echo CHtml::activeDropDownList($this->model, $this->attributeTo, $this->valuesTo, $this->optionsTo);
            }
            else
            {
                echo CHtml::activeDropDownList($this->nameFrom, $this->valueFrom, $this->valuesFrom, $this->optionsFrom);
                echo ' &rarr;  ';
                echo CHtml::activeDropDownList($this->nameTo, $this->valueTo, $this->valuesFrom, $this->optionsTo);
            }
        }
        else
        {
            throw new SearchTourException('ошибка вывода виджета rangeDropDownList');
        }
    }
}