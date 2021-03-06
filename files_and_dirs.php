<?

/*
возвращает список всех вложенный файлов каталога
путь включает $directory
пропускает папки, имя которых начинается с точки
*/
function full_dir($directory)
{
    $files = array();

    $dir = opendir($directory);
    while($smth = readdir($dir))
    {
        if (is_file($directory . "/" . $smth))
        {
            $files []= $directory . "/" . $smth;
        }
        else if (is_dir($directory . "/" . $smth) && $smth[0] != '.')
        {
            $files = array_merge($files, full_dir($directory . "/" . $smth));
        }
    }

    closedir($dir);

    return $files;
}






// Мемоизатор для чтения файлов
function filebank($file)
{
    global $filebank; # массив с текстами файлов
    global $sourcepath;

    if (!$filebank[$file])
    {
        $filebank[$file] = @file($sourcepath . $file);
    }

    return $filebank[$file];
}
















?>
