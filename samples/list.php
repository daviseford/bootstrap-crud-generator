<? 
require_once(__DIR__.'/config.php'); 
echo "<table border=1 >"; 
echo "<tr>"; 
echo "<td><b>Id</b></td>"; 
echo "<td><b>AdName</b></td>"; 
echo "<td><b>AdGroup</b></td>"; 
echo "<td><b>AdURL</b></td>"; 
echo "<td><b>AdDescription</b></td>"; 
echo "<td><b>Template</b></td>"; 
echo "<td><b>ImageURL</b></td>"; 
echo "<td><b>ImageWidth</b></td>"; 
echo "<td><b>ImageHeight</b></td>"; 
echo "<td><b>Enabled</b></td>"; 
echo "<td><b>Highlight</b></td>"; 
echo "<td><b>IsInline</b></td>"; 
echo "<td><b>InlineHeader</b></td>"; 
echo "<td><b>InlineText</b></td>"; 
echo "</tr>"; 
$result = mysql_query("SELECT * FROM `generator_Ads`") or trigger_error(mysql_error()); 
while($row = mysql_fetch_array($result)){ 
foreach($row AS $key => $value) { $row[$key] = stripslashes($value); } 
echo "<tr>";  
echo "<td valign='top'>" . nl2br( $row['id']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['adName']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['adGroup']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['adURL']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['adDescription']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['template']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['imageURL']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['imageWidth']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['imageHeight']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['enabled']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['highlight']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['isInline']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['inlineHeader']) . "</td>";  
echo "<td valign='top'>" . nl2br( $row['inlineText']) . "</td>";  
echo "<td valign='top'><a href=edit.php?id={$row['id']}>Edit</a></td><td><a href=delete.php?id={$row['id']}>Delete</a></td> "; 
echo "</tr>"; 
} 
echo "</table>"; 
echo "<a href=new.php>New Row</a>"; 
?>