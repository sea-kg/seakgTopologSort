<?
	include_once("cl_inputform.php");
	$form = new inputForm();	
	$form->processData();
?>

<html>
<head>
<title> Generator classes & reader/writer for xml </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">

<?
	$form->printHead();
?>

</head>
<body onload="init_v();">
<hr>
<center>
	
	<h1>Generator xml-reader & xml writer</h1>
	
<?
	$form->printForm();
?>
</center>
<hr>
<?
	$form->printData();
?>

</body>
</html>
