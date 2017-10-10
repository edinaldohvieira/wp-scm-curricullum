<?php
/**
 * Plugin Name:     WP SCM CURRICULLUM
 * Plugin URI:      https://github.com/edinaldohvieira/wp-scm-curricullum
 * Description:     Exibe uma listagem, busca e detalhes dos CURRICULLUMs publicados por cada usuário
 * Author:          Edinaldo H Vieira
 * Author URI:      https://github.com/edinaldohvieira
 * Text Domain:     wp-scm-curricullum
 * Domain Path:     /languages
 * Version:         0.10
 * Charge log:      v0.10 - Inicio de configuração do historico curricular. 
 *
 * @package         Wp_Scm_Curricullum
 */


if ( ! defined( 'ABSPATH' ) ) { exit; }
add_filter('widget_text', 'do_shortcode');
$api_url = 'http://idados.xyz/update/';
$plugin_slug = basename(dirname(__FILE__));
// die($plugin_slug);
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
			'key' => 'scm_uf',
			'value' => $uf,
			'compare' => 'LIKE'
		);
	}



	$args  = array(
		// 'role' => 'consultor',
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

		$ramo_atividade = get_user_meta( $user->ID, 'ramo_atividade', false );
		$experiencia = get_user_meta( $user->ID, 'experiencia', true );
		$scm_uf = get_user_meta( $user->ID, 'scm_uf', true );
		$trab_realizados = get_user_meta( $user->ID, 'trab_realizados', true );
		
		$href = '<a href="#"></a>';

		$first_name = get_user_meta( $user->ID, 'first_name', true );
		$last_name = get_user_meta( $user->ID, 'last_name', true );
		if($first_name) $name = $first_name." ".$last_name;
		if(!$name) $name = $user->display_name; 
		// if(!$first_name) $name = $user->user_login; 
		


		// echo '<pre>';
		// print_r($user);
		// echo '</pre>';
		?>
		<div class="scm066_list_row">
			<div class="">
				<div class="">
					<div class="border_externa3">
						<div class="border_externa2">
							<div class="border_externa1">
								<a href="<?php echo $path_det; ?>">
									<!--img class="s066l_foto" src="<?php echo $path_foto ?>"-->
									<?php echo get_avatar($user->ID) ?> 
								</a>
								
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="scm066_list_col_detalhe" >
				<div class="s066l_title" style="border:solid 0px gray;">
					<div><a href="<?php echo $path_det; ?>?id=<?php echo $user->ID; ?>"><?php echo esc_html( $name ) ?> <img class="s066l_estrelas" src="<?php echo $path_star ?>"></a></div>
				</div>
				<div class="s066l_detalhe">
					<div>Ramo de Atividade: <span style="color:#000000;">
					<?php 
					foreach ($ramo_atividade as $key => $value) {
						foreach ($value as $key2 => $value2) {
							echo '<strong>'.$value2.'</strong>';
							echo ' ';
						}
					}
					
					?>
						
					</span></div>
					<div>Experiência: <span style="color:#000000;"><strong><?php echo $experiencia; ?></strong></span></div>
					<div>Estado: <span style="color:#000000;"><strong><?php echo $scm_uf; ?></strong></span></div>
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

	$first_name = get_user_meta( $id, 'first_name', true );
	$last_name = get_user_meta( $id, 'last_name', true );
	if($first_name) $name = $first_name." ".$last_name;
	if(!$first_name) $name = $user->display_name; 

	?>


<div class="scm066_det_container">
	<div class="scm066_det_col_1">
		<div >
			<!--img class="s066l_foto" src="<?php echo $path_foto ?>"-->
			<?php echo get_avatar($user->ID, 200) ?> 
		</div>
		<div style="text-align: center;padding-bottom: 30px;">
			<img class="s066l_estrelas" src="<?php echo $path_star ?>">
		</div>
		<div style="padding-bottom: 30px;">
			<div><i>Ramo de Atividade:</i></div>
			<div style="color:#000000;">
			<?php $ramo_atividade =  get_user_meta( $user->ID, 'ramo_atividade', false ); ?>


					<?php 
					foreach ($ramo_atividade as $key => $value) {
						foreach ($value as $key2 => $value2) {
							echo '<strong>'.$value2.'</strong>';
							echo ' ';
						}
					}
					
					?>

				
			</div>
		</div>
		<div style="padding-bottom: 30px;">
			<div>UF:</div>
			<div style="color:#000000;"><strong><?php echo get_user_meta( $user->ID, 'scm_uf', true ); ?></strong></div>
		</div>
		<div style="padding-bottom: 30px;">
			<div>Experiência:</div>
			<div style="color:#000000;"><strong><?php echo get_user_meta( $user->ID, 'experiencia', true ); ?></strong></div>
		</div>
	</div>
	<div class="scm066_det_col_2">
		<div class="scm066_det_title_conteiner">
			<div class="scm066_det_title_col_1" style="font-size: 130%;padding: 0 10px;color:#000000;"><strong><?php echo $name ?></strong></div>
			<div class="scm066_det_title_col_2"><img class="s066l_medalha" src="<?php echo $path_medalha ?>"></div>
		</div>

		<div class="det_curricullum">
			<?php echo do_shortcode('[scm_pt_curricullum_list user_id='.$user->ID.']') ?>
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



function scm075_enqueue_scripts() {
	wp_enqueue_style( 'scm075', plugins_url('css/style-0.1.0.css',__FILE__ ), '1.2.0' );
}
add_action( 'wp_enqueue_scripts', 'scm075_enqueue_scripts', 999 );




function wp_scm_curricullum_user_panel($atts, $content = null){
	// extract(shortcode_atts(array(
	// 	"on_op" => '',
	// ), $atts));


}
add_shortcode("wp_scm_curricullum_user_panel", "wp_scm_curricullum_user_panel");


function wp_scm_curricullum_admin_panel($atts, $content = null){
	// extract(shortcode_atts(array(
	// 	"on_op" => '',
	// ), $atts));


}
add_shortcode("wp_scm_curricullum_admin_panel", "wp_scm_curricullum_admin_panel");





function curricullums_post_type() {
	$labels = array(
		'name'                  => _x( 'Curricullum', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Curricullum', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Curricullum', 'text_domain' ),
		'name_admin_bar'        => __( 'Curricullum', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'attributes'            => __( 'Item Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Curricullum:', 'text_domain' ),
		'all_items'             => __( 'All Curricullum', 'text_domain' ),
		'add_new_item'          => __( 'Add New Curricullum', 'text_domain' ),
		'add_new'               => __( 'New Curricullum', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Curricullum', 'text_domain' ),
		'update_item'           => __( 'Update Curricullum', 'text_domain' ),
		'view_item'             => __( 'View Curricullum', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search curricullums', 'text_domain' ),
		'not_found'             => __( 'No curricullums found', 'text_domain' ),
		'not_found_in_trash'    => __( 'No curricullums found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Curricullum', 'text_domain' ),
		'description'           => __( 'Curricullum information pages.', 'text_domain' ),
		'labels'                => $labels,
		// 'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields', ),
		'supports'              => array( 'title','author','editor' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
	);
	register_post_type( 'curricullum', $args );
}
add_action( 'init', 'curricullums_post_type', 0 );




function cv_metabox1_add(){
	add_meta_box( 
		'cv_metabox1_id', 
		'Opões', 
		'cv_metabox1_fnc', 
		'curricullum', 
		'normal', //'normal', 'side', and 'advanced'
		'high' // 'high', 'low'
	);
}
add_action( 'add_meta_boxes', 'cv_metabox1_add' );

/*
	<p>
		<label for="texto_meta_box">Text Label</label><br>
		<input type="text" name="texto_meta_box" id="texto_meta_box" />
	</p>
	<p>
		<input type="checkbox" name="meta_box_check" id="meta_box_check" <?php checked( $check, 'on' ); ?> />
		<label for="meta_box_check">Don't Check This.</label>
	</p>

*/
function cv_metabox1_fnc(){
	$values = get_post_custom( $post->ID );
	// $text = isset( $values['texto_meta_box'] ) ? esc_attr( $values['texto_meta_box'][0] ) : '';
	$selected = isset( $values['cv_metabox1_select1'] ) ? esc_attr( $values['cv_metabox1_select1'][0] ) : '';
	// $check = isset( $values['meta_box_check'] ) ? esc_attr( $values['meta_box_check'][0] ) : '';
	wp_nonce_field( 'cv_metabox1_nonce', 'meta_box_nonce' );
	?>
	<p>
		<label for="cv_metabox1_select1">Secção</label><br>
		<select name="cv_metabox1_select1" id="cv_metabox1_select1" style="width:100%;">
			<option value="formacao_academica" <?php selected( $selected, 'formacao_academica' ); ?>>001 - Formação academica</option>
			<option value="especialidades" <?php selected( $selected, 'especialidades' ); ?>>002 - Principais Atribuições e Especialidades</option>
			<option value="conhecimento_tecnico" <?php selected( $selected, 'conhecimento_tecnico' ); ?>>003 - Conhecimento Técnico</option>
		</select>
	</p>
	<?php
}

add_action( 'save_post', 'cv_metabox1_save' );
function cv_metabox1_save( $post_id ){
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'cv_metabox1_nonce' ) ) return;
	if( !current_user_can( 'edit_post' ) ) return;

	$allowed = array(
		'a' => array(
		'href' => array()
	));
	// if( isset( $_POST['texto_meta_box'] ) )
	// update_post_meta( $post_id, 'texto_meta_box', wp_kses( $_POST['texto_meta_box'], $allowed ) );

	if( isset( $_POST['cv_metabox1_select1'] ) )
	update_post_meta( $post_id, 'cv_metabox1_select1', esc_attr( $_POST['cv_metabox1_select1'] ) );

	// $chk = ( isset( $_POST['meta_box_check'] ) && $_POST['meta_box_check'] ) ? 'on' : 'off';
	// update_post_meta( $post_id, 'meta_box_check', $chk );
}



/*

//ADICIONANDO O META BOX
add_action( 'add_meta_boxes', 'cv_metabox1_add' );
function cv_metabox1_add()
{
add_meta_box( 'cv_metabox1_id', 'Meu primeiro Meta Box', 'cv_metabox1_fnc', 'post', 'normal', 'high' );
}

//FORMULARIO PARA SALVAS OS DADOS
function cv_metabox1_fnc()
{
$values = get_post_custom( $post->ID );
$text = isset( $values['texto_meta_box'] ) ? esc_attr( $values['texto_meta_box'][0] ) : '';
$selected = isset( $values['cv_metabox1_select1'] ) ? esc_attr( $values['cv_metabox1_select1'][0] ) : '';
$check = isset( $values['meta_box_check'] ) ? esc_attr( $values['meta_box_check'][0] ) : '';
wp_nonce_field( 'cv_metabox1_nonce', 'meta_box_nonce' );
?>
<p>
<label for="texto_meta_box">Text Label</label>
<input type="text" name="texto_meta_box" id="texto_meta_box" />
</p>
<p>
<label for="cv_metabox1_select1">Color</label>
<select name="cv_metabox1_select1" id="cv_metabox1_select1">
<option value="red" <?php selected( $selected, 'red' ); ?>>Vermelho</option>
<option value="blue" <?php selected( $selected, 'blue' ); ?>>Azul</option>
</select>
</p>
<p>
<input type="checkbox" name="meta_box_check" id="meta_box_check" <?php checked( $check, 'on' ); ?> />
<label for="meta_box_check">Don't Check This.</label>
</p>
<?php
}

	add_action( 'save_post', 'cv_metabox1_save' );
	function cv_metabox1_save( $post_id ){
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'cv_metabox1_nonce' ) ) return;

		if( !current_user_can( 'edit_post' ) ) return;

		$allowed = array(
			'a' => array(
			'href' => array()
		)
	);

	if( isset( $_POST['texto_meta_box'] ) )
	update_post_meta( $post_id, 'texto_meta_box', wp_kses( $_POST['texto_meta_box'], $allowed ) );

	if( isset( $_POST['cv_metabox1_select1'] ) )
	update_post_meta( $post_id, 'cv_metabox1_select1', esc_attr( $_POST['cv_metabox1_select1'] ) );

	$chk = ( isset( $_POST['meta_box_check'] ) && $_POST['meta_box_check'] ) ? 'on' : 'off';
	update_post_meta( $post_id, 'meta_box_check', $chk );
}

*/

function scm_pt_curricullum_list($atts, $content = null){
	extract(shortcode_atts(array(
		"user_id" => '',
	), $atts));

	$ret = '';

	
	$ret .= '<div style="padding:0 10px;">';
	$ret .= '<h4 style="margin:0px;">HISTÓRICO CURRICULAR</h4>';
	$ret .= '</div>';
	
	// HISTÓRICO CURRICULAR - ini
	$args = array(
		'posts_per_page' => 20,
		'post_type' 	=> 'curricullum',
		'orderby'   	=> 'date',
		'order'     	=> 'DESC',
		'author'		=> $user_id,
		'meta_key'		=> 'cv_metabox1_select1',
		'meta_value'	=> 'formacao_academica',
		 // 's' => $nome
	);
	$cv = get_posts( $args );

	$ret .= '<div style="height:20px;"></div>';
	$ret .= '<div style="padding:0 10px;border-bottom:1px solid #000000;"><strong>FORMAÇÃO ACADÊMICA</strong></div>';
	foreach ($cv as $key => $value) {
		$titulo = $value->post_title;
		$descricao = $value->post_content;

		$ret .= '<div style="padding:0 10px;">';
		$ret .= '<strong>'.$titulo.'</strong>';
		$ret .= '<br>';
		$ret .= $descricao;
		$ret .= '<br>';
		$ret .= '<br>';
		$ret .= '</div>';
	}
	// HISTÓRICO CURRICULAR - end



	// HISTÓRICO CURRICULAR - ini
	$args = array(
		'posts_per_page' => 20,
		'post_type' 	=> 'curricullum',
		'orderby'   	=> 'date',
		'order'     	=> 'DESC',
		'author'		=> $user_id,
		'meta_key'		=> 'cv_metabox1_select1',
		'meta_value'	=> 'especialidades',
		 // 's' => $nome
	);
	$cv = get_posts( $args );

	$ret .= '<div style="height:20px;"></div>';
	$ret .= '<div style="padding:0 10px;border-bottom:1px solid #000000; "><strong>PRINCIPAIS ATRIBUIÇÕES E ESPECIALIDADES</strong></div>';
	foreach ($cv as $key => $value) {
		$titulo = $value->post_title;
		$descricao = $value->post_content;

		$ret .= '<div style="padding:0 10px;">';
		$ret .= '<strong>'.$titulo.'</strong>';
		$ret .= '<br>';
		$ret .= $descricao;
		$ret .= '<br>';
		$ret .= '<br>';
		$ret .= '</div>';
	}
	// HISTÓRICO CURRICULAR - end
	return $ret;
}
add_shortcode("scm_pt_curricullum_list", "scm_pt_curricullum_list");


/*

$args = array(
	'posts_per_page'   => 5,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => '',
	'orderby'          => 'date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'post',
	'post_mime_type'   => '',
	'post_parent'      => '',
	'author'	   => '',
	'author_name'	   => '',
	'post_status'      => 'publish',
	'suppress_filters' => true 
);
$posts_array = get_posts( $args ); 



Array
(
    [0] => WP_Post Object
        (
            [ID] => 72
            [post_author] => 1
            [post_date] => 2017-10-10 08:31:15
            [post_date_gmt] => 2017-10-10 11:31:15
            [post_content] => 
sdsdsds

sdsdsds


            [post_title] => sdsds
            [post_excerpt] => 
            [post_status] => publish
            [comment_status] => open
            [ping_status] => closed
            [post_password] => 
            [post_name] => sdsds
            [to_ping] => 
            [pinged] => 
            [post_modified] => 2017-10-10 08:31:15
            [post_modified_gmt] => 2017-10-10 11:31:15
            [post_content_filtered] => 
            [post_parent] => 0
            [guid] => http://localhost/075-scm-curricullum/curricullum/sdsds/
            [menu_order] => 0
            [post_type] => curricullum
            [post_mime_type] => 
            [comment_count] => 0
            [filter] => raw
        )

)

*/