<?php
/**
 * Plugin Name:     WP SCM CURRICULLUM
 * Plugin URI:      https://github.com/edinaldohvieira/wp-scm-curricullum
 * Description:     Exibe uma listagem, busca e detalhes dos CURRICULLUMs publicados por cada usuário
 * Author:          Edinaldo H Vieira
 * Author URI:      https://github.com/edinaldohvieira
 * Text Domain:     wp-scm-curricullum
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Wp_Scm_Curricullum
 */


if ( ! defined( 'ABSPATH' ) ) { exit; }
add_filter('widget_text', 'do_shortcode');
$api_url = 'http://idados.xyz/update/';
$plugin_slug = basename(dirname(__FILE__));
add_filter('pre_set_site_transient_update_plugins', 'scm075_check_for_plugin_update');
function scm075_check_for_plugin_update($checked_data) {
global $api_url, $plugin_slug, $wp_version;
if (empty($checked_data->checked)) return $checked_data;
$args = array('slug' => $plugin_slug,'version' => $checked_data->checked[$plugin_slug .'/'. $plugin_slug .'.php'],);
$request_string = array('body' => array('action' => 'basic_check', 'request' => serialize($args),'api-key' => md5(get_bloginfo('url'))),'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'));
$raw_response = wp_remote_post($api_url, $request_string);
if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) $response = unserialize($raw_response['body']);
if (is_object($response) && !empty($response)) $checked_data->response[$plugin_slug .'/'. $plugin_slug .'.php'] = $response;
return $checked_data;}
add_filter('plugins_api', 'scm075_plugin_api_call', 10, 3);
function scm075_plugin_api_call($def, $action, $args) {
global $plugin_slug, $api_url, $wp_version;
if (!isset($args->slug) || ($args->slug != $plugin_slug)) return false;
$plugin_info = get_site_transient('update_plugins');
$current_version = $plugin_info->checked[$plugin_slug .'/'. $plugin_slug .'.php'];
$args->version = $current_version;
$request_string = array('body' => array('action' => $action, 'request' => serialize($args),'api-key' => md5(get_bloginfo('url'))),'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'));
$request = wp_remote_post($api_url, $request_string);
if (is_wp_error($request)) {$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
} else {$res = unserialize($request['body']);
if ($res === false) $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);}
return $res;}



function wp_scm_curricullum_list($atts, $content = null){
	extract(shortcode_atts(array(
		"on_op" => '',
		"path_det" => '__site_url__/curriculum/'
	), $atts));

	$get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';if($on_op) {if($on_op=="empty"){if($get_url_if_op) return '';}else{if(!$get_url_if_op)  return '';if($get_url_if_op<>$on_op) return '';}}


	$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
	$ramo = isset($_GET['ramo']) ? $_GET['ramo'] : '';
	$uf = isset($_GET['uf']) ? $_GET['uf'] : '';
	

	$nome =  array();
	if($busca){
		$nome[] = array(
			'key' => 'first_name',
			'value' => $busca,
			'compare' => 'LIKE'
		);
	}
	if($ramo){
		$nome[] = array(
			'key' => 'ramo_atividade',
			'value' => $ramo,
			'compare' => 'LIKE'
		);
	}
	if($uf){
		$nome[] = array(
			'key' => 's066_uf',
			'value' => $uf,
			'compare' => 'LIKE'
		);
	}



	$args  = array(
		'role' => 'consultor',
		'orderby' => 'first_name',
		'meta_query' => array(
			'relation' => 'OR',
			$nome
		)
	);
	$wp_user_query = new WP_User_Query($args);
	$authors = $wp_user_query->get_results();


	if (empty($authors)){
	    echo '---NADA ENCONTRADO---';
	}


	$path_det = preg_replace("/__site_url__/",site_url() , $path_det);
	echo '<div class="scm066_list_content">';
	foreach ( $authors as $user ) {

		$path_star = plugins_url( 'images/0_estrelas.png', __FILE__ );
		$scm066_estrelas = get_user_meta( $user->ID, 'scm066_estrelas', true );
		if($scm066_estrelas) $path_star = plugins_url( 'images/'.$scm066_estrelas.'_estrelas.png', __FILE__ );

		$path_medalha = plugins_url( 'images/nivel_start.png', __FILE__ );
		$scm066_nivel = get_user_meta( $user->ID, 'scm066_nivel', true );
		if($scm066_nivel) $path_medalha = plugins_url( 'images/nivel_'.$scm066_nivel.'.png', __FILE__ );


		$path_foto = plugins_url( 'images/thumbnail_default-1.png', __FILE__ );

		$ramo_atividade = get_user_meta( $user->ID, 'ramo_atividade', true );
		$experiencia = get_user_meta( $user->ID, 'experiencia', true );
		$s066_uf = get_user_meta( $user->ID, 's066_uf', true );
		$trab_realizados = get_user_meta( $user->ID, 'trab_realizados', true );
		
		$href = '<a href="#"></a>';
		?>
		<div class="scm066_list_row">
			<div class="">
				<div class="">
					<div class="border_externa3">
						<div class="border_externa2">
							<div class="border_externa1">
								<a href="<?php echo $path_det; ?>"><img class="s066l_foto" src="<?php echo $path_foto ?>"></a>
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="scm066_list_col_detalhe" >
				<div class="s066l_title">
					<div><a href="<?php echo $path_det; ?>?id=<?php echo $user->ID; ?>"><?php echo esc_html( get_user_meta( $user->ID, 'first_name', true ). " ".get_user_meta( $user->ID, 'last_name', true ) ) ?> <img class="s066l_estrelas" src="<?php echo $path_star ?>"></a></div>
				</div>
				<div class="s066l_detalhe">
					<div>Ramo de Atividade: <span style="color:#000000;"><?php echo $ramo_atividade; ?></span></div>
					<div>Experiência: <span style="color:#000000;"><?php echo $experiencia; ?></span></div>
					<div>Estado: <span style="color:#000000;"><?php echo $s066_uf; ?></span></div>
					<div>Trabalhos realizados: <span style="color:#000000;"><?php echo $trab_realizados; ?></span></div>
				</div>
			</div>
			<div class="scm066_list_col_medalha" >
				<img class="s066l_medalha" src="<?php echo $path_medalha ?>">
			</div>
		</div>
		<?php 
	}
	echo '</div>';
}
add_shortcode("wp_scm_curricullum_list", "wp_scm_curricullum_list");


function wp_scm_curricullum_det($atts, $content = null){
	extract(shortcode_atts(array(
		"on_op" => ''
	), $atts));

	$get_url_if_op = isset($_GET['op']) ? $_GET['op'] : '';
	if($on_op) {
		if($on_op=="empty"){
			if($get_url_if_op) return '';
		}else{
			if(!$get_url_if_op)  return '';
			if($get_url_if_op<>$on_op) return '';
		}
	}
	$id = isset($_GET['id']) ? $_GET['id'] : 0;
	$user = get_user_by('id', $id);
	$user_meta = get_user_meta( $id );
	$path_foto = plugins_url( 'images/thumbnail_default-1.png', __FILE__ );


	$path_star = plugins_url( 'images/0_estrelas.png', __FILE__ );
	$scm066_estrelas = get_user_meta( $user->ID, 'scm066_estrelas', true );
	if($scm066_estrelas) $path_star = plugins_url( 'images/'.$scm066_estrelas.'_estrelas.png', __FILE__ );


	$path_medalha = plugins_url( 'images/nivel_start.png', __FILE__ );
	$scm066_nivel = get_user_meta( $id, 'scm066_nivel', true );
	if($scm066_nivel) $path_medalha = plugins_url( 'images/nivel_'.$scm066_nivel.'.png', __FILE__ );

?>

<?php //echo esc_html( get_user_meta( $id, 'first_name', true ). " ".get_user_meta( $user->ID, 'last_name', true ) ) ?>
<div class="scm066_det_container">
	<div class="scm066_det_col_1">
		<div >
			<img class="s066l_foto" src="<?php echo $path_foto ?>">
		</div>
		<div style="text-align: center;padding-bottom: 30px;">
			<img class="s066l_estrelas" src="<?php echo $path_star ?>">
		</div>
		<div style="padding-bottom: 30px;">
			<div><i>Ramo de Atividade:</i></div>
			<div style="color:#000000;"><?php echo get_user_meta( $user->ID, 'ramo_atividade', true ); ?></div>
		</div>
		<div style="padding-bottom: 30px;">
			<div>UF:</div>
			<div style="color:#000000;"><?php echo get_user_meta( $user->ID, 's066_uf', true ); ?></div>
		</div>
		<div style="padding-bottom: 30px;">
			<div>Experiência:</div>
			<div style="color:#000000;"><?php echo get_user_meta( $user->ID, 'experiencia', true ); ?></div>
		</div>
	</div>
	<div class="scm066_det_col_2">
		<div class="scm066_det_title_conteiner">
			<div class="scm066_det_title_col_1" style="font-size: 130%;padding: 0 10px;color:#000000;"><strong><?php echo esc_html( get_user_meta( $id, 'first_name', true ). " ".get_user_meta( $user->ID, 'last_name', true ) ) ?></strong></div>
			<div class="scm066_det_title_col_2"><img class="s066l_medalha" src="<?php echo $path_medalha ?>"></div>
		</div>

		<div class="det_curricullum">
		
		</div>
	</div>
</div>


<?php 
}
add_shortcode("wp_scm_curricullum_det", "wp_scm_curricullum_det");




function wp_scm_curricullum_busca($atts, $content = null){
	extract(shortcode_atts(array(
		"on_op" => '',
		"url" => '__site_url__/curricullums/',
		"title" => '',
		"placeholder" => 'nome',
		"label_submit" => 'BUSCAR'
	), $atts));

	// get_bloginfo('url')
	$url = preg_replace("/__site_url__/",site_url() , $url);
	$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
	$ramo = isset($_GET['ramo']) ? $_GET['ramo'] : '';
	$uf = isset($_GET['uf']) ? $_GET['uf'] : '';

	?>
	<form style="padding:10px; border: 1px solid silver;" action="<?php echo $url; ?>" method="GET" role="form">
	<div class="">
		<?php echo $title; ?>
		<div style="padding-bottom: 20px;border:0px solid silver;">
			<label for="busca">Nome:</label><br>
			<input style="text-align: center;width:100%;padding: 5px;" type="text" class="" name="busca" placeholder="" value="<?php echo $busca ?>" autocomplete="off">
		</div>
		<div style="padding-bottom: 20px;border:0px solid silver;">
			<label for="ramo">Ramo de Atividade:</label><br>
			<select name="ramo" style="text-align: center;width:100%;padding: 5px;">
				<option value="">-- selecione --</option>
				<option value="arquiteto" <?php if($ramo=='arquiteto') echo 'selected'; ?> >ARQUITETO</option>
				<option value="padeiro" <?php if($ramo=='padeiro') echo 'selected'; ?> >PADEIRO</option>
			</select>
		</div>
		<div style="padding-bottom: 20px;border:0px solid silver;">
			<label for="uf">UF:</label><br>
			<input style="text-align: center;width:100%;padding: 5px;" type="text" class="" name="uf" placeholder="" value="<?php echo $uf ?>" autocomplete="off">
		</div>
	</div>
	<button style="width: 100%;color:gray;height: 60px;font-size: 130%;color: #000000;" type="submit"><strong><?php echo $label_submit ?></strong></button>
	<div style="text-align: center;padding: 10px;"><a href="?" >LIMPAR</a></div>
</form>
	<?php 

}
add_shortcode("wp_scm_curricullum_busca", "wp_scm_curricullum_busca");
