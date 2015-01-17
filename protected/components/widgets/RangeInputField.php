<?php
class RangeInputField extends CInputWidget
{

    public $attributeFrom;
    public $attributeTo;
    public $nameFrom;
    public $nameTo;
    public $valueFrom;
    public $valueTo;

    function run()
    {
        if (($this->attributeFrom!==null)&&(($this->attributeTo!==null))&&($this->model instanceof CModel))
        {
            $this->attribute = true;
            echo CHtml::activeTextField($this->model, $this->attributeFrom);
            echo ' &rarr;  ';
            echo CHtml::activeTextField($this->model, $this->attributeTo);
        }
        else
        {
            echo CHtml::activeTextField($this->nameFrom, $this->valueFrom);
            echo ' &rarr;  ';
            echo CHtml::activeTextField($this->nameTo, $this->valueTo);
        }
    }


}