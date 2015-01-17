<?php
$this->breadcrumbs=array(

);

$this->widget('searchTourTabs', array(
    'model'=>$model,
));
?>

<br>

<div id = "result">

</div>

<?php
$this->widget('ImperaviRedactorWidget', array(
    // or just for input field
    'name' => 'Post_content',
    'options'=>array(
        'buttons'=>array(
            'formatting', '|', 'bold', 'italic', 'deleted', '|',
            'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
            'link', '|', 'html',
        ),
        'minHeight'=>200,
        'lang'=>'ru',
        'fullPage' => true,
    ),
));

?>
