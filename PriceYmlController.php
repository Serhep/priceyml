<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
 
class PriceYmlControllerCore extends FrontController
{
    public $auth = false;
    public $php_self = 'priceyml';
    public $authRedirection = 'priceyml';

    public $passwordRequired = false;

    public function postProcess()
    {
        
        $cats = Category::getCategories((int)$this->context->language->id, true, false) ;
        
        $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/files/price.yml', 'w');
            $text = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <yml_catalog date=\"".date('Y-m-d')."T".date('H:i:s')."\">
            <shop>
                <name>БИЗНЕСMEN</name>
                <company>БИЗНЕСMEN</company>
                <url>https://shop.bizmn.ru</url>
                <currencies>
                    <currency id=\"RUR\" rate=\"1\"/>
                </currencies>
                <categories>
                ";
                fwrite($fp, $text);
                foreach($cats as $data){
                if($data['id_category']>1){ 
                    if($data['id_category'] == 2)
                $text = "   <category id=\"".$data['id_category']."\">".$data['name']."</category>
                ";
                    else   
                $text = "   <category id=\"".$data['id_category']."\" parentId=\"".$data['id_parent']."\">".$data['name']."</category>
                ";
                fwrite($fp, $text);
                    }
                }
                $text = "</categories>
                <delivery-options>
                    <option cost=\"0\" days=\"2-4\"/>
                </delivery-options>
                <pickup-options>
                    <option cost=\"0\" days=\"2-4\"/>
                </pickup-options>
            <offers>
                ";

            fwrite($fp, $text); 


        $products_partial = Product::getProducts((int)$this->context->language->id, 0, 0, 'name', 'asc');
        $products = Product::getProductsProperties((int)$this->context->language->id, $products_partial);
/*
        $item = (new Product($products[0]['id_product']))->getAttributeCombinations();
*/
        $link = new Link;

    foreach ($products as $key => $product) {          
        $cover = Product::getCover($product['id_product']);
        $products[$key]["id_image"] = 'https://'.$link->getImageLink($products[$key]['link_rewrite'], $cover["id_image"], 'home_default');
    }
            foreach ($products as $data) {
                if($data['active']){
                $text = "<offer id=\"".$data['id_product']."\">
                        <name>".$data['name']."</name>
                        <url>".$data['link']."</url>
                        <price>".$data['price']."</price>
                        "
                        .(($data['price']==$data['price_without_reduction'])?"":"<oldprice>".$data['price_without_reduction']."</oldprice>
                        ").
                        "<currencyId>RUR</currencyId>
                        <categoryId>".$data['id_category_default']."</categoryId>
                        <picture>".$data['id_image']."</picture>
                        <delivery>true</delivery>
                        <pickup>true</pickup>
                        <delivery-options>
                            <option cost=\"0\" days=\"2-4\"/>
                        </delivery-options>
                        <pickup-options>
                            <option cost=\"0\" days=\"2-4\"/>                        
                        </pickup-options>
                        <store>true</store>
                        <description><![CDATA[".$data['description_short']."]]></description>
                    </offer>
                ";
                fwrite($fp, $text);
                }
            }
            $text = "</offers>
        </shop>
</yml_catalog>";

            fwrite($fp, $text);             
            fclose($fp);

    }

}
