<?

require_once 'js_packer.php';
require_once 'css_packer.php';


############################################################
# функция выдаёт файл php собранный из [inc] и [inc_js]
############################################################



function php_includer($file)
{
    global $already_included; # включен ли уже файл
    global $include0;
    //global $temp;
    
    global $sourcepath;
    
    
    // читаем файл
    $stringi = filebank($file);
    
    
    
    
    if (!$stringi)
    {
        echo '<br><br><b>Can not find file: ';
        echo $file;
        echo '</b>';
        return '';
    }

    $text = ''; // текст выходного файла
    
    
    $c = count($stringi);

    for ($i = 0; $i < $c; $i++)
    {
        //$string = $stringi[$i];

        $string = $stringi[$i];
        $tstr = trim($string);

        //if (!$string)
        //{
        //    continue;
        //}


//        //if ($string[0] == '#' && ($string[1] == '#' || $string[1] == ' '))
//        if ($string[0] == '#')
//        {
//            //echo "<br>Delete comment:<br>";
//            //echo $string;
//            //echo "<br>";
//            
//            continue;
//        }
//
//        if ($string[0] == '/' && $string[1] == '/')
//        {
//            continue;
//        }

        if (strtolower(substr($tstr, 0, 5)) == '[inc]')
        {
            $inc = substr($tstr, 5);
            
            //if ($already_included[$inc] == 1 && $temp[$inc] == 1)
            if ($already_included[$inc] == 1 && substr($tstr, 0, 5) != '[INC]')
            {
                if ($_GET['mess'] == 'all')
                {
                    echo '<br><br>';
                    echo "You want include file $inc in file $file several times";
                }

                continue;
            }
            
            $already_included[$inc] = 1;

            $text .= '?' . '>' . php_includer($inc) . '<' . '?';
            $include0 []= trim($inc); // добавим в список включаемых файлов

            continue;
        }

        if (substr($tstr, 0, 8) == '[inc_js]')
        {
            $str = '';
            
            for ($j = $i; substr(trim($stringi[$j]), 0, 8) == '[inc_js]'; $j++)
            {
                $str .= trim($stringi[$j]) . ',';
            }
            
            
            $str = str_replace('[inc_js]', '', $str);
            $str = str_replace(',,', ',', $str);
            $str = str_replace(' ', '', $str);
            $filelist = explode(',', $str);
            
            
            $text .= js_packer($filelist);
            $include0 = array_merge($include0, $filelist); // добавим в список включаемых файлов
            
            $i = $j;
            
            continue;
        }
        
        
        if (substr($tstr, 0, 9) == '[inc_css]')
        {
            $str = '';
            
            for($j = $i; substr(trim($stringi[$j]), 0, 9) == '[inc_css]'; $j++)
            {
                $str .= trim($stringi[$j]) . ',';
            }
            
            $str = str_replace('[inc_css]', '', $str);
            $str = str_replace(',,', ',', $str);
            $str = str_replace(' ', '', $str);
            $filelist = explode(',', $str);
            
            
            $text .= css_packer($filelist);
            $include0 = array_merge($include0, $filelist); // добавим в список включаемых файлов
            
            $i = $j;
            
            continue;
        }        



        $text .= $string;

    }

    return $text;

}

?>