<?php

/**
 * Class filterVM
 * @property string $id
 * @property Filter $filter
 */
class filterVM extends CFormModel
{
    private $_filter;
    private $_id;
    private $_managers;
    private $_visions;

    public $saved = false;

    public $country;
    public $region;
    public $operator;
    public $hotel;

    public function getId()
    {
        if (!(isset($this->_id)))
        {
            throw new SearchTourException("Обращение к несуществующему id в filterVM");
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





    public function getFilter()
    {
        if(!(isset($this->_filter)))
        {
            if($this->scenario === 'create')
            {
                $this->_filter = new Filter('create');
            }
            else
            {
                $this->_filter = Filter::model()->findByPk($this->id);
            }

        }
        return $this->_filter;
    }

    public function getOperatorsDataProvider()
    {
        return Yii::app()->dm->searchTour->getOperatorsDataProviderByFilterId($this->id);
    }
    public function getCountryDataProvider()
    {
        return Yii::app()->dm->searchTour->getCountryDataProviderByFilterId($this->id);
    }
    public function getRegionDataProvider()
    {
        return Yii::app()->dm->searchTour->getRegionDataProviderByFilterId($this->id);
    }
    public function getHotelDataProvider()
    {
        return Yii::app()->dm->searchTour->getHotelDataProviderByFilterId($this->id);
    }



    public function getOperators()
    {
        $operators = Yii::app()->dm->searchTour->getOperatorNamesByFilterId($this->id);
        return functions::compactList($operators,'entityId','name');
    }

    public function getCountries()
    {
        $countries = Yii::app()->dm->searchTour->getCountriesNameCyrByFilterId($this->id);
        return functions::compactList($countries,'entityId','nameCyr');
    }

    public function getCountriesByOperatorId($operatorId)
    {
        $countries = Yii::app()->dm->searchTour->getCountriesNameCyrByFilterIdAndOperatorId($this->id, $operatorId);
        return $countries;
    }



    public function getHotels()
    {
        if($this->region)
        {
            $hotels = Yii::app()->dm->searchTour->getHotelsNameByRegionId($this->region);
            return functions::compactList($hotels,'entityId','name');
        }
        elseif($this->country)
        {
            $hotels = Yii::app()->dm->searchTour->getHotelsNameByCountryId($this->country);
            return functions::compactList($hotels,'entityId','name');
        }
        else
        {
            return array();
        }
    }


    public function getRegions()
    {
        $regions = Yii::app()->dm->searchTour->getRegionsNameCyrByCountryId($this->country);
        return functions::compactList($regions,'entityId','nameCyr');
    }

    public function getManagers()
    {

        if(!(isset($this->_managers)))
        {

            if(Yii::app()->user->checkAccess('filterAdmin'))
            {
                $this->_managers = functions::compactList(Yii::app()->dm->membership->getAccountNamesByAuthItem('boFilters'),'id','username');
            }
            else
            {
                $this->_managers = functions::compactList(Yii::app()->dm->membership->getAccountNameById(Yii::app()->user->id),'id','username');
            }

        }
        return $this->_managers;
    }


    public function getVisions()
    {
        if(!(isset($this->_visions)))
        {
            $this->_visions = array();
            $this->_visions[1] = "Виден только мне";
            if(Yii::app()->user->checkAccess('filterAdmin'))
            {
                $this->_visions[2] = "Виден всем с правами filterAdmin";
                $this->_visions[3] = "Виден всем";
            }
        }
        return $this->_visions;
    }

    public function getRegionsByCountryId($countryId)
    {
        return Yii::app()->dm->searchTour->getRegionsNameCyrByCountryIdAndFilterId($countryId, $this->id);
    }
    public function getHotelsByRegionId($regionId)
    {
        return Yii::app()->dm->searchTour->getHotelsNameByRegionIdAndFilterId($regionId, $this->id);
    }

    public function getHotelsByCountryId($countryId)
    {
        return Yii::app()->dm->searchTour->getHotelsNameByCountryIdAndFilterId($countryId, $this->id);

    }







}