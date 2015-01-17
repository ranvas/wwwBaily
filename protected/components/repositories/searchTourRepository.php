<?php
//region exceptions

class SearchTourException extends CException {}

//endregion

class searchTourRepository extends CComponent
{

    //region служебные

        //region filters

    private function saveFilter(Filter $filter)
    {
        if($filter->save())
        {
            return $filter;
        }
        else
        {
            return false;
        }
    }

    private function deleteFilter(Filter $filter)
    {
        if($filter->delete())
        {
            return $filter;
        }
        else
        {
            return false;
        }
    }

        //endregion


    //endregion
    //region filters
//region crud
    /**
     * @param $filterId
     * @param $entityId
     * @return mixed выбрать количество записей filterCountries, по фильтру и курорту
     */
    public function checkCountryByFilterIdAndEntityId($filterId, $entityId)
    {
        $countryId = functions::separationIdsMedium($entityId);
        $sql = "select count(*) from filterCountries where filterId=$filterId and entityId = $countryId";
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }
    public function getHotelsNameByCountryIdAndFilterId($countryId, $filterId)
    {
        $sql = "select entityId, name from hotels where entityId in(select entityId from filterHotels where filterId = $filterId) and countryId = $countryId";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getHotelsNameByRegionIdAndFilterId($regionId, $filterId)
    {
        $sql = "select entityId, name from hotels where entityId in(select entityId from filterHotels where filterId = $filterId) and regionId = $regionId";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getRegionsNameCyrByCountryIdAndFilterId($countryId, $filterId)
    {
        $sql = "select entityId, nameCyr from regions where entityId in(select entityId from filterRegions where filterId = $filterId) and countryId = $countryId";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getCountriesNameCyrByFilterId($filterId)
    {
        $sql = "select entityId,nameCyr from countries where entityId in (select countryId from operatorCountry where operatorId in (select entityId from filterOperators where filterId = $filterId));";
        return Yii::app()->db->createCommand($sql)->queryAll();

    }
    public function getOperatorNamesByFilterId($filterId)
    {
        $sql = 'select entityId, name from operators where entityId not in (select entityId from filterOperators where filterId='.$filterId.')';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getCountryDataProviderByFilterId($filterId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('entityId in (select entityId from filterCountries where filterId='.$filterId.')');
        $dataProvider = new CActiveDataProvider('Country', array(
            'criteria'=>$criteria
        ));
        return $dataProvider;
    }
    public function getHotelDataProviderByFilterId($filterId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('entityId in (select entityId from filterHotels where filterId='.$filterId.')');
        $dataProvider = new CActiveDataProvider('Hotel', array(
            'criteria'=>$criteria
        ));
        return $dataProvider;
    }
    public function getRegionDataProviderByFilterId($filterId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('entityId in (select entityId from filterRegions where filterId='.$filterId.')');
        $dataProvider = new CActiveDataProvider('Region', array(
            'criteria'=>$criteria
        ));
        return $dataProvider;
    }
    public function getOperatorsDataProviderByFilterId($filterId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('entityId in (select entityId from filterOperators where filterId='.$filterId.')');
        $dataProvider = new CActiveDataProvider('Operator', array(
            'criteria'=>$criteria
        ));
        return $dataProvider;
    }
    public function getFilterNamesByManagerId($managerId)
    {
        $sql = 'select id,name from filters where vision = 3,2 or (vision = 1 and managerId = '.$managerId.')';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getFilterNamesWithVision3()
    {
        $sql = 'select id,name from filters where vision = 3';
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getCountriesNameCyrByFilterIdAndOperatorId($filterId,$operatorId)
    {
        $sql = "select entityId, nameCyr from countries where entityId in (select entityId from filterCountries where filterId = $filterId) and entityId in (select countryId from operatorCountry where operatorId = $operatorId)";
        return Yii::app()->db->createCommand($sql)->queryAll();

    }

    public function createNewFilter($name, $managerId, $vision = 1, $description = 'нет описания')
    {
        $filter = new Filter();
        $filter->name = $name;
        $filter->managerId = $managerId;
        $filter->vision = $vision;
        $filter->description = $description;
        return $this->saveFilter($filter);
    }
    public function updateFilter($id, $name, $managerId, $vision, $description)
    {
        $filter = Filter::model()->findByPk($id);
        $filter->name = $name;
        $filter->managerId = $managerId;
        $filter->vision = $vision;
        $filter->description = $description;
        return $this->saveFilter($filter);
    }
    public function deleteFilterById($id)
    {
        $filter = Filter::model()->findByPk($id);
        return $this->deleteFilter($filter);
    }
//endregion
//region unbindFilterFrom
    /**
     * отвязать все привязки к этому фильтру
     * @param $id
     * @return int
     */
    public function unbindFilterFromAll($id)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition('filterId',array($id));
        Yii::app()->db->createCommand()->delete('filterRegions',$criteria->condition, $criteria->params);
        Yii::app()->db->createCommand()->delete('filterOperators',$criteria->condition, $criteria->params);
        Yii::app()->db->createCommand()->delete('filterCountries',$criteria->condition, $criteria->params);
        Yii::app()->db->createCommand()->delete('filterHotels',$criteria->condition,$criteria->params);
    }
    /**
     * @param $filterId
     * @return int
     *
     */
    public function unbindFilterFromFreeRegions($filterId)
    {
        $regionCriteria = new CDbCriteria();
        $regionCriteria->addInCondition('filterId',array($filterId));
        $regionCriteria->addCondition("entityId in (select entityId from regions where countryId not in(select entityId from filterCountries where filterId = $filterId))",'AND');
        return Yii::app()->db->createCommand()->delete('filterRegions',$regionCriteria->condition, $regionCriteria->params);
    }
    public function unbindFilterFromFreeHotels($filterId)
    {
        $regionCriteria = new CDbCriteria();
        $regionCriteria->addInCondition('filterId',array($filterId));
        $regionCriteria->addCondition("entityId in (select entityId from hotels where countryId not in(select entityId from filterCountries where filterId = $filterId))",'AND');
        return Yii::app()->db->createCommand()->delete('filterHotels',$regionCriteria->condition, $regionCriteria->params);
    }
    /**
     * @param $filterId
     * @return int
     * удаление привязок стран, которые больше не поддерживаются операторами
     */
    public function unbindFilterFromFreeCountries($filterId)
    {
        $sql = "delete from filterCountries where entityId not in (select countryId from operatorCountry where operatorId in (select DISTINCT entityId from filterOperators where filterId=$filterId)) and filterId=$filterId";
        return Yii::app()->db->createCommand($sql)->execute();
    }
    /**
     * отвязать указанный курорт от указанного фильтра
     * @param $filterId
     * @param $regionId
     * @return int
     */
    public function unbindFilterFromRegion($filterId,$regionId)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition('filterId',array($filterId));
        $criteria->addInCondition('entityId',array($regionId));
        return Yii::app()->db->createCommand()->delete('filterRegions',$criteria->condition, $criteria->params);
    }
    /*
     * отвязать указанную страну от указанного фильтра
     * @param $filterId
     * @param $countryId
     * @return int
     */
    public function unbindFilterFromCountry($filterId, $countryId)
    {
        $countryCriteria = new CDbCriteria;
        $countryCriteria->addInCondition('filterId',array($filterId));
        $countryCriteria->addInCondition('entityId',array($countryId));
        return Yii::app()->db->createCommand()->delete('filterCountries',$countryCriteria->condition, $countryCriteria->params);
    }
    public function unbindFilterFromOperator($filterId, $operatorId)
    {
        $countryCriteria = new CDbCriteria;
        $countryCriteria->addInCondition('filterId',array($filterId));
        $countryCriteria->addInCondition('entityId',array($operatorId));
        return Yii::app()->db->createCommand()->delete('filterOperators',$countryCriteria->condition, $countryCriteria->params);
    }
    public function unbindFilterFromHotel($filterId,$hotelId)
    {
        $countryCriteria = new CDbCriteria;
        $countryCriteria->addInCondition('filterId',array($filterId));
        $countryCriteria->addInCondition('entityId',array($hotelId));
        return Yii::app()->db->createCommand()->delete('filterHotels',$countryCriteria->condition, $countryCriteria->params);
    }
//endregion
//region bindFilterTo
    public function bindFilterToHotel($filterId, $hotelId)
    {
        return Yii::app()->db->createCommand()->insert('filterHotels',array(
                'entityId'=>$hotelId,
                'filterId'=>$filterId,
            )
        );
    }

    public function bindFilterToCountry($filterId, $countryId)
    {
        return Yii::app()->db->createCommand()->insert('filterCountries',array(
                'entityId'=>$countryId,
                'filterId'=>$filterId,
            )
        );
    }

    /*
     * @param $filterId
     * @param $regionId
     * @return int
     */
    public function bindFilterToRegion($filterId, $regionId)
    {
        return Yii::app()->db->createCommand()->insert('filterRegions',array(
            'entityId'=>$regionId,
            'filterId'=>$filterId,
        ));
    }

    /**
     * @param $filterId
     * @param $operatorId
     * @return int
     */
    public function bindFilterToOperator($filterId, $operatorId)
    {
        return Yii::app()->db->createCommand()->insert('filterOperators',array(
            'entityId'=>$operatorId,
            'filterId'=>$filterId,
        ));
    }
//endregion
    //endregion
    //region searchTour
    public function getResults(CacheSearch $cacheSearch)
    {
        if ($cacheSearch->validate())
        {
            $result = Yii::app()->dm->api->getResults($cacheSearch);
            return $result;
        }
        else
        {
            throw new SearchTourException('поисковая форма не выдерживает критики');
        }
    }
    //endregion
    //region APIEntity

    public function getHotelsNameByRegionId($regionId)
    {
        $sql = "select entityId, concat(name, '  ',categoryDescription) as name from hotels where regionId = $regionId";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }
    public function getCountriesNameCyrByOperatorId($operatorId)
    {
        $sql = "select entityId, nameCyr from countries where entityId in (select countryId from operatorCountry where operatorId = $operatorId)";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }



    public function getCountryCodeByCountryKey($id)
    {
        $sql = 'select countryCode from countries where entityId='.$id;
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }
    public function getCountriesNameCyr()
    {
        $sql = 'select entityId, nameCyr from countries';
        $countries = Yii::app()->db->createCommand($sql)->queryAll();
        return $countries;
    }
    public function getRegionsNameCyrByCountryId($countryId)
    {
        $sql = 'select entityId, nameCyr from regions where countryId=\''.$countryId.'\'';
        $regions = Yii::app()->db->createCommand($sql)->queryAll();
        return $regions;
    }
    public function getHotelsNameRegionIdCatIdByCountryIdAndFilterId($countryId, $filterId)
    {
        $sql = 'select entityId, name, regionId, categoryId from hotels where countryId = '.$countryId.' and regionId in (select entityId from filterRegions where filterId = '.$filterId.')';
        $hotels = Yii::app()->db->createCommand($sql)->queryAll();
        return $hotels;
    }
    public function getCategoriesCatTextByCountryId($countryId)
    {
        $sql = 'select entityId, categoryText from categories where entityId in (select categoryId from countryCategory where countryId = '.$countryId.') ';
        $categories = Yii::app()->db->createCommand($sql)->queryAll();
        return $categories;
    }
    public function getMealCyrByCountryId($countryId)
    {
        $sql = 'select entityId, mealCyr from meals where entityId in (select mealId from countryMeal where countryId = '.$countryId.') ';
        $meals = Yii::app()->db->createCommand($sql)->queryAll();
        return $meals;
    }

    public function getHotelsNameByCountryId($countryId)
    {
        if($countryId)
        {
            $sql = "select entityId, name from hotels where countryId = $countryId";
            return Yii::app()->db->createCommand($sql)->queryAll();
        }
        else
        {
            CVarDumper::dump($countryId);
            return array();
        }

    }

    //endregion
}