<?php

/**
 * Class westVM
 * @property string $id
 * @property West $west
 */
class westVM extends CFormModel
{


    private $_id;
    private $_west;

    public $country;
    public $region;

    public function getWest()
    {
        if(!(isset($this->_west)))
        {
            $this->_west = West::model()->findByPk($this->id);
        }
        return $this->_west;
    }


    public function getId()
    {
        if (!(isset($this->_id)))
        {
            throw new helpException("Обращение к несуществующему id в westVM");
        }
        return $this->_id;
    }

    public function setId($value)
    {
        if(!(isset($this->_id)))
        {
            $this->_id = $value;
        }
    }

    public function getCountries()
    {
        return functions::compactList(Yii::app()->dm->searchTour->getCountriesNameCyr(),'entityId','nameCyr');
    }

    public function getRegions()
    {
        return functions::compactList(Yii::app()->dm->searchTour->getRegionsNameCyrByCountryId($this->country),'entityId','nameCyr');
    }





    public function getCountriesDataProvider()
    {
        $ids = Yii::app()->dm->help->getCountriesIdByWestId($this->id);
        $criteria = new CDbCriteria();
        $criteria->addInCondition('entityId',$ids);
        $dataProvider = new CActiveDataProvider('Country', array(
            'criteria'=>$criteria
        ));
        return $dataProvider;
    }

    public function getRegionsDataProvider()
    {
        $ids = Yii::app()->dm->help->getRegionsIdByWestId($this->id);
        $criteria = new CDbCriteria();
        $criteria->addInCondition('entityId',$ids);
        $dataProvider = new CActiveDataProvider('Region', array(
            'criteria'=>$criteria
        ));
        return $dataProvider;
    }


}