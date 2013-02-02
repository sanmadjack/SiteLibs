<?php
abstract class AXmlOutput {
    
    public static $content_type = "text/xml";
    
    private $schema;
    protected $xml;
    protected $root;

    public function __construct($root_node_name, $schema = null) {
        $this->schema = $schema;

        $this->xml = new DOMDocument();
        $this->xml->encoding = 'UTF-8';
        $this->xml->formatOutput = true;
        
        $this->root = $this->createelement($root_node_name);
        
        $this->xml->appendChild($this->root);

    }
 
     public function renderXml() {
        $text = $this->xml->saveXML();
        $document = new DOMDocument();
        $document->loadXML($text);
        $folder =  dirname(__FILE__);
        if($this->schema!=null) {
            if (!$document->schemaValidate($folder."/".$this->schema)) {
                echo $text;
                $this->error_occured = true;
                throw new Exception("XML DID NOT PASS VALIDATION: " . $this->schema);
            }
        }
        header("Content-Type:".$this::$content_type."; charset=UTF-8'");
        echo $text;
    }
    
        protected function createElement($name, $content = null) {
        if ($content == null) {
            return $this->xml->createElement($name);
        } else if($content =="") {
            $ele = $this->xml->createElement($name);
            $ele->appendChild($this->xml->createTextNode(''));
            return $ele;
        } else {
            return $this->xml->createElement($name, self::cleanUp($content));
        }
    }

    protected function setAttribute($element,$name,$value) {
        $element->appendChild($this->xml->createAttribute($name))->
                appendChild($this->createTextNode($value));
        
    }
    protected function createTextNode($text) {
        return $this->xml->createTextNode($text);
    }

    private static function cleanUp($string) {
        @$string = htmlspecialchars($string,ENT_COMPAT|ENT_XML1,'UTF-8');
        return $string;
    }

}
?>