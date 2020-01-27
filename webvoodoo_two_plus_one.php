<?php 
/**
 * Plugin Name: Sale 2+1 Woocommerce
 * Description: Buy 3 products and get one in present - the cheepest of them
 * Author URI:  https://seolab.dp.ua
 * Author:      Webvoodoo
 * Version:     1.0
 * WC requires at least: 3.8.0
 * WC tested up to: 3.8.0
 */
require __DIR__ . "/functions.php";
//запускаем пересчет цен на товары в корзине в момент добавления их в нее
add_action( 'woocommerce_before_calculate_totals', 'webvoodoo_add_custom_price');
//добавление меню плагина в админ панель
add_action( 'admin_menu', 'webvoodoo_sale_admin_menu');
//вывод сообщения администратору об активации Woocoommerce
add_action('admin_notices', 'webvoodoo_sale_settings_error_notice');
//хуки при активации и деактивации плагина
register_activation_hook(__FILE__, "webvoodoo_sale_install");
register_deactivation_hook(__FILE__, "webvoodoo_sale_uninstall");
//создаем таблицу в бд, если плагин активирован
function webvoodoo_sale_install(){
	global $wpdb;
	$table_docs = $wpdb->prefix."webvoodoo_sale_plugin";
	if($wpdb->get_var( "SHOW TABLES LIKE '$table_docs'" ) != $table_docs ) {
		$sql = "CREATE TABLE IF NOT EXISTS $table_docs (			  
				  `id` VARCHAR(250) UNIQUE,
				  `name` INT NULL,			  
				  PRIMARY KEY (`id`))
				ENGINE = InnoDB DEFAULT CHARSET=utf8;";
		$wpdb->query($sql);
	}
}
//удаляем таблицу из бд, если плагин деактивирован
function webvoodoo_sale_uninstall(){
	global $wpdb;
	$table_docs = $wpdb->prefix."webvoodoo_sale_plugin";
	$sql = "DROP TABLE `" .$table_docs . "`;";
	$wpdb->query($sql);
}
function webvoodoo_sale_settings_error_notice() {
    global $wpdb;
    $page_setup = $wpdb->prefix."webvoodoo_sale_plugin";
    $key = $wpdb->get_row("SELECT `id`, `name` FROM `" . $page_setup . "` ;");
    if($key->id != "webvoodoo_sale_product"):?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( 'Укажите число товаров, участвующих в акции в настройках плагина!', 'webvoodoo_sale_woocommerce' ); ?></p>
        </div>
    <?php endif;
}





