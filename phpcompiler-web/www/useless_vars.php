<?

/*

Вы чищает код от объявления бесполезных постоянных типа
$rerert = "ssdfdsf";

и потом нигде не используется

//код должен быть предварительно вычищен откомментариев

*/


function useless_vars($text)
{
    global $file;
    
    
    $reg = '/\s(var )?\$([a-z0-9_]+)( )?=( )?((-)?[0-9\.\*\-\+\/\(\) ]+|"[^"]*"|\'[^\']*\'|array\([^\)]*\));/i';
    preg_match_all($reg, $text, $definitions, PREG_SET_ORDER);
    
    
    if (!$definitions)
    {
        return $text;
    }
    
    
    foreach ($definitions as $definition)
    {
        // подсчитаем, сколько раз встречается имя этой функции
        
        $var = $definition[2];
        
        preg_match_all('/(\$|\-\> ?' . $var . ')/i', $text, $matches);
        if (count($matches) == 1)
        {
            // переменная встречается 1 раз - исключаем её
            $text = str_replace($definition[0], '', $text);
            
            
            if ($_GET['mess'] == 'all')
            {
                echo '<br><br>';
                echo 'Var ' . $definition[0] . ' was deleted from ' . $file;
            }
            
        }
    }
    
    return $text;
}


?>