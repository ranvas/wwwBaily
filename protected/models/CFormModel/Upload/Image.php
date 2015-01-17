<?php
class Image extends Upload
{
    public function rules() {
        $rules=parent::rules();
        return CMap::mergeArray($rules,array(
            array('file', 'file', 'types'=>'jpg,gif,png'),
        ));
    }
}