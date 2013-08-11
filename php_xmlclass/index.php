<html>
<head>
<title> collection my films </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
</head>
<body>
<center>
<form action='form-handler.php' method='post' enctype='multipart/form-data'>
<table>
	<tr>
		<td>XML-file: </td>
		<td><input type='file' value=''/></td>
	</tr>
	<tr>
		<td>Select language:</td>
		<td>
			<select name='lang'>
				<option>C++ && Qt 5.1 (QXmlReader, QXmlWriter)</option>
				<option>C++ Builder XE3</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>rename class <br> or namespace: </td>
		<td><input name='rename' type='text' value=''/></td>
	</tr>
	<tr>
		<td></td>
		<td><input type='submit' value='OK'/></td>
	</tr>	
	
</table>
</form>

<?
	echo "Привет, мир!";

?>

</body>
</html>
