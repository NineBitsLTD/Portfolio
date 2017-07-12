<?php

$url="http://www.floatrates.com/daily/usd.xml";
$contents = file_get_contents($url);
$parser = xml_parser_create('');
xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
xml_parse_into_struct($parser, trim($contents), $xml_values);
xml_parser_free($parser);
foreach ($xml_values as $data){
    print_r($data);
}