<?php

class widgetException extends CException {}

/**
 * Статичные действия для HTML разметки
 * Class widgetException
 */
class MyWidgetHTML
{

    /**
     * @var string the CSS class for displaying error summaries (see {@link errorSummary}).
     */
//    public static $errorSummaryCss='errorSummary';
    /**
     * @var string the CSS class for displaying error messages (see {@link error}).
     */
//    public static $errorMessageCss='errorMessage';
    /**
     * @var string the CSS class for highlighting error inputs. Form inputs will be appended
     * with this CSS class if they have input errors.
     */
    public static $errorCss='error';

    /**
     * @param $tag
     * @param array $htmlOptions аттрибуты открывающего тэга
     * @param mixed $content содержимое между открывающим тэгом и закрывающим
     * @param bool $closeTag есть ли закрывающий тэг
     * @return string верстка
     * вернуть html тэги с содержимым
     */
    public static function tag($tag,$htmlOptions=array(),$content=false,$closeTag=true)
    {
        $attributes = '';
        foreach($htmlOptions as $name=>$value)
        {
            if($value!==null)
            {
                $attributes .= ' ' . $name . '="' . self::encode($value) . '"';
            }
        }
        $html = '<' . $tag . $attributes;
        if($content===false)
        {
            return $closeTag  ? $html.' />' : $html.'>';
        }
        else
        {
            return $closeTag ? $html.'>'.$content.'</'.$tag.'>' : $html.'>'.$content;
        }
    }


    public static function resolveModelName($model, $attribute, $prefix='')
    {
        //генерация name виджета
        if(is_object($model))
        {
            $className = get_class($model);
        }
        else
        {
            throw new widgetException('Ошибка идентификации модели при генерации виджета');
        }
        return $prefix.trim(str_replace('\\','_',$className),'_').'['.$attribute.']';
    }

    public static function resolveIdByName($modelName)
    {
        return str_replace(array('[]','][','[',']',' '),array('','_','_','','_'), $modelName);
    }






    /**
     * Encodes special characters into HTML entities.
     * The {@link CApplication::charset application charset} will be used for encoding.
     * @param string $text data to be encoded
     * @return string the encoded data
     * @see http://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode($text)
    {
        return htmlspecialchars($text,ENT_QUOTES,Yii::app()->charset);
    }




    /**
     * @param $name аттрибут name
     * @param $value аттрибут value
     * @param $id аттрибут id
     * @return string html разметка
     */
    public static function hiddenField($name, $value, $id)
    {
        $htmlOptions['type'] = 'hidden';
        $htmlOptions['value'] = $value;
        $htmlOptions['name'] = $name;
        $htmlOptions['id'] = $id;
        return self::tag('input',$htmlOptions);
    }

    public static function checkBox($name, $checked=false, $htmlOptions=array())
    {
        if($checked)
        {
            $htmlOptions['checked']='checked';
        }
        else
        {
            unset($htmlOptions['checked']);
        }
        $htmlOptions['name'] = $name;
        $htmlOptions['type'] = 'checkbox';
        return self::tag('input',$htmlOptions);
    }

    public static function label($label,$for = false,$htmlOptions=array())
    {
        if($for)
        {
            $htmlOptions['for']=$for;
        }
        else
        {
            unset($htmlOptions['for']);
        }
        return self::tag('label',$htmlOptions,$label);
    }


}