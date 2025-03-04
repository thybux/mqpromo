<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

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
    $promo = $this->getHomePromo();

    $enrichedProducts = [];

    foreach ($promo as $product) {
      $id_product = (int)$product['id_product'];
      $productObj = new Product($id_product, true, $this->context->language->id);

      if (!Validate::isLoadedObject($productObj)) {
        continue;
      }

      $images = Image::getImages($this->context->language->id, $id_product);
      $id_image = !empty($images) ? $images[0]['id_image'] : null;

      $specific_prices = SpecificPrice::getByProductId($id_product);

      // Prix non réduit (prix régulier)
      $priceWithoutReduction = $productObj->getPriceWithoutReduct();

      // Prix actuel (avec réduction si applicable)
      $currentPrice = Product::getPriceStatic($id_product);

      // Calcul du pourcentage de réduction
      $discount_percentage = 0;
      if (!empty($specific_prices) && $priceWithoutReduction > 0 && $currentPrice < $priceWithoutReduction) {
        $discount_percentage = round(100 - ($currentPrice * 100 / $priceWithoutReduction));
      }

      $productInfo = [
        'id_product' => $id_product,
        'reference' => $product['reference'],
        'name' => $product['name'],
        'price' => Tools::displayPrice($currentPrice),
        'price_without_reduction' => Tools::displayPrice($priceWithoutReduction),
        'specific_prices' => !empty($specific_prices),
        'discount_percentage' => $discount_percentage,
        'id_image' => $id_image,
        'link_rewrite' => $productObj->link_rewrite
      ];

      $enrichedProducts[] = $productInfo;
    }

    // Retirez cette ligne pour la production
    // dump($enrichedProducts);

    $this->context->smarty->assign([
      "promo" => $enrichedProducts
    ]);

    return $this->fetch('module:' . $this->name . '/views/templates/mqpromo.tpl');
  }

  public function hookDisplayHeader()
  {
    $this->context->controller->registerStylesheet(
      'mqpromo-style',
      'modules/' . $this->name . '/views/css/mqpromo.css',
      ['media' => 'all', 'priority' => 200]
    );

    $this->context->controller->registerJavascript(
      'mqpromo-js',
      'modules/' . $this->name . '/views/js/mqpromo.js',
      ['position' => 'bottom', 'priority' => 200]
    );
  }

  public function getHomePromo()
  {
    $result = $this->getPromoFromBdd();
    return $result;
  }

  public function getPromoFromBdd()
  {
    $db = Db::getInstance();
    $id_lang = (int)Context::getContext()->language->id;
    $sql = 'SELECT p.reference, p.id_product, p.price, pl.name
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . $id_lang . ')
            WHERE p.on_sale = 1
            AND p.active = 1
            LIMIT 10';
    $result = $db->executeS($sql);
    return $result;
  }
}
