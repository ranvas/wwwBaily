<?php
abstract class EntityAPI extends CActiveRecord
{
    public $entityId;
    public $date;



    //region getters and setters

    public function getDate()
    {
        if ($this->date)
        {
            return $this->date;
        }
        else
        {
            $this->date = time();
        }
        return $this->date;
    }

    public function setDate()
    {
        $this->date = time();
    }



    //endregion


}