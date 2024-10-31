<?php
/*
Plugin Name: PM Currency For WooCommerce
Plugin URI: https://www.plugins.monster/pmcurrencyforwc
Description: Show Products Price In Site Visitors Local Currency In Your WooCommerce Store Automatically
Version: 1.0.0
Author: Plugins Monster
Author URI: https://www.plugins.monster
License: GPL2
*/
/*  Copyright 2021 Plugins Monster (email : support@plugins.monster)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


global $wpdb;

add_action('admin_menu', 'woocurrencybypluginsmonster_top_menu');

function woocurrencybypluginsmonster_top_menu() {
add_menu_page('PMCurrencyForWC', 'PMCurrencyForWC', 'read', 'woocurrencybypluginsmonster_slug', 'woocurrencybypluginsmonster_mainpage', plugin_dir_url( __FILE__ ) . '/pmcurrencyforwcicon.png');
}

function woocurrencybypluginsmonster_mainpage() {
global $wpdb;
$myrow21 = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."woocurrencybypluginsmonster" );
$blogurl = get_bloginfo ( 'wpurl' );
$errcheckurl = $blogurl."/wp-admin/admin.php?page=woocurrencybypluginsmonster_slug&errcheck=yes";
?>
<p align="center"><a href="https://www.plugins.monster/pmcurrencyforwc" target=_blank><img src="<?php echo(plugin_dir_url( __FILE__ )); ?>/woocurrencytopbanner.png"></a></p>
<div align="center" style="background-color: #ffdbf6;width: auto;border: 5px solid black;padding: 50px;margin: 20px;">
<h1 align="center">PM Currency For WooCommerce</h1>
<strong>Plugins Monster is an all women team</strong> that loves to create useful Wordpress plugins for users around the world. <br>We also offer customization services & various other useful plugins. <br><a href="https://www.plugins.monster" target="_blank">Click here</a> to visit our website to know more. 
<br><br><b>Thank you for installing this plugin & supporting our all women team</b>.
<br><br>Visit <a href="https://www.plugins.monster/pmcurrencyforwc" target=_blank>plugin website</a> to read all about plugin, see demo & find help for customization.
<br><br>The plugin base currency is USD so all product prices will also show the amount in site visitor's local currency after USD conversion.
<br><br>So for example, if product price is $100 then for a UK site visitor the product price will also show as £70 (assuming $100 converts to £70).
<br><br><strong>Is your WooCommerce store in any base currency other than USD?</strong> <br>If so, click on the banner below to purchase the Woo Currency Pro plugin and get instant ability to set base currency from choice of 75+ currencies.
<br><br>And PRO version also gives you ability to use short tags [woocurrency]AMOUNT[/woocurrency] anywhere in product description to show any amount in site visitor currency.
<br><br>
<a href="https://www.plugins.monster/pmcurrencyforwc" target=_blank><img src="<?php echo(plugin_dir_url( __FILE__ )); ?>/woocurrencyprobanner.png" style="height:auto;max-width:100%;border:none;display:block;" alt="Upgrade To Pro Today"></a>
<br><br>
Checkout other useful <a href="https://www.plugins.monster" target=_blank>free Wordpress plugins</a> by Plugins Monster team.
<br><br>
<strong>Did this plugin help you?</strong> <br>If yes and you are willing & capable, you can <a href="https://www.plugins.monster/donate" target=_blank>donate to help</a> our all women group keep running & creating helpful free plugins.
<br><br>
For any help or customization in the plugin, feel free to email us at support@plugins.monster<br>
We also code custom plugins plus help you get your own branded plugins added on Wordpress repository.
<br><br>
<strong>Plugin not working?</strong><br>Its mostly caused when our plugin web service API blocks the IP address.<br>Please <a href="<?php echo esc_url($errcheckurl); ?>">click here</a> and once this page refreshes, please check the below area for error check information and contact us accordingly if needed. 
<?php
if($_GET['errcheck']=="yes")
{
$pmcargs = array(
        'timeout'     => 5,
	    'sslverify' => false,
	    'httpversion' => '1.0',
        'headers'     => array('referer' => home_url()),
); 
$pmcapidata = wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$_SERVER['REMOTE_ADDR'], $pmcargs);
$pmcapidatajson = wp_remote_retrieve_body( $pmcapidata );
$woocurrencydata_raw=@json_decode($pmcapidatajson);
echo "<br><br><h3 align=center>Error Check Information</h3>";
if($woocurrencydata_raw->geoplugin_status=="200")
echo "<br>There is no issue with plugin API, please contact support@plugins.monster for further help.<br>";
else
echo "<br>There is an issue with plugin API, error message is displayed below, please contact support@plugins.monster with below error message for further help.<br><br><i>".esc_html($woocurrencydata_raw->geoplugin_message)."</i>";	
}
?>
</div>
<p align="center"><a href="https://www.plugins.monster/pmcurrencyforwc" target=_blank><img src="<?php echo(plugin_dir_url( __FILE__ )); ?>/logo.png"></a></p>
<?php
}

function woocurrencybypluginsmonster_in_content_pdesc($content) {
global $wpdb;
$myrow212 = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."woocurrencybypluginsmonster" );

$pmcargs = array(
        'timeout'     => 5,
	    'sslverify' => false,
	    'httpversion' => '1.0',
        'headers'     => array('referer' => home_url()),
); 
$pmcapidata = wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$_SERVER['REMOTE_ADDR']."&base_currency=".$myrow212->base_currency, $pmcargs);
$pmcapidatajson = wp_remote_retrieve_body( $pmcapidata );
$woocurrencydata_raw=@json_decode($pmcapidatajson);
$woocurrencydata_currencycode=$woocurrencydata_raw->geoplugin_currencyCode;
$woocurrencydata_currencysymbol=$woocurrencydata_raw->geoplugin_currencySymbol;
$woocurrencydata_currencyconverter=$woocurrencydata_raw->geoplugin_currencyConverter;

//tags logic starts
if (!function_exists('pmcurrencyforwc_get_string_between'))   {
function pmcurrencyforwc_get_string_between($string, $start, $end)
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
}

$wpvinfotag1 = substr_count($content,"[woocurrency]");
$i = $wpvinfotag1 + 1;
$i2 = 1;
while($i2<$i)
{
$popcheckstart = '[woocurrency]';
$popcheckend = '[/woocurrency]';

$fullstring = $content;
$parsed = pmcurrencyforwc_get_string_between($fullstring, $popcheckstart, $popcheckend);

$wpcval = $parsed*$woocurrencydata_currencyconverter;
$wpcval = number_format((float)$wpcval, 2, '.', '');
$wpcdata = $woocurrencydata_currencysymbol.$wpcval." (".$woocurrencydata_currencycode.")";
$toreplaceval = $popcheckstart.$parsed.$popcheckend;

$content = str_replace($toreplaceval,$wpcdata,$content); 
$i2 = $i2 + 1;
}


return($content);

}

add_filter('woocommerce_short_description', 'woocurrencybypluginsmonster_in_content_pdesc');
add_filter('the_content', 'woocurrencybypluginsmonster_in_content_pdesc');


function woocurrencybypluginsmonster_in_content_price($price) {
global $wpdb;
global $product;

if( is_shop() || is_product())
{
$myrow212 = $wpdb->get_row( "SELECT * FROM ".$wpdb->prefix."woocurrencybypluginsmonster" );

$pmcargs = array(
        'timeout'     => 5,
	    'sslverify' => false,
	    'httpversion' => '1.0',
        'headers'     => array('referer' => home_url()),
); 
$pmcapidata = wp_remote_get("http://www.geoplugin.net/json.gp?ip=".$_SERVER['REMOTE_ADDR']."&base_currency=".$myrow212->base_currency, $pmcargs);
$pmcapidatajson = wp_remote_retrieve_body( $pmcapidata );
$woocurrencydata_raw=@json_decode($pmcapidatajson);

$woocurrencydata_currencycode=$woocurrencydata_raw->geoplugin_currencyCode;
$woocurrencydata_currencysymbol=$woocurrencydata_raw->geoplugin_currencySymbol;
$woocurrencydata_currencyconverter=$woocurrencydata_raw->geoplugin_currencyConverter;


//$product = wc_get_product(get_the_ID());
$thePrice = $product->get_price(); //will give raw price
    
$wpcval = $thePrice*$woocurrencydata_currencyconverter;
$wpcval = number_format((float)$wpcval, 2, '.', '');
$wpcdata = $woocurrencydata_currencysymbol.$wpcval." (".$woocurrencydata_currencycode.")";

$price = $price." (Approx. ".$wpcdata.")";
}
return($price);

}

add_filter('woocommerce_get_price_html', 'woocurrencybypluginsmonster_in_content_price');

function woocurrencybypluginsmonster_create_set_tables()
{
global $wpdb;

$sql1  = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."woocurrencybypluginsmonster (";
	$sql1 .= "   `datanumber` int NOT NULL AUTO_INCREMENT,";
	$sql1 .= "   `base_currency` varchar(100),";
	$sql1 .= "   PRIMARY KEY  (`datanumber`)";
	$sql1 .= " ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


$wpdb->query($sql1);

$sql11 = "INSERT INTO ".$wpdb->prefix."woocurrencybypluginsmonster (base_currency) VALUES ('USD')";
$wpdb->query($sql11);

}

register_activation_hook( __FILE__, 'woocurrencybypluginsmonster_create_set_tables' );

function woocurrencybypluginsmonster_del_tables()
{
global $wpdb;

$sql2 = "DROP TABLE ".$wpdb->prefix."woocurrencybypluginsmonster";

$wpdb->query($sql2);

}

register_deactivation_hook( __FILE__, 'woocurrencybypluginsmonster_del_tables' );


?>
