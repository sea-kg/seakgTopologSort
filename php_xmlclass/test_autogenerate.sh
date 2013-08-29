
# xml_input=fromfile
# data_xml=filename == text.xml
# lang=cpp_qt_useqxml
# outputtype=zipfile
# outputfilename='autocode'

FILENAME_XML="test.xml"

curl -o temp.zip -X POST -d outputfilename=autocode --data-binary data-xml=$FILENAME_XML -d xml_input=fromfile -d lang=cpp_qt_useqxml -d outputtype=zipfile http://localhost/xmlclass/index.php
unzip temp.zip temp/*

SOURCE_FOLDER="../proj/src/"

cp temp/* $SOURCE_FOLDER
rm temp.zip
