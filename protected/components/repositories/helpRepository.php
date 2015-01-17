<?php
class helpException extends CException {}

class helpRepository extends CComponent
{

    //region private


    private function saveRecord(Record $record)
    {
        $record->redactorId = Yii::app()->user->id;
        $record->updateDate = date("D M d, Y G:i");
        if ($record->save())
        {
            return $record;
        }
        else
        {
            throw new helpException('Ошибка при сохранении record');
        }
    }


    private function saveWest(West $west)
    {
        $west->lastUpdate = time();
        if ($west->save())
        {
            return true;
        }
        else
        {
            throw new helpException('Ошибка при сохранении west');
        }
    }

    /**
     * @param $entity
     * @param $entityId
     * @param $westId
     * @return int
     */
    private function createWestEntity($entity, $entityId, $westId)
    {

        return Yii::app()->db->createCommand()->insert('westEntity', array('entityId'=>$entityId, 'entityClass'=>$entity, 'westId'=>$westId));
    }


    /**
     * @param CDbCriteria $criteria
     * @return int
     */
    private function deleteWestEntity(CDbCriteria $criteria)
    {
        return Yii::app()->db->createCommand()->delete('westEntity',$criteria->condition, $criteria->params);
    }


    //endregion

    //region record


    /**
     * Создать новую статью
     * @param string $text Текст статьи
     * @return Record|null
     */
    public function createNewRecord($text)
    {
        $record = new Record();
        $record->authorId = Yii::app()->user->id;
        $record->createDate = date("D M d, Y G:i");
        $record->text = $text;
        return $this->saveRecord($record);
    }

    /**
     * обновить статью зная id
     * @param $id
     * @param $text
     * @return null|Record
     */
    public function updateRecordById($id, $text)
    {
        $record = Record::model()->findByPk($id);
        $record->text = $text;
        return $this->saveRecord($record);
    }

    /**
     * @param $id
     * @return null|Record
     */
    public function deleteRecordById($id)
    {
        return Yii::app()->db->createCommand()->delete('records','id='.$id);
    }




    //endregion

    //region west

    /**
     * привязать погодную станцию к стране
     * @param $countryId
     * @param $westId
     * @return int
     */
    public function bindWestToCountry($countryId, $westId)
    {
        //создать привязку погодной станции
        return $this->createWestEntity('Country', $countryId, $westId);
    }


    public function bindWestToRegion($regionId, $westId)
    {
        //создать привязку погодной станции
        return $this->createWestEntity('Region', $regionId, $westId);
    }

    /**
     * @param $countryId
     * @param $westId
     * @return int
     */
    public function unbindWestFromCountry($countryId, $westId)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition('westId', array($westId));
        $criteria->addInCondition('entityClass',array('Country'));
        $criteria->addInCondition('entityId', array($countryId));
        //удалить привязку погодной станции к стране
        return $this->deleteWestEntity($criteria);
    }

    public function unbindWestFromRegion($regionId, $westId)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition('westId', array($westId));
        $criteria->addInCondition('entityClass',array('Region'));
        $criteria->addInCondition('entityId', array($regionId));
        //удалить привязку погодной станции к курорту
        return $this->deleteWestEntity($criteria);
    }

    /**
     * @param $westId
     * @return int
     */
    public function unbindWestFromAll($westId)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition('westId', array($westId));
        //создать привязку погодной станции
        $delete =  $this->deleteWestEntity($criteria);
        if ($delete > 0)
        {
            return $delete;
        }
        else
        {
            return true;
        }
    }

    public function getCountriesIdByWestId($westId)
    {
        $sql = 'select entityId from westEntity where westId = \''.$westId.'\' and entityClass = \'Country\'';
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }

    public function getRegionsIdByWestId($westId)
    {
        $sql = 'select entityId from westEntity where westId = \''.$westId.'\' and entityClass = \'Region\'';
        return Yii::app()->db->createCommand($sql)->queryColumn();
    }


    /**
     * @param $id
     * @param $name
     * @param $count текущий счетчик обновлений за день
     * @return bool
     */
    public function createNewWest($id, $name, $count)
    {
        $west = new West();
        $west->id = $id;
        $west->name = $name;
        $west->count = $count;
        return $this->saveWest($west);
    }

    /**
     * @param $id
     * @param $name
     * @return bool
     */
    public function updateWestNameById($id, $name)
    {
        $west = West::model()->findByPk($id);
        $west->name = $name;
        return $this->saveWest($west);
    }

    public function updateWestDescriptionById($id, $description)
    {
        $west = West::model()->findByPk($id);
        $west->description= $description;
        return $this->saveWest($west);

    }

    public function updateWest(West $west)
    {
        return $this->saveWest($west);
    }



    /**
     * @param $id
     * @return bool
     * @throws helpException
     */
    public function deleteWestById($id)
    {
        $delete = Yii::app()->db->createCommand()->delete('west','id=:id',array(':id'=>$id));
        if(($delete > 0)&&($delete < 2))
        {
            return true;
        }
        else
        {
            throw new helpException("ошибка удаления погодной станции");
        }
    }



    //endregion

    //region photos ВНИМАНИЕ!!! Блок не покрыт тестами



    public function saveFile($tmp, $name)
    {
//        $upFileName = Yii::getPathOfAlias('application').'/../'.PATH_TO_STATIC.'/'.$name;
        $upFileName = PATH_TO_STATIC.'/'.$name;
        if(move_uploaded_file($tmp, $upFileName))
        {
            return $upFileName;
        }
        $error = sprintf('Не получилось сохранить файл %s.',$name);
        throw new helpException($error);
    }

    public function deleteFile($name)
    {
        $fileName = PATH_TO_STATIC.'/'.$name;
        if(unlink($fileName))
        {
            return $fileName;
        }
        $error = sprintf('Не получилось удалить файл %s.',$name);
        throw new helpException($error);
    }

    public function getFilesCount($dir)
    {
        $c = 0;
        $dirName = PATH_TO_STATIC.'/'.$dir;
        if(is_dir($dirName))
        {
            $dh = opendir($dirName);
            while(($file = readdir($dh)) !== false )
            {
                if (is_file($dirName.'/'.$file))
                {
                    $c++;
                }
            }
            closedir($dh);
            return $c;
        }
        else
        {
            $error = sprintf('Не получилось считать директорию %s.',$dir);
            throw new helpException($error);
        }
    }

    //endregion

}