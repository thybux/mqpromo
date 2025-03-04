<?php

class MqPromo extends Module
{
    public function __construct()
    {
        $this->name = 'mqpromo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Your Name';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MQ Promotions');
        $this->description = $this->l('Display promotions on your homepage');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
         return parent::install() &&
            $this->registerHook('displayHome') &&
            $this->registerHook('header');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    
   
    public function hookDisplayHome($params)
    {
      $promo = "salut";
      $this->context->smarty->assign([
        "promo" => $promo
      ]);
      return $this->fetch('module:' . $this->name . '/views/template/mqpromo.tpl');
    }

   public function hookDisplayHeader()
    {

        $this->context->controller->registerStylesheet(
            'mqreviews-style-reviews',
            'modules/'.$this->name.'/views/css/reviews.css',
            ['media' => 'all', 'priority' => 200]
        );

        $this->context->controller->registerStylesheet(
            'mqreviews-style-reassurance',
            'modules/'.$this->name.'/views/css/reassurance.css',
            ['media' => 'all', 'priority' => 200]
        );

        $this->context->controller->registerStylesheet(
            'material-icons',
            'https://fonts.googleapis.com/icon?family=Material+Icons',
            ['server' => 'remote', 'media' => 'all', 'priority' => 200]
        );
     }

  }


?>
