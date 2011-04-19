<?


//require_once 'Compressor.php';

//require_once 'func_cleaner.php';


#############
# pack CSS
#############

function css_packer($filelist)
{
    global $sourcepath;
    
    
    if (gettype($filelist) == 'string')
    {
        $filelist = trim($filelist);
        $filelist = str_replace('[inc_css]', '', $filelist);
        $filelist = explode(',', $filelist);
    }


    $css = '';

    foreach($filelist as $file)
    {

        $file = trim($file);

        if (!$file)
        {
            continue;
        }




        
        $text = @implode(filebank($file));
        
        
        if (!$text)
        {
            echo '<br><br><b>Can not find file: ';
            echo $file;
            echo '</b>';
            
            continue;
        }
        
        $css .= "\n\n";
        
        $css .= $text;
        
        $css .= "\n\n";


    }
    
    
    #####################

    if ($_GET['pack'] == 'yes')
    {
        // запускаем кодировщик

        $encoding = 'None';
        $fast_decode = true;
        $special_char = false;

        $packer = new JavaScriptPacker($css, $encoding, $fast_decode, $special_char);

        $css = $packer->pack();
    }
    
    $css .= "\n\n";

    return $css;
}

?>