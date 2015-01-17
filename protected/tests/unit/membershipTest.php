<?php
//require_once('/usr/share/php5/yii/yiit.php');

class membershipTest extends CDbTestCase{

    /**
     * действия перед началом всех тестов
     */
    public static function setUpBeforeClass(){
        $connection = Yii::app()->db;
        //удаление таблиц
        $dumpfile = '/srv/sql/membership/delete_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection))
        {
            self::markTestSkipped('db delete error');
        }
        //создание таблиц
        $dumpfile = '/srv/sql/membership/create_tables.sql';
        if(!functions::executeSQLFile($dumpfile, $connection))
        {
            self::markTestSkipped('ошибка создания БД');
        }
        $dumpfile = '/srv/sql/membership/update_tables.sql';
        //Заполнение таблиц
        if(!functions::executeSQLFile($dumpfile, $connection))
        {
            self::markTestSkipped('ошибка создания БД');
        }
    }

    /**
     * действия после всех тестов
     */
    public static function tearDownAfterClass()
    {
//        $connection = Yii::app()->db;
////        удаление таблиц
//        $dumpfile = '/srv/sql/membership/delete_tables.sql';
//        if(!functions::executeSQLFile($dumpfile, $connection)){
//            self::markTestSkipped('db delete error');
//        }
    }

    /**
     * действия перед каждым тестом
     */
    protected function setUp()
    {

        parent::setUp();
    }
    /**
     * тестировать наличие нужных табличек,
     * а также розовых пони
     * Assertions: 6
     */
    public function testTables()
    {
        //тестируем наличие нужных таблиц
        $this->assertNotNull(Yii::app()->db->schema->getTable('Account'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('AuthItem'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('AuthItemChild'));
        $this->assertNotNull(Yii::app()->db->schema->getTable('AuthAssignment'));
        //проверка фикстур
        $users = Yii::app()->dm->membership->getAllUsers();
        $this->assertCount(3,$users);
    }

    /**
     * тест на пользователя
     * Assertions: 9
     */
    public function testUsers(){
        $user = new Account();
        //Проверка валидации неверных значений
        $user->username = '';
        $this->assertFalse($user->validate(array('username')));
        $user->username = functions::generateString(3);
        $this->assertFalse($user->validate(array('username')));
        $user->username = functions::generateString(151);
        $this->assertFalse($user->validate(array('username')));
        //проверка создания нового администратора
        $user->setAttributes(array(
            'username'=>functions::generateString(150),
            'password'=>'123456',
            'email'=>'zopa@pizda.ru',
            'status'=>1,
            'role'=>'admin'
        ));
        $id = Yii::app()->dm->membership->createNewAccount($user);
        $this->assertEquals($id, 4);
        $users = Yii::app()->dm->membership->getAllUsers();
        $this->assertCount(4, $users);
        //обнуляем пользователя
        $user = new Account();
        //проверка создания нового пользователя
        $user->setAttributes(array(
            'username'=>'random',
            'password'=>'123456',
            'email'=>'zopa@pizda.com',
            'status'=>1,
            'role'=>'user'
        ));
        $id = Yii::app()->dm->membership->createNewAccount($user);
        $this->assertEquals($id, 5);
        $users = Yii::app()->dm->membership->getAllUsers();
        $this->assertCount(5,$users);

        //тест на чтение
        $users = Yii::app()->dm->membership->getAllUsers();
        for($i=0;$i<3;$i++)
        {
            $this->assertEquals('ranvas@baily.ru',$users[$i]->email);
        }
        $user = Yii::app()->dm->membership->getAccountById(5);
        $this->assertEquals('random',$user->username);
        //тест на удаление
        $user = Yii::app()->dm->membership->deleteUserById(5);
        $this->assertNotNull($user);
        $user = Yii::app()->dm->membership->deleteAdminById(4);
        $this->assertNotNull($user);
        $users = Yii::app()->dm->membership->getAllUsers();
        $this->assertCount(3,$users);
    }


    /**
     * тестирование работы с ролями
     */
    public function testRoles()
    {
        //создание роли
        $role = Yii::app()->dm->membership->createNewRole('testmanager','будет хуярить');
        $this->assertNotNull($role);
        //назначение пользователю роли
        $acc = new Account();
        $acc->setAttributes(array(
            'username'=>'random',
            'password'=>'123456',
            'email'=>'zopa@pizda.com',
            'status'=>1,
            'role'=>'user'
        ));
        $id = Yii::app()->dm->membership->createNewAccount($acc);
        $this->assertEquals($id, 6);
        $user = Yii::app()->dm->membership->getAccountByNameWhereStatusTrue('random');
        $this->assertInstanceOf('Account',$user);
        $user->role = 'testmanager';
        $user =  Yii::app()->dm->membership->updateAccount($user);
        $this->assertEquals($user->role,'testmanager');
        $user =  Yii::app()->dm->membership->getAccountByNameWhereStatusTrue('random');
        $this->assertEquals($user->role,'testmanager');
        Yii::app()->dm->membership->createNewRole('createRole','test');
        Yii::app()->dm->membership->addAuthChild('testmanager', 'createRole');
        //удаляем пользователя
        $user = Yii::app()->dm->membership->getAccountByNameWhereStatusTrue('random');
        $user =  Yii::app()->dm->membership->deleteAccount($user);
        $this->assertTrue($user);
        //удаляем роль manager
        $role = Yii::app()->dm->membership->deleteRole('testmanager');
        $this->assertTrue($role);
    }


















}