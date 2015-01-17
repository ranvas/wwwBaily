<?php
/**
 * Class Upload
 * @property string $file name of file in tmp directory
 */
abstract class Upload extends CFormModel
{
    protected $_file;

    public function getFile()
    {
        return $this->_file;
    }




    /**
     * @param $file
     * поддерживаемые варианты формата файла:
     * CUploadedFile::getInstance($model,'file');
     * и
     * foreach(CUploadedFile::getInstances($model,'file'))
     */
    public function setFile($file)
    {
        $this->_file = $file;
    }

    public function rules()
    {
        return array(
            //            array('file', 'file', 'maxFiles'=>'2'),<------- этот параметр не включать, при одиночной загрузке yii глючит

        );
    }
}