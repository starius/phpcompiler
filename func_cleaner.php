<?

################################################################
# удаляет из программного кода неиспользуемые функции и классы
################################################################


function func_cleaner ($text)
{

    if (substr_count($text, '{') != substr_count($text, '}'))
    {
        return $text;
    }

    // убедились, что число "{" = число "}" по всему тексту

    // составим список функций


    while (true)
    {
        $text1 = func_cleaner1($text);

        if ($text1 == $text)
        {
            break;
        }

        $text = $text1;
    }


    return $text;

}







function func_cleaner1 ($text)
{
    global $deleted_func;

    preg_match_all("/(function|class) ([a-z0-9_]+)/i", $text, $funcs, PREG_PATTERN_ORDER);

    $funcs = $funcs[2];

    if (!$funcs)
    {
        return $text;
    }


    foreach ($funcs as $func)
    {
        //$func = str_replace('function ', '', $func);

        // попробуем вырезать эту функцию

        // номер, соответствующий букве "f"
        $p = strpos($text, 'function ' . $func);

        if (!$p)
        {
            $p = strpos($text, 'class ' . $func);
        }

        if (!$p)
        {
            // странно, не могу найти эту функцию
            continue;
        }

        $text1 = substr($text, 0, $p); // часть строки до функции

        // найдём конец функции

        $brackets = 0; // число "{" - число "}"
        $ok = 0; // побывал ли $brackets > 0

        $l = strlen($text);

        for (; $p < $l; $p++)
        {

            if ($ok && $brackets == 0)
            {
                break;
            }


            if ($text[$p] == '{')
            {
                $ok = 1;
                $brackets++;
            }

            if ($text[$p] == '}')
            {
                $brackets--;
            }

        }

        $text1 .= substr($text, $p);

        // подсчитаем, сколько раз встречается имя этой функции
        preg_match_all("/" . $func . "(\s)*\(/i", $text1, $calls, PREG_PATTERN_ORDER);
        $calls = $calls[0];
        if (count($calls))
        {
            // Эту функцию где-то вызывают
            continue;
        }


        // похоже, имя функции встречается только в её заголовке или теле

        if ($ok)
        {
            $deleted_func []= $func;

            $text = $text1;
        }


    }


    return $text;

}









?>
