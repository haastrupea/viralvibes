<?php
class homePageModel{
    public function __construct($section)
    {
        $this->pageSection=$section;
    }
    public function getOfficialName()
    {
        return "viralVibes Tv";
    }
    
    public function getLogo()
    {
        return "/template/sharedAssets/imgs/logo.svg";
    }
    public function getPageSection()
    {
        return $this->pageSection;
    }
}