<?





function php_min ($php)
{

    // research all symbols

    $len = strlen($php);

    $type = 5;
    /*
        0 - commands
        1 - 'string'
        2 - "string"
        3 - simple comment (// or #)
        4 - big comment (/ * ... * /)
        5 - html (? > ... < ?)
    */


    // new text
    $text = '';


    for ($i = 0; $i < $len; $i++)
    {
        $symbol = $php[$i];

        if (ord($symbol) == 13)
        {
            continue; // microsoft must die
        }

        if ($type == 0)
        {
            // command

            if ($symbol == '#')
            {
                $type = 3;
                continue;
            }

            if ($symbol == '/')
            {
                if ($php[$i + 1] == '/')
                {
                    $type = 3;
                    continue;
                }
            }

            if ($symbol == '/')
            {
                if ($php[$i + 1] == '*')
                {
                    $type = 4;
                    continue;
                }
            }



            if (ord($symbol) == 10)
            {
                if (ord($text[strlen($text) - 1]) == 10)
                {
                    continue; // спаренный разрыв строки
                }
            }


            if ($symbol == ' ')
            {
                if (whitechar($text[strlen($text) - 1]) || whitechar($php[$i + 1]))
                {
                    // this ' ' can be removed
                    continue;
                }
            }


            if ($symbol == "'")
            {
                // 'string'
                $type = 1;
            }

            if ($symbol == '"')
            {
                // "string"
                $type = 2;
            }

            if ($symbol == '?')
            {
                if ($php[$i + 1] == '>')
                {
                    // html
                    $type = 5;
                }
            }

            $text .= $symbol;

            continue;

        }







        if ($type == 1 || $type == 2)
        {
            // 'string' or "string"


            if ($symbol == "\\")
            {
                $text .= "\\" . $php[$i + 1];
                $i += 1;

                continue;
            }

            if ($symbol == "'")
            {
                if ($type == 1)
                {
                    $type = 0;
                }
            }

            if ($symbol == '"')
            {
                if ($type == 2)
                {
                    $type = 0;
                }
            }

            $text .= $symbol;

            continue;

        }




        if ($type == 3)
        {
            // '//' or '#'

            if ($symbol == "\n")
            {
                $type = 0;
            }

            continue;
        }



        if ($type == 4)
        {
            // /* ... */

            if ($symbol == "*")
            {
                if ($php[$i + 1] == '/')
                {
                    $type = 0;
                    $i += 1;
                }
            }

            continue;
        }


        if ($type == 5)
        {
            // ? > ... < ?

            if ($symbol == "<")
            {
                if ($php[$i + 1] == '?')
                {
                    $type = 0;
                }
            }

            $text .= $symbol;

            continue;
        }



    }



    return $text;

}












// is it letter like    =;-,.!()[]{}|&
function whitechar ($char)
{

    //if ($char == ' ')
    //{
    //    return 1;
    //}

    if (preg_match('|^[\s=;\-\,\.\!\(\)\[\]\{\}\|\&]$|i', $char))
    {
        return 1;
    }

    return 0;
}









?>
