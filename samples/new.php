<?php
include('config.php'); 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "INSERT INTO `generator_Ads` ( `adName` ,  `adGroup` ,  `adURL` ,  `adDescription` ,  `template` ,  `imageURL` ,  `imageWidth` ,  `imageHeight` ,  `enabled` ,  `highlight` ,  `isInline` ,  `inlineHeader` ,  `inlineText`  ) VALUES(  '{$_POST['adName']}' ,  '{$_POST['adGroup']}' ,  '{$_POST['adURL']}' ,  '{$_POST['adDescription']}' ,  '{$_POST['template']}' ,  '{$_POST['imageURL']}' ,  '{$_POST['imageWidth']}' ,  '{$_POST['imageHeight']}' ,  '{$_POST['enabled']}' ,  '{$_POST['highlight']}' ,  '{$_POST['isInline']}' ,  '{$_POST['inlineHeader']}' ,  '{$_POST['inlineText']}'  ) "; 
mysql_query($sql) or die(mysql_error()); 
echo "Added row.<br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
?>

<form action='' method='POST'> 
<p><b>AdName:</b><br /><textarea name='adName'></textarea> 
<p><b>AdGroup:</b><br /><input type='text' name='adGroup'/> 
<p><b>AdURL:</b><br /><textarea name='adURL'></textarea> 
<p><b>AdDescription:</b><br /><textarea name='adDescription'></textarea> 
<p><b>Template:</b><br /><input type='text' name='template'/> 
<p><b>ImageURL:</b><br /><input type='text' name='imageURL'/> 
<p><b>ImageWidth:</b><br /><input type='text' name='imageWidth'/> 
<p><b>ImageHeight:</b><br /><input type='text' name='imageHeight'/> 
<p><b>Enabled:</b><br /><input type='text' name='enabled'/> 
<p><b>Highlight:</b><br /><input type='text' name='highlight'/> 
<p><b>IsInline:</b><br /><input type='text' name='isInline'/> 
<p><b>InlineHeader:</b><br /><textarea name='inlineHeader'></textarea> 
<p><b>InlineText:</b><br /><textarea name='inlineText'></textarea> 
<p><input type='submit' value='Add Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 
