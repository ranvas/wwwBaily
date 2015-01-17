<?php


class helpTest extends CDbTestCase{

    /**
     * действия перед началом всех тестов
     */
    public static function setUpBeforeClass()
    {
        $connection = Yii::app()->db;
        //удаление таблиц
        $dumpfile = '/srv/sql/help/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('db delete error');
        }
        //создание таблиц
        $dumpfile = '/srv/sql/help/create_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){

            self::markTestSkipped('ошибка создания БД');
        }


    }

    /**
     * действия после всех тестов
     */
    public static function tearDownAfterClass()
    {
        $connection = Yii::app()->db;
//        удаление таблиц
        $dumpfile = '/srv/sql/help/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('db delete error');
        }

    }

    /**
     * действия перед каждым тестом
     */
    protected function setUp(){


        parent::setUp();
    }

    /**
     * Тестировать наличия таблиц
     */
    public function testTables()
    {
        $this->assertNotNull(Yii::app()->db->schema->getTable('records'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('west'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('westEntity'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('recordEntity'));
    }

    /**
     * CRUD со статьями
     */
//    public function testCRUDRecord()
//    {
//        //создание сессии пользователя
//        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
//        Yii::app()->setComponent('session', $mockSession);
//        $identity=new UserIdentity('admin','admin');
//        Yii::app()->user->login($identity);
//        if($identity->authenticate()){
//            Yii::app()->user->login($identity);
//        }
//        //репозиторий
//        $help = Yii::app()->dm->help;
//        //создание статьи
//        $text = 'Это шок в 15 лет, это первый рок-концерт, это слэм и губы в кровь раз за разом вновь и вновь, это Хэтфилд и Мастейн, это Это Deftones и Mudvayne, это шаг в опасный путь и назад не повернуть';
//        $record = $help->createNewRecord($text);
//        $this->assertNotNull($record);
//        //изменение статьи
//        $text = 'Время X пришло';
//        $updated = $help->updateRecordById($record->id,$text);
//        $this->assertEquals($updated->text, 'Время X пришло');
//        //удаление статьи
//        $deleted = $help->deleteRecordById($updated->id);
//        $this->assertEquals(1, $deleted);
//        //получение сиска статей
//        $text = 'первый текст';
//        $help->createNewRecord($text);
//        $text = 'второй текст';
//        $help->createNewRecord($text);
//        $text = 'третий текст';
//        $help->createNewRecord($text);
//        $records = Record::model()->findAll();
//        $this->assertCount(3,$records);
//    }

    public function testCRUDWest()
    {
        //создание таблиц api(для создания привязок)
        $connection = Yii::app()->db;
        $dumpfile = '/srv/sql/api/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('db delete error');
        }
        $dumpfile = '/srv/sql/api/create_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){

            self::markTestSkipped('ошибка создания БД');
        }
        //репозиторий
        $help = Yii::app()->dm->help;
        //создание новой погодной станции(Ярославль)
        $id = '57.56066513,40.15736771';
        $res = $help->createNewWest($id,'test', 50);
        $this->assertTrue($res);
        $west = West::model()->findByPk($id);
        //изменение погодной станции(Чебоксары)
        $name = 'новое имя';
        $res = $help->updateWestNameById($west->id, $name);
        $this->assertTrue($res);
        $updated = West::model()->findByPk($id);
        //удаление погодной станции
        $res = $help->deleteWestById($updated->id);
        $this->assertTrue($res);

        //получение списка погодных станций
        $id = '57.56066513,40.15736771';
        $help->createNewWest($id,'test too',51);
        $id = '56.09000015,47.34833145';
        $res = $help->createNewWest($id,'and this test too', 52);
        $wests = West::model()->findAll();
        $this->assertCount(2,$wests);
        $west = West::model()->findByPk($id);


        $country = new Country();
        $country->attributes = array(
            'entityId' => 1,
            'name' => 'Russia',
            'nameCyr' => 'Россия',
            'countryCode' => 'RF',
            'currencyId' => '810',
            'date' => date('Y')
        );
        $country->save();
        //привязка погодной станции к стране
        $wtc = $help->bindWestToCountry(1,$west->id);
        $this->assertEquals(1, $wtc);

        //отвязка погодной станции от страны
        $wtc = $help->unbindWestFromCountry(1,$west->id);
        $this->assertEquals(1, $wtc);
        //удаление погодной станции с привязкой
        $help->bindWestToCountry(1,$west->id);
        $deleted = $help->deleteWestById($west->id);
        $this->assertEquals(1,$deleted);
        //удаление таблиц api
        $dumpfile = '/srv/sql/api/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('db delete error');
        }
    }

    public function testPhotos()
    {
        //создать модель
        $model = new Image();

        //имитировать загрузку файла
        $tempName = '/srv/temp/tempfile';
        chmod($tempName, 0666);
        copy("/srv/temp/original.png", $tempName);
        $upFileName = Yii::getPathOfAlias('application').'/../'.PATH_TO_STATIC.'/arrow_down.jpg';
        $type = 'image/jpeg';
        $file = new CUploadedFile($upFileName,$tempName, $type,4668, 0);
        $model->file = $file;
        //сохранение файла модели
        $ret = false;
        if ($model->validate())
        {
            copy($model->file->tempName, $model->file->name);
            //эмулировать загрузку файла на сервер не получилось, поэтому тестирование сохранения файла не покрывает код никак
//            Yii::app()->dm->help->saveFile($model->file->tempName, $model->file->name);
            $ret = file_exists($model->file->name);
        }
        $this->assertTrue($ret);
        $c = 0;
        $dirName = Yii::getPathOfAlias('application').'/../'.PATH_TO_STATIC;

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
        }
        $this->assertEquals($c,1);
        $ret = false;
        $ret = unlink($model->file->name);
        $this->assertTrue($ret);

    }

}

