<?php
class homePageView implements views{

    public function render($model)
    {
        //unpack models
        $officialName=$model->getOfficialName();
        $logoImg=$model->getLogo();
        $section=$model->getPageSection();
        $renderSection=$this->renderSection($section);//render page section
        
        //load template
       include_once 'template/home/homePage.php';

    }

    protected function renderSection($section)
    {
        $str="<h4>Home";
        if(!empty($section)){
            $str.="<span style='color:grey;'>";
            $str.=">$section";
            $str.="</span>";
        }
        $str.="</h4>";
        return $str;
    }
}