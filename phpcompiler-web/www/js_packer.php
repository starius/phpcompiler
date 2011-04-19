<?


require_once 'class.JavaScriptPacker.php';
//require_once 'func_cleaner.php';


############################################################
# функция возвращает запакованный js-код из многих файлов
############################################################

function js_packer($filelist)
{

    //if (gettype($filelist) == 'string')
    //{
    //    $filelist = trim($filelist);
    //    $filelist = str_replace('[inc_js]', '', $filelist);
    //    $filelist = explode(',', $filelist);
    //}
    
    
    global $sourcepath;
    
    
    $js = '';

    foreach($filelist as $file)
    {

        $file = trim($file);

        if (!$file)
        {
            continue;
        }



        if (strpos($file, 'txt') || strpos($file, 'txt2js'))
        {
            // функция, выводящая этот текст

            $name = explode('.', $file);
            $name = $name[0];


            $name = explode('/', $name);
            $name = $name[count($name) - 1];

            $stringi = filebank($file);

            if (!$stringi)
            {
                echo '<br><br><b>Can not find file: ';
                echo $file;
                echo '</b>';
            
                continue;
            }
            
            $bank = array();
            
            foreach ($stringi as $string)
            {
                $string = trim($string);
                
                if ($string)
                {
                    $bank[] = $string . ' ';
                }
                
            }
            
            $ttt = implode($bank);
            
            $ttt = str_replace('\\', "\\\\", $ttt);
            $ttt = str_replace('"', "\\\"", $ttt);
            
            $ttt = str_replace('> <', "><", $ttt);
            
            
            if (strpos($file, 'txt2js'))
            {
                $text = 'function ' . $name . ' (){ return "' . $ttt . '";}';
            }
            else
            {
                $text = 'function ' . $name . ' (){mp_set("' . $ttt . '");}';
            }
        }
        else
        {
            // просто файл
            
            $text = @implode(filebank($file));
            
            
            if (!$text)
            {
                echo '<br><br><b>Can not find file: ';
                echo $file;
                echo '</b>';
                
                continue;
            }
        }
        
        
        $js .= "\n\n";
        
        $js .= $text;
        
        $js .= "\n\n";


    }


////
////    /////////////////////////////////////////////////////
////    // удалим все неиспользованные собственные функции
////    /////////////////////////////////////////////////////
////    
////    
////    if ($_GET['func'] == 'yes')
////    {
////        $deleted_func = array();
////        
////        $js = func_cleaner($js);
////        
////        if ($_GET['mess'] == 'all' && $deleted_func)
////        {
////            $deleted_func = implode($deleted_func, ', ');
////            
////            echo '<br><br>';
////            echo "Javascript function(s) $deleted_func were deleted";
////        }
////    }
////    








    #####################

    if ($_GET['pack'] == 'yes')
    {
        
        // закодируем числами каждый символ
        
        $n = 100222;
        
        $symbols = array(); // индекс - символ, значение - код-число
        
        $js_1 = ''; // новая строка



        $l = strlen($js);
        
        for ($i = 0; $i < $l; $i++)
        {
            $s = $js[$i];
            
            if (ord($s) < 128)
            {
                // нормальные символы
                $js_1 .= $s;
            }
            else
            {
                // национальные языки
                
                if (!$symbols[$s])
                {
                    $n += 1;
                    $symbols[$s] = 'u' . $n . 'u';
                }
                
                $js_1 .= $symbols[$s];
            }
        }
        
        
        
        // запускаем кодировщик

        $encoding = 62;
        $fast_decode = true;
        $special_char = false;

        $packer = new JavaScriptPacker($js_1, $encoding, $fast_decode, $special_char);

        $js_1 = $packer->pack();


        $js_1 = trim($js_1);
        $js_1 .= ';';
        
        
        // раскодиуем эту вещь
        
        $js = $js_1;
        
        foreach ($symbols as $s => $code)
        {
            $js = str_replace($code, $s, $js);
        }
        

    }

    ################



    return $js;
}

?>