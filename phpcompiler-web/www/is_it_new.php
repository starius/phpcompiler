<?

// определяет по файлу, изменился ли он или вложенные в него файлы
function is_it_new($file, $exec = false)
{

    if ($_GET['remake'] == 'yes')
    {
        // если всё переделывать - все файлы новые
        return true;
    }
    
    global $include;
    global $modified;
    global $last_done;
    
    global $sourcepath;
    global $wwwpath;

    
    if (!$modified[$file])
    {
    
        if ($exec)
        {
            $F = str_replace('.php2htm', '.htm', $file);
        }
        else
        {
            $F = $file;
        }
    
        if (!@file_exists($wwwpath . $F))
        {
            // такого файла раньше не было
            $modified[$file] = $last_done + 10;
            
        }
        else
        {    
            $modified[$file] = @filemtime($sourcepath . $file);
        }
    }
        
    if ($modified[$file] > $last_done)
    {
        //echo $sourcepath . $file . ' ' . @filemtime($sourcepath . $file) . ' ' . $last_done;
        return true;
    }
    
    if ($include[$file])
    {
        foreach ($include[$file] as $file0)
        {
        
            //echo "<br><br>";
            //echo $file;
            //echo '<br>';
            //echo $file0;
                        
            if (!$modified[$file0])
            {
                $modified[$file0] = @filemtime($sourcepath . $file0);
            }
                
            if ($modified[$file0] > $last_done)
            {
                //echo $sourcepath . $file0 . ' ' . @filemtime($sourcepath . $file0) . ' ' . $last_done;
                return true;
            }
        
        }
    }
    
    return false;
}



?>