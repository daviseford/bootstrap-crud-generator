<? 
include('config.php'); 
if (isset($_GET['id']) ) { 
$id = (int) $_GET['id']; 
if (isset($_POST['submitted'])) { 
foreach($_POST AS $key => $value) { $_POST[$key] = mysql_real_escape_string($value); } 
$sql = "UPDATE `generator_Ads` SET  `adName` =  '{$_POST['adName']}' ,  `adGroup` =  '{$_POST['adGroup']}' ,  `adURL` =  '{$_POST['adURL']}' ,  `adDescription` =  '{$_POST['adDescription']}' ,  `template` =  '{$_POST['template']}' ,  `imageURL` =  '{$_POST['imageURL']}' ,  `imageWidth` =  '{$_POST['imageWidth']}' ,  `imageHeight` =  '{$_POST['imageHeight']}' ,  `enabled` =  '{$_POST['enabled']}' ,  `highlight` =  '{$_POST['highlight']}' ,  `isInline` =  '{$_POST['isInline']}' ,  `inlineHeader` =  '{$_POST['inlineHeader']}' ,  `inlineText` =  '{$_POST['inlineText']}'   WHERE `id` = '$id' "; 
mysql_query($sql) or die(mysql_error()); 
echo (mysql_affected_rows()) ? "Edited row.<br />" : "Nothing changed. <br />"; 
echo "<a href='list.php'>Back To Listing</a>"; 
} 
$row = mysql_fetch_array ( mysql_query("SELECT * FROM `generator_Ads` WHERE `id` = '$id' ")); 
?>

<form action='' method='POST'> 
<p><b>AdName:</b><br /><textarea name='adName'><?= stripslashes($row['adName']) ?></textarea> 
<p><b>AdGroup:</b><br /><input type='text' name='adGroup' value='<?= stripslashes($row['adGroup']) ?>' /> 
<p><b>AdURL:</b><br /><textarea name='adURL'><?= stripslashes($row['adURL']) ?></textarea> 
<p><b>AdDescription:</b><br /><textarea name='adDescription'><?= stripslashes($row['adDescription']) ?></textarea> 
<p><b>Template:</b><br /><input type='text' name='template' value='<?= stripslashes($row['template']) ?>' /> 
<p><b>ImageURL:</b><br /><input type='text' name='imageURL' value='<?= stripslashes($row['imageURL']) ?>' /> 
<p><b>ImageWidth:</b><br /><input type='text' name='imageWidth' value='<?= stripslashes($row['imageWidth']) ?>' /> 
<p><b>ImageHeight:</b><br /><input type='text' name='imageHeight' value='<?= stripslashes($row['imageHeight']) ?>' /> 
<p><b>Enabled:</b><br /><input type='text' name='enabled' value='<?= stripslashes($row['enabled']) ?>' /> 
<p><b>Highlight:</b><br /><input type='text' name='highlight' value='<?= stripslashes($row['highlight']) ?>' /> 
<p><b>IsInline:</b><br /><input type='text' name='isInline' value='<?= stripslashes($row['isInline']) ?>' /> 
<p><b>InlineHeader:</b><br /><textarea name='inlineHeader'><?= stripslashes($row['inlineHeader']) ?></textarea> 
<p><b>InlineText:</b><br /><textarea name='inlineText'><?= stripslashes($row['inlineText']) ?></textarea> 
<p><input type='submit' value='Edit Row' /><input type='hidden' value='1' name='submitted' /> 
</form> 
<? } ?> 
