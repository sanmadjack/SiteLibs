<?php
// Generic sitemap generating library written by Matthew Barbour in 2013
require_once "AXmlOutput.php";
abstract class ARSSFeed extends AXmlOutput {
    public static $rss_version = "2.0";

    private $channels = array();

    public function __construct() {
        parent::__construct("rss",null);
        
        $this->setAttribute($this->root,"version",self::$rss_version);
        
    }
    
    protected function addChannel($title, $link, $description) {
        $channel = $this->createElement("channel");
        
        $channel->appendChild($this->createElement("title",$title));
        $channel->appendChild($this->createElement("link",$link));
        $channel->appendChild($this->createElement("description",$description));
        
        $this->root->appendChild($channel);
        
        $id = count($this->channels);
        $this->channels[$id] = $channel;
        return $id;
    }
    
    protected function addItem($channel_id, $title, $link, $description,
                                $author = null, $pudDate = null) {
        $item = $this->createElement("item");
        
        $item->appendChild($this->createElement("title",$title));
        $item->appendChild($this->createElement("link",$link));
        $item->appendChild($this->createElement("description",$description));
        
        if($author!=null) {
            $item->appendChild($this->createElement("author",$author));
        }
        if($pudDate!=null) {
            $item->appendChild($this->createElement("pudDate",$pudDate));
        }
        
        $this->channels[$channel_id]->appendChild($item);
        
    }
    
    

}
?>