<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
 */
    public $layout='//layouts/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

    public function renderAjax($view, $data = null,$return=false,$processOutput=true){
        //dont load jQuery framework stuff twice, it causes mucho problems!
        Yii::app()->clientscript->scriptMap['jquery.js'] = false;
//        Yii::app()->clientscript->scriptMap['jquery.yii.js'] = false;
        Yii::app()->clientscript->scriptMap['jquery-ui.min.js'] = false;
        Yii::app()->clientscript->scriptMap['jquery.yiigridview.js'] = false;
        Yii::app()->clientscript->scriptMap['jquery.ba-bbq.js'] = false;


        $this->renderPartial($view,$data,$return,$processOutput);
    }

    public function returnAjax($data)
    {
        if(is_array($data))
        {
            echo $this->myDump($data);
        }
        else
        {
            echo $data;
        }
    }

    private function myDump($data, $string = false)
    {
        $ret = '';
        if(is_array($data))
        {
            foreach($data as $id=>$value)
            {
                if(!$string)
                {
                    $ret.='['.$id.']'.$this->myDump($value, true).'<br>';
                }
                else
                {
                    $ret.='['.$id.']'.$this->myDump($value, true).';';
                }
            }
        }
        else
        {
            if ($string)
            {
                $ret .= '='.$data;
            }
            else
            {
                $ret = $data;
            }

        }
        return $ret;
    }


}