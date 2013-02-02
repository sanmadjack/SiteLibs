<?php
// Generic sitemap generating library written by Matthew Barbour in 2013
require_once "AXmlOutput.php";
abstract class ASiteMap extends AXmlOutput {
    protected $root_url;

    public static $namespace = "http://www.sitemaps.org/schemas/sitemap/0.9";
    public static $last_mod_format = "Y-m-d";
    public static $change_freq = array("always","hourly","daily","weekly","monthly","yearly","never");

    public function __construct($root_url) {
        parent::__construct("urlset","sitemap.xsd");
        $this->root_url = $root_url;
        
        $this->setAttribute($this->root,"xmlns",self::$namespace);
        
    }
    
    protected function addURL($loc, $lastmod = null, $changefreq = null, $priority = null) {
        $url = $this->createElement("url");
        
        $loc_node = $this->createElement("loc", $loc);
        $url->appendChild($loc_node);
        
        if($lastmod != null) {
            $lastmod_node = $this->createElement("lastmod", date_format($lastmod,$this::$last_mod_format));
            $url->appendChild($lastmod_node);
        }
        if($changefreq != null) {
            $changefreq_node = $this->createElement("changefreq",$changefreq);
            $url->appendChild($changefreq_node);
        }
        if($priority != null) {
            if ( !is_numeric($priority) || $priority < 0 || $priority > 1 ) {
                throw new Exception("Priority must be a decimal number between 0 and 1");
            }
            $priority_node = $this->createElement("priority",$priority);
            $url->appendChild($priority_node);
        }
        
        $this->root->appendChild($url);
    }
    
    
    

}
?>