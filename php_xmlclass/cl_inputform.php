<?
class inputForm
{
	private $langs, $inputData, $code;
	
	function inputForm()
	{
		$this->langs = Array();
		$this->langs['cpp_qt_useqxml'] = Array();
		$this->langs['cpp_qt_useqxml']['name'] = 'C++ & Qt 5.1 (QXmlReader, QXmlWriter)';
		$this->langs['cpp_qt_useqxml']['include_file'] = 'generators/cpp_qt_useqxml.php';
		
		$this->langs['cpp_cbuilderxe3_usexml'] = Array();
		$this->langs['cpp_cbuilderxe3_usexml']['name'] = 'C++ Builder XE3 & TXMLDocument';
		$this->langs['cpp_cbuilderxe3_usexml']['include_file'] = 'generators/cpp_cbuilderxe3_usexml.php';
		$this->inputData = "";
	}

	function printHead()
	{
		echo "<script type='text/javascript' src='js/form.js'></script>\r\n";
	}
	
	function printData()
	{
		if(is_array($this->code))
		{
			echo '<h3>XML:</h3>';
			echo '<pre>'.htmlspecialchars($this->inputData).'</pre>';

			foreach ($this->code as $key => $value) {
				echo '<h3>'.$key.':</h3>';
				echo '<pre>'.htmlspecialchars($value).'</pre>';
			}
		}
		else
		{
			echo "It has not data";
		}
	}
	
	function printForm()
	{
		echo '
		
<form action="index.php" method="post" enctype="multipart/form-data">
	<table>
		<tr>
		<td>XML: </td>
		<td>
			<select name="xml_input" id="xml_input" onchange="getComboA(this)">
				<option id="fromfile" value="fromfile">from file</option>
				<option id="fromtext" value="fromtext">text</option>
			</select><br>
		</td>
	</tr>
	<tr> <td/> <td> <div id="xml_i"/> </td> </tr>
<tr>
		<td>Select language:</td>
		<td>
			<select name="output_lang">';
			
			foreach ($this->langs as $key => $value) {
				echo '<option value="'.$key.'">'.$value['name'].'</option>';
			}
echo 
'			</select>
		</td>
	</tr>
	<tr>
		<td>Select output type:</td>
		<td>
			<select name="output_type" id="outputtype" onchange="getComboB(this)">
				<option value="onpage">On Page</option>
				<option value="zipfile">*.zip</option>
			</select>
		</td>
	</tr>	
	<tr id="xml_o">
		<td>Output filename: </td>
		<td><input name="output_filename" type="text" value=""/></td>
	</tr>
	<tr>
		<td>rename class <br> or namespace: </td>
		<td><input name="renameclass" type="text" value=""/></td>
	</tr>

	<tr>
		<td></td>
		<td><input type="submit" value="OK"/></td>
	</tr>		

	</table>
</form>
';
	}
	
	function processData()
	{
		if(isset($_POST['xml_input']))
		{
			$typeOfInput = $_POST['xml_input'];
			if($typeOfInput == "fromfile")
			{
				if(!isset($_FILES['data_xml']))
				{
					echo "I don't found file 'data_xml'\r\n";
					exit;
				}
				if(file_exists($_FILES['data_xml']['tmp_name']))
					$this->inputData = file_get_contents($_FILES['data_xml']['tmp_name']);
			}
			else if($typeOfInput == "fromtext")
				$this->inputData = $_POST['data_xml'];

			$lang = $_POST['output_lang'];
			
			$output_filename = "autogenxml";
			if(isset($_POST['output_filename']) && strlen($_POST['output_filename']) > 0 )
				$output_filename = basename($_POST['output_filename']);
					
			include_once($this->langs[$_POST['output_lang']]['include_file']);
			$this->code = generateFiles($this->inputData, $output_filename);
			
			if($_POST['output_type'] == 'zipfile')
			{
				$zipname = tempnam("/tmp", "axf");
				$zip = new ZipArchive();
				if($zip->open($zipname, true ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
					echo "error 1";
					return false;
				}
				foreach ($this->code as $key => $value) {
					$zip->addFromString($key, $value);
				}
				$zip->close();
				
				header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
				header("Cache-Control: public"); // needed for i.e.
				header("Content-Type: application/zip");
				header("Content-Transfer-Encoding: Binary");
				header("Content-Length:".filesize($zipname));
				header("Content-Disposition: attachment; filename=".$output_filename.'.zip');
				readfile($zipname); 
				unlink($zipname);
				exit;
			}
			return true;
		}
		return false;
	}
};
?>
