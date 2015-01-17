<table>
<?php
foreach ($modules as $module => $tables)
{
    //модули
    ?>
    <tr>
        <th><?php echo sprintf('Таблицы в модуле %s:',$module);?></th>
        <th>Select:</th>
        <th>Where:</th>
        <th>Показать:</th>

        <th><?php
            echo CHtml::ajaxButton('truncate',
                Yii::app()->createUrl(sprintf('back/sql/TruncateTables/%s/',$module)),
                array(
                    'type'      => 'GET',
                    'update'    => '#resultSQL',
                ),
                array(
                    'confirm'   => 'Все данные всех таблиц модуля будут потеряны!!!'
                )
            );
            ?></th>
    </tr>
    <?php
    //таблицы модуля
    foreach ($tables as $table)
    {
        ?>
    <tr>
    <td>
        <?php echo $table; ?>
    </td>
    <td>
        <?php echo CHtml::textField('select_'.$table,'');?>
    </td>
    <td>
        <?php echo CHtml::textField('where_'.$table,'');?>
    </td>
    <td>
        <?php echo CHtml::button('+',
            array(
                'onclick'=>'if (this.value == "+")
                                {
                                    this.value = "-";
                                    $("#show_'.$table.'").show(
                                        function()
                                        {
                                            var Select = $("#select_'.$table.'").val();
                                            var Where = $("#where_'.$table.'").val();
                                            $(this).load("/back/sql/showTable/",
                                                {select: Select, from: "'.$table.'",where: Where}
                                            );
                                        });
                                    }
                                    else
                                    {
                                        this.value = "+";
                                        $("#show_'.$table.'").hide(function(){this.innerHTML = ""});
                                    }'
            )
        ); ?>
        <div id="<?php echo 'show_'.$table?>" style="display: none;"></div>
    </td>

    </tr>
    <?php
    }
}
?>
</table>
<div id='resultSQL'></div>






