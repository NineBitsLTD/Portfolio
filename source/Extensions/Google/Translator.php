<?php
namespace Google;

class Translator{
    /**
     * Url pattern
     * 
     * @var string
     */
    static public $Url = "https://glosbe.com/gapi/translate?from=en&dest=ru&format=json&phrase=%s";
    //static public $Url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e&sl=%s&tl=%s&g=%s";
    /**
     * Trenslate $key from $from language to $to language
     * 
     * @param string $from From language ISO 2 chars
     * @param string $to To language ISO 2 chars
     * @param string $key Text
     * @return string Translated text
     */
    static public function Get($from, $to, $key){
        $url = sprintf(static::$Url, urlencode($key));
        $doc = \Helper\File::GetConditionalContents($url);
        $res = json_decode($doc, true);
        if(is_array($res['tuc']) && count($res['tuc'])>0 && is_array($res['tuc'][0]) && isset($res['tuc'][0]['phrase'])){
            return $res['tuc'][0]['phrase']['text'];
        } else {
            return $key;
        }
        //$dom = new \DOMDocument();
        //@$dom->loadHTML($doc);
        //if(!is_object($dom)) return $key;
        //$dom->preserveWhiteSpace = false;
        //$dom->getElementsByTagName('h1')->item(0)->lastChild->nodeValue
        //foreach ($group->getElementsByTagName('div') as $item) if($item->getAttribute('class')=='catalog_group_components_c')
        //$finder = new \DomXPath($dom);
        //$finder->query("//*[contains(@class, 'catalog_group_params')]")->item(0)->getElementsByTagName('tr');
        //$result = $dom->getElementById('result_box');
        //if(isset($result)) return $result->textContent;
        //else return $key;
        
    }
}
