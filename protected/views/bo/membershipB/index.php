<?php
if(Yii::app()->user->checkAccess('boAccounts'))
{
    echo CHtml::label("Пользователи", false);
    $this->renderPartial('accounts',compact('accounts'));
    echo '<BR>';
}
if(Yii::app()->user->checkAccess('boRoles'))
{
echo CHtml::label("Роли", false);
$this->renderPartial('roles',compact('roles'));
echo '<BR>';
}
?>



<!--<table id='operations'>-->
<!--    <tr>-->
<!--        <th>Название операции</th>-->
<!--    </tr>-->
<!---->
<!--    --><?php
//    foreach ($operations as $operation)
//    {
//        ?>
<!--        <tr>-->
<!--            <td>--><?php //echo $operation;?><!--</td>-->
<!--        </tr>-->
<!--    --><?php
//    }
//    ?>
<!--</table>-->
<!--<table id='tasks'>-->
<!--    <tr>-->
<!--        <th>Название задачи</th>-->
<!--    </tr>-->
<!---->
<!--    --><?php
//    foreach ($tasks as $task=>$bizrule)
//    {
//        ?>
<!--        <tr>-->
<!--            <td>--><?php //echo $task;?><!--</td>-->
<!--            <td>--><?php //echo $bizrule;?><!--</td>-->
<!--        </tr>-->
<!--    --><?php
//    }
//    ?>
<!--</table>-->


