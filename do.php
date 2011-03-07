#!/usr/bin/php
<?php

umask(0);


// засечем время
$t0 = microtime(true);

/*
    как включать другие файлы:
        [inc] - включить файл php, если ещё не включали
        [INC] - включить файл PHP, даже если уже включали
        [inc_js] - включить код js в это место
        [inc_css] - включить код css в это место


    Все пути указывать от директори www, то есть:
        index.php
        reg/reg1.php
*/


require_once 'files_and_dirs.php';
require_once 'php_includer.php';
require_once 'is_it_new.php';

require_once 'php_min.php';
require_once 'func_cleaner.php';
require_once 'useless_vars.php';





///////////////////////////
// необходимые переменные
///////////////////////////

//$created_dirs = array(); # создавали ли такую папку

$to_compile = array(); # кого запустить и записать результат
$to_delete = array(); # будут удалены в самом конце


$second_iter = array(); # список файлов для второго обхода
//$real = array(); # является ли файл основным


$include = array(); // список включенных файлов

$modified = array(); // время последнего изменения файла


$wwwpath = 'www/';
$sourcepath = 'source/';
$phpcompilerpath = $sourcepath . '.phpcompiler/';


//if (!@file_exists($phpcompilerpath . 'filelist.txt'))
//{
//    echo "Bad project! No filelist.txt";
//    exit();
//}

//$filelist = file($phpcompilerpath . 'filelist.txt');



#############################
#  очистим папку от старья
#############################


#################################################
#   определим время последнего запуска операции
#################################################

$last_done = 0;
$skiped_file = array();
//$temp_file = array();

if (file_exists($phpcompilerpath . 'last.txt'))
{


    $ttt = json_decode(implode(file($phpcompilerpath . 'last.txt')), true);

    $include = $ttt['include'];
    $last_done = $ttt['last_done'];
    $skiped_file = $ttt['skiped_file'];
    //$temp_file = $ttt['temp_file'];
    $skip_txt_date = $ttt['skip_txt_date'];

    if (@filemtime($phpcompilerpath . 'skip.txt') != $skip_txt_date)
    {
        // файл с правилами пропуска менялся
        $skiped_file = array();
        //$temp_file = array();
    }

    unset($ttt);
}


#######################################################
# определим, требуется ли пропустить какие-либо файлы
#######################################################


$skip = array();

if (file_exists($phpcompilerpath . 'skip.txt'))
{
    $skip0 = file($phpcompilerpath . 'skip.txt');

    foreach ($skip0 as $pattern)
    {
        $pattern = trim($pattern);

        // если на конце была *, её убирают, если не было - ставят пробел
        $pattern = ' ' . $pattern . ' ';
        $pattern = str_replace('* ', '', $pattern);
        $pattern = str_replace(' *', '', $pattern);

        $skip []= $pattern;
    }

    unset($skip0);
}






################################
# определим сырой список файлов
################################


$filelist = full_dir($sourcepath);



#########################
#########################

$filebank = array(); # содержимое файлов


foreach ($filelist as $file)
{
    $file = str_replace($sourcepath . '/', '', $file);


    // проверим, не нужно ли пропустить этот файл

    if ($skiped_file[$file])
    {
        continue;
    }


    # определим расширение файла

    $ext = explode('.', $file);
    $ext = $ext[count($ext) - 1];
    $ext = strtolower($ext);




    if ($ext == 'php2htm')
    {
        if (!is_it_new($file, true))
        {
            // файл не изменялся
            continue;
        }
    }
    else
    {
        if (!is_it_new($file))
        {
            // файл не изменялся
            continue;
        }
    }





    if (!$last_done || !array_key_exists($file, $skiped_file))
    {
        // все эти проверки только по одному разу

        foreach ($skip as $pattern)
        {
            if (strpos(' ' . $file . ' ', $pattern) !== false)
            {
                $skiped_file[$file] = 1;
                continue 2;
            }
        }




        $str = @implode(filebank($file));

        if (strpos($str, 'phpcompiler: skip') !== false)
        {
            $skiped_file[$file] = 1;
            continue;
        }

//        if (strpos($str, 'phpcompiler: temp') !== false)
//        {
//            // временный файл
//            $temp_file[$file] = 1;
//        }

    }

    $skiped_file[$file] = 0;

//    if ($temp_file[$file])
//    {
//        #$to_delete[] = $file;
//        continue;
//    }




    # обрабатываем файл

    echo $file;



    # проверяем наличие папки

    $parts = explode('/', $file);
    array_pop($parts);
    $folder = implode($parts, '/');

    if (!is_dir($folder))
    {
        @mkdir($wwwpath . $folder, 0755, true);
        chmod($wwwpath . $folder, 0755);
    }




    //if ($ext != 'php' && $ext != 'htm' && $ext != 'html')
    if ($ext != 'php' && $ext != 'php2htm')
    {
        copy($sourcepath . $file, $wwwpath . $file);
        continue;
    }


    # it is php file

    $already_included = array(); # был ли включен этот файл (чтобы дважды не включать)

    $include0 = array(); // список файлов, включенных в этот файл
    $text = php_includer($file);

    if ($include0)
    {
        $include0 = array_count_values($include0); // частоты элементов
        unset($include0['']); // уберем ссылку на пустую строку
        $include0 = array_keys($include0); // возьмем обратно имена файлов

        $include[$file] = $include0;
    }



    //////////////
    // Чистим PHP
    //////////////


    # kill ? > < ? or < ?? >
    $text = preg_replace('!\?\>[\s]*\<\?(php)?!', '', $text);
    $text = preg_replace('!\<\?(php)?[\s]*\?\>!', '', $text);

    // php minimize
    $text = php_min($text);


    $deleted_func = array();

    $text = func_cleaner($text);

    if ($deleted_func)
    {
        $deleted_func = implode($deleted_func, ', ');

        echo "Function(s) $deleted_func were deleted from $file";
    }

    // бесполезные переменные
    $text = useless_vars($text);



    //////////////////
    // запишем файл
    //////////////////

    if ($ext == 'php2htm')
    {
        $file = str_replace('.php2htm', '.php', $file);
        $to_compile[] = $file;
        $to_delete[] = $file;
    }


    $w = fopen($wwwpath . $file, 'w');
    fwrite($w, $text);
    fclose($w);

}





######################################
# запускаем и записываем компируемые
######################################

if ($to_compile)
{
    foreach($to_compile as $file)
    {
        $url = 'http://' . $_GET['site'] . '/' . $file;
        $text = implode(file($url));


        $file = str_replace('.php', '.htm', $file);
        $w = fopen($wwwpath . $file, 'w');
        fwrite($w, $text);
        fclose($w);
    }
}









#############################
# удаляем временные файлы
#############################

if (file_exists($phpcompilerpath . 'delete.txt'))
{
    $delete0 = file($phpcompilerpath . 'delete.txt');
    foreach ($delete0 as $file)
    {
        $file = trim($file);
        $to_delete []= $file;
    }
    unset($delete0);
}


if ($to_delete)
{
    foreach ($to_delete as $file)
    {
        @unlink($wwwpath . $file);
    }
}








//////////////////////////////////////////////
// запишем время последнего компиллирования
//////////////////////////////////////////////

$ttt = array();

$ttt['include'] = $include;
$ttt['last_done'] = time();
$ttt['skiped_file'] = $skiped_file;
//$ttt['temp_file'] = $temp_file;
$ttt['skip_txt_date'] = @filemtime($phpcompilerpath . 'skip.txt');


$ttt = json_encode($ttt);


$w = fopen($phpcompilerpath . 'last.txt', 'w');
fwrite($w, $ttt);
fclose($w);







// засечем время
$t1 = microtime(true);

$t = $t1 - $t0;

echo 'Task required ' . $t . ' second</b>';



?>
