{*
* Carousel moderne des produits en promotion
*}
<div class="mqpromo-container">
  <div class="mqpromo-header">
    <h2>{l s='Nos offres promotionnelles' d='Modules.Mqpromo.Shop'}</h2>
    <p>{l s='Découvrez nos produits à prix réduits' d='Modules.Mqpromo.Shop'}</p>
  </div>

  <div class="mqpromo-carousel-wrapper">
    <div class="mqpromo-carousel" id="mqpromo-carousel">
      {foreach from=$promo item=product name=promoloop}
      <div class="mqpromo-item" data-index="{$smarty.foreach.promoloop.index}">
        <a href="{$link->getProductLink($product.id_product)}" class="mqpromo-product-link">
          <div class="mqpromo-image-container">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}"
              alt="{$product.name|escape:'htmlall':'UTF-8'}" class="mqpromo-product-image" />
            <div class="mqpromo-badge">
              <span>{l s='PROMO' d='Modules.Mqpromo.Shop'}</span>
            </div>
          </div>
          <div class="mqpromo-product-info">
            <h3 class="mqpromo-product-name">{$product.name|truncate:30:'...'}</h3>
            {*<p class="mqpromo-product-ref">{l s='Ref:' d='Modules.Mqpromo.Shop'} {$product.reference}</p>*}
            <div class="mqpromo-product-price">
              {if isset($product.specific_prices) && $product.specific_prices}
              <span class="mqpromo-old-price">{$product.price_without_reduction}</span>
              <span class="mqpromo-current-price">{$product.price}</span>
              {else}
              <span class="mqpromo-current-price">{$product.price}</span>
              {/if}
            </div>
            <button class="mqpromo-cta">{l s='Consulter' d='Modules.Mqpromo.Shop'}</button>
          </div>
        </a>
      </div>
      {/foreach}
    </div>

    <div class="mqpromo-controls">
      <button class="mqpromo-prev" aria-label="{l s='Produit précédent' d='Modules.Mqpromo.Shop'}">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" />
        </svg>
      </button>
      <button class="mqpromo-next" aria-label="{l s='Produit suivant' d='Modules.Mqpromo.Shop'}">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" />
        </svg>
      </button>
    </div>
  </div>

  <div class="mqpromo-dot-indicators" id="mqpromo-dots">
  </div>
</div>

<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function () {
    initPromoCarousel();
  });
</script>
