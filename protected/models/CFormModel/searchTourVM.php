<?php

/**
 * Класс используется на странице главной поисковой формы
 * Class searchTourVM
 * @property CacheSearch cacheSearch
 * @property array filters
 */
class searchTourVM extends CFormModel
{

    //поисковая модель
    protected $cacheSearch;
    //переменные для виджета
    public $filterId = 1;
    public $searchAction = '/searchTour/getResult/';
    public $changeAction = '/searchTour/changeForm/';
    //updateId формируется следующим образом: '#'.$model->updateId.'_'.$model->filterId
    public $updateId = 'ftw';

    //region CFormModel
    public function rules()
    {
        return array(
            array('cacheSearch, filterId', 'safe'),
        );
    }

    public function attributeLabels()
    {
        return array(

        );
    }

    //endregion

    //region геттеры и сеттеры

    /**
     * массив названий стран и их идентификаторов для ddl
     * @return array($entityId=>$nameCyr)
     */
    public function getCountries()
    {
        $countries = Yii::app()->dm->searchTour->getCountriesNameCyr();
        return functions::compactList($countries, 'entityId','nameCyr');

    }

    /**
     * массив количества ночей для ddl
     * @return array
     */
    public function getNights()
    {
        $arr = array();
        for($i = 2; $i < 28; $i++)
        {
            $arr[$i] = $i;
        }
        return $arr;
    }

    /**
     * массив фильтров и их идентификаторов для виджета
     * @return array
     */
    public function getFilters()
    {
        if(Yii::app()->user->checkAccess("boFilters"))
        {
            //вернуть фильтры доступные для этого менеджера
            $filters = Yii::app()->dm->searchTour->getFilterNamesByManagerId(Yii::app()->user->id);
            return functions::compactList($filters, 'id','name');
        }
        else
        {
            //вернуть только с vision=3
            $filters = Yii::app()->dm->searchTour->getFilterNamesWithVision3();
            return functions::compactList($filters, 'id','name');
        }
    }

    public function getCacheSearch()
    {
        if (!(isset($this->cacheSearch)))
        {

            $this->cacheSearch = new CacheSearch();
            $this->cacheSearch->countryKey = DEFAULT_COUNTRY_ID;
            $this->cacheSearch->bDate1 = date('d.m.Y');
            $this->cacheSearch->bDate2 = date('d.m.Y', time() + (60 * 60 * 24)*7);
            $this->cacheSearch->durationMin = 7;
            $this->cacheSearch->durationMax = 7;
        }
        return $this->cacheSearch;
    }

    public function setCacheSearch(CacheSearch $cSearch)
    {
        $this->cacheSearch = $cSearch;
    }

    public function getRegions()
    {
        $regions = Yii::app()->dm->searchTour->getRegionsNameCyrByCountryIdAndFilterId($this->cacheSearch->countryKey, $this->filterId);
        return functions::compactList($regions, 'entityId','nameCyr');
    }

    public function getHotels()
    {
        return Yii::app()->dm->searchTour->getHotelsNameRegionIdCatIdByCountryIdAndFilterId($this->cacheSearch->countryKey, $this->filterId);
    }


    public function getCategories()
    {
        $categories = Yii::app()->dm->searchTour->getCategoriesCatTextByCountryId($this->cacheSearch->countryKey);
        return functions::compactList($categories, 'entityId','categoryText');
    }

    public function getMeals()
    {
        $meals = Yii::app()->dm->searchTour->getMealCyrByCountryId($this->cacheSearch->countryKey);
        return functions::compactList($meals, 'entityId','mealCyr');
    }

    public function getAdQty()
    {
        $arr = array();
        for($i = 2; $i < 6; $i++)
        {
            $arr[$i] = $i;
        }
        return $arr;
    }

    public function getChQty()
    {
        $arr = array(0=>'--',1=>1,2=>2);
        return $arr;
    }

    public function getChd()
    {
        $arr = array();
        for($i = 2; $i < 17; $i++)
        {
            $arr[$i] = $i;
        }
        return $arr;
    }


    //endregion





}