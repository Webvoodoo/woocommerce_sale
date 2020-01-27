<?php
//пересчет цены при акции 2+1
function webvoodoo_add_custom_price($cart_object) {
	global $wpdb;
	$page_setup = $wpdb->prefix."webvoodoo_sale_plugin";
	$key = $wpdb->get_row("SELECT `id`, `name` FROM `" . $page_setup . "` ;");
	if($key->id == "webvoodoo_sale_product") {
		$data_product = [];
		//считаем количество товаров в корзине
		$count_products_in_cart = WC()->cart->get_cart_contents_count();
		//если товаров , то получем ид и цену всех товаров
		if ( $count_products_in_cart == $key->name ) {
			foreach ( $cart_object->cart_contents as $key => $value ) {
				//записываем в ассоц массив данные ид=>цена
				$data_product[ $value['data']->get_id() ] = $value['data']->get_price();
			}
			//берем ид товара с минимальной ценой
			$id_min_product = array_keys( $data_product, min( $data_product ) );
			foreach ( $cart_object->cart_contents as $key => $value ) {
				//если в ид товара в корзине совпадает с тем, что получили в строке №13
				//то меняем цену товара на 0, то есть он идет бесплатно
				if ($value['data']->get_id() == $id_min_product[0]) {
					$value['data']->set_price(0);
				} else continue;
			}
		}
	} else {
	    if(is_super_admin()) echo("Для работы акции укажите в настройках плагина число товаров!");
    }
}
//добавление меню плагина в админ панель
function webvoodoo_sale_admin_menu(){
	add_menu_page( 'Настроки акции 2+1',
		'Акция 2+1 Woocommerce',
		'administrator',
		'settings_sale_two_plus_one',
		'admin_woocommerce_sale_page');
}
//страница настроек акции 2+1
function admin_woocommerce_sale_page() {
	echo "<h2>Настройки акции</h2><p>Укажите число товаров(минимум 2):</p>";
	if (is_admin()):
		global $wpdb;
		$page_setup = $wpdb->prefix . "webvoodoo_sale_plugin";
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$page_setup'" ) == $page_setup ) {
			$get_all_data = $wpdb->query( "SELECT * FROM `" . $page_setup . "`;" );
		}
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			if (function_exists( 'check_admin_referrer')) {
				check_admin_referer( 'webvoodoo_admin_form' );
			}
			if ($get_all_data == 0) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO `$page_setup` (`id`, `name`) VALUES(%s , %s)", 'webvoodoo_sale_product', trim( strip_tags( $_POST['webvoodoo_sale_product'] ) ) ) );
			} else {
				$wpdb->query( $wpdb->prepare( "UPDATE `$page_setup` SET `name` = %s WHERE `id` = 'webvoodoo_sale_product';", trim( strip_tags( $_POST['webvoodoo_sale_product'] ) ) ) );
			}
		} ?>
		<form action="<?php echo $_SERVER['PHP_SELF'] . "?page=settings_sale_two_plus_one&amp;updated=true" ?>" method="POST" name="webvoodoo_admin_form">
			<?php
			if (function_exists( 'wp_nonce_field' )) {
				wp_nonce_field( 'webvoodoo_admin_form' );
			}
			$page_setup = $wpdb->prefix . "webvoodoo_sale_plugin";
			if ($wpdb->get_var( "SHOW TABLES LIKE '$page_setup'") == $page_setup) {
				$get_product_count = $wpdb->query( "SELECT COUNT(`name`) FROM `" . $page_setup . "`;");
			}
			if ($get_product_count == 1) {
				$get_str = $wpdb->get_var( "SELECT `name` FROM `" . $page_setup . "` WHERE `id` = 'webvoodoo_sale_product';" ); ?>
				<label>Акция действует при покупке: <b><?php echo $get_str . "</b></label><br/>";?>
			<?php
			} else { echo "<label>Акция действует при покупке: </label><br/>"; }?>
						<input type="number" name="webvoodoo_sale_product" id="webvoodoo_sale_setting" min="2"/><br/><br/>
						<input name="webvoodoo_submit" type="submit" class="button-primary" value="Сохранить"/>
		</form>
		<?php endif;
}

