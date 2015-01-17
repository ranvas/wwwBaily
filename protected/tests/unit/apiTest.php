<?php

class apiTest extends CDbTestCase{
    /**
     * действия перед началом всех тестов
     */
    public static function setUpBeforeClass(){
        $connection = Yii::app()->db;

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
        //создание таблиц
        $dumpfile = '/srv/sql/api/update_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection)){

            self::markTestSkipped('ошибка создания БД');
        }

    }

    /**
     * действия после всех тестов
     */
    public static function tearDownAfterClass(){
//        $connection = Yii::app()->db;
////        удаление таблиц
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
        $this->assertNotNull(Yii::app()->db->schema->getTable('functions'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('functionInOrder'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('functionOutOrder'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('countries'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('currencies'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('categories'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('countryCategory'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('meals'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('countryMeal'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('regions'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('hotels'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('history'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('childRegions'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('operators'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('operatorCountry'));
    }

    /**
     *
     */
    public function testUpdate()
    {
        $api = Yii::app()->dm->api;
        $count = $api->updateFunctions();

        $this->assertGreaterThan(7,$count);
        //валюта
        $count = $api->updateCurrencies();
        $this->assertGreaterThan(0,$count);
        $sql = 'select * from currencies where date = \'\'';
        $currencyDate = Yii::app()->db->createCommand($sql)->queryAll();
        $this->assertCount(0,$currencyDate);

        //страны
        $count = $api->updateCountries();
        $this->assertGreaterThan(0,$count);

        //питание
        $count = $api->updateMeals();
        $this->assertGreaterThan(0,$count);

        //категории
        $count = $api->updateCategories();
        $this->assertGreaterThan(0,$count);

        //курорты
        $count = $api->updateRegions();
        $this->assertGreaterThan(0,$count);


        //отели по одной стране
        $count = $api->updateHotelsByCountryId(1);
        $this->assertGreaterThan(0, $count);
        $api->refactoringHotelsAndRegions();
        //все отели
//        $count = $api->updateHotels();
//        $this->assertGreaterThan(0,$count);

        //операторы
        $count = $api->updateOperators();
        $this->assertGreaterThan(0,$count);
    }







}
