<?
$overwords=$_POST[overwords];
$letters=$_POST[letters];
if($_POST){
    $t=$_POST[txt];
    $t=preg_match_all("#([a-zA-Z]{".$letters.",})#is",$t,$f);
    foreach($f[0] as $w){
        $w=strtolower($w);
        $all[$w]++;
    }
    foreach($all as $key=>$val){
        if($val>$overwords){
        echo $key.'<br>';
        }
    }
}

?><form method=post><textarea rows="20" cols="70" name="txt"></textarea><br>
    <input type="text" name="overwords" value="count of includings"><br>
    <input type="text" name="letters" value="letters more than.."><br>
    <br>
<input type=submit></form>
