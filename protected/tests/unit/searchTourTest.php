<?php
class searchTourTest extends CDbTestCase{
    /**
     * действия перед началом всех тестов
     */
    public static function setUpBeforeClass(){
        $connection = Yii::app()->db;

        //удаление таблиц
        $dumpfile = '/srv/sql/search/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('db delete error');
        }
        //создание таблиц
        $dumpfile = '/srv/sql/search/create_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){

            self::markTestSkipped('ошибка создания БД');
        }
        $dumpfile = '/srv/sql/search/update_tables.sql';
        //обновление таблиц
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('ошибка создания БД');
        }

        //удаление таблиц
        $dumpfile = '/srv/sql/api/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('db delete error');
        }
        //создание таблиц
        $dumpfile = '/srv/sql/api/create_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){
            self::markTestSkipped('ошибка создания БД');
        }


    }

    /**
     * действия после всех тестов
     */
    public static function tearDownAfterClass(){
//        $connection = Yii::app()->db;
////        удаление таблиц searchTour
//        $dumpfile = '/srv/sql/search/delete_tables.sql';
//        if(!functions::executeSQLFile($dumpfile, $connection)){
//            self::markTestSkipped('db delete error');
//        }
////        удаление таблиц api
//        $dumpfile = '/srv/sql/api/delete_tables.sql';
//        if(!functions::executeSQLFile($dumpfile, $connection)){
//            self::markTestSkipped('db delete error');
//        }
    }

    /**
     * действия перед каждым тестом
     */
    protected function setUp(){

        parent::setUp();
    }

    /**
     * тестировать наличие таблиц
     */
    public function testTables()
    {
        $this->assertNotNull(Yii::app()->db->schema->getTable('cacheTours'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('cacheSearch'));

        $this->assertNotNull(Yii::app()->db->schema->getTable('filters'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('filterEntity'));
    }

    /**
     * тестирование работы с фильтрами
     */
    public function testFilterCRUD()
    {
        //создание сессии пользователя
        $mockSession = $this->getMock('CHttpSession', array('regenerateID'));
        Yii::app()->setComponent('session', $mockSession);
        $identity=new UserIdentity('admin','admin');
        Yii::app()->user->login($identity);
        if($identity->authenticate()){
            Yii::app()->user->login($identity);
        }
        //получение API сущностей
        Yii::app()->dm->api->updateFunctions();
        Yii::app()->dm->api->updateCurrencies();
        Yii::app()->dm->api->updateCountries();
        Yii::app()->dm->api->updateRegions();
        //привязка стран и курортов к основному фильтру
        $search = Yii::app()->dm->searchTour;
        $count = $search->bindFilterToAll(1);
        $this->assertGreaterThan(24,$count);
        //создание нового фильтра
        $name = 'extension filter';
        $filter = $search->createNewFilter($name);
        $this->assertInstanceOf('Filter',$filter);
        //правка фильтра
        $filter->description = 'accessed filter';
        $filter->ready = 1;
        $updated = $search->updateFilter($filter);

        $this->assertInstanceOf('Filter',$updated);
        $this->assertEquals(1,$updated->ready);
        $this->assertEquals('accessed filter',$updated->description);
        //привязка страны к фильтру
        $raw = $search->bindFilterToCountry($updated->id, 1);
        $this->assertEquals($raw, 1);
        //привязка курорта к фильтру
        $raw = $search->bindFilterToRegion($updated->id, 16777217);
        $this->assertEquals($raw, 1);
        //отвязка страны от фильтра
        $raw = $search->unbindFilterFromCountry($updated->id, 1);
        $this->assertEquals($raw, 2);
        //удаление фильтра
        $deleted = $search->deleteFilterById(2);
        $this->assertInstanceOf('Filter',$deleted);
        $this->assertNull(Filter::model()->findByPk(2));
    }



    public function testSearch()
    {
        $searchTour = Yii::app()->dm->searchTour;
        $api = Yii::app()->dm->api;
        //обновление сигнатур
        $api->updateFunctions('getResults');
        //имитация заполнения формы
        $search = new CacheSearch;
        $search->countryKey = '1';
        $search->destQryStr = '2';
        $search->bDate1 = '09.15.14';
        $search->bDate2 = '09.15.14';
        $search->durationMin = '7';
        $search->durationMax = '7';
        $search->adQty = '2';
        $search->chQty = '0';
        $search->fstChd = '0';
        $search->sndChd = '0';
        $search->mealQryStr = '';
        $search->catQryStr = '';
        $search->priceMin = '';
        $search->priceMax = '';
        $search->hotelQryStr = '11058';
        $search->operQryStr = '34';
        $search->airCharge = '0';
        //если модель проходит проверку, то все плохо
        if($search->validate())
        {
            $this->assertTrue(false);
        }
        //а теперь модель должна проходить проверку
        $search->userId = 'isGuest';
        if($search->validate())
        {
            $responseString = $searchTour->getResults($search);
            $this->assertNotNull($responseString);
        }
        else
        {
            $this->assertTrue(false);
        }
        //запрос сигнатуры поиска у API
        //имитация выбора тура
//        $requestString =  "`TR`|`2`|```09.01.14```|```09.01.14```|`7`|`7`|`2`|`0`|`0`|`0`|``|``|``|``|`11058`|`34`|`0`|`price`|`0`|`1`|`15`";
//        $tour = new cacheTour;
//        проверка тура у API
//        $responseString = $tour->getTour($requestString);
//        $this->assertNotNul($responseString);





    }



}