<?php
/**
 * Welcome Page
 *
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly



if( ! class_exists( 'TIELABS_WELCOME_PAGE' ) ) {

	class TIELABS_WELCOME_PAGE{

		/**
		 * __construct
		 *
		 * Class constructor where we will call our filter and action hooks.
		 */
		function __construct(){

		}


		/**
		 * _head_section
		 *
		 * Show the Welcome Page head
		 */
		public static function _head_section( $current_tab = 'getting_started' ){

			$welcome_args = array(
				'title'   => sprintf( esc_html__( 'Welcome to %s', TIELABS_TEXTDOMAIN ), apply_filters( 'TieLabs/theme_name', 'TieLabs' ) ),
				'about'   => sprintf( esc_html__( 'You are awesome! Thanks for using our theme, %s is now installed and ready to use! Get ready to build something beautiful :)', TIELABS_TEXTDOMAIN ), apply_filters( 'TieLabs/theme_name', 'TieLabs' ) ),
				'color'   => apply_filters( 'TieLabs/default_theme_color', '#000' ),
				'img'     => TIELABS_TEMPLATE_URL .'/framework/admin/assets/images/tielabs-logo-mini.png',
				'version' => '',
			);

			$welcome_args = apply_filters( 'TieLabs/welcome_args', $welcome_args );

			$tabs = array();
			$tabs = apply_filters( 'TieLabs/about_tabs', $tabs );

			$item_url = tie_get_purchase_link( array( 'utm_source' => 'twitter', 'utm_medium' => 'installed-msg' ) );

			?>

			<h1><?php esc_html_e( $welcome_args['title'] ) ?></h1>


			<p class="about-text"><?php esc_html_e( $welcome_args['about'] ); ?>

				<a href="https://twitter.com/share" class="twitter-share-button"
				   data-url="<?php echo esc_url( $item_url ) ?>"
				   data-text="<?php printf( esc_html__( 'I just installed the amazing %s #WordPress theme #tielabs', TIELABS_TEXTDOMAIN ), apply_filters( 'TieLabs/theme_name', 'TieLabs' ) ); ?>"
				   data-via="tielabs" data-size="large">Tweet</a>
				<script>
					!function (d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (!d.getElementById(id)) {
							js = d.createElement(s);
							js.id = id;
							js.src = "//platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore(js, fjs);
						}
					}(document, "script", "twitter-wjs");
				</script>

			</p>
			<div class="tie-badge" style="background-color: <?php echo esc_attr( $welcome_args['color'] ); ?>;">
				<img src="<?php echo esc_attr( $welcome_args['img'] ); ?>" alt="" />
				<?php printf( esc_html__( 'Version %s', TIELABS_TEXTDOMAIN  ), TIELABS_DB_VERSION ); ?>
			</div>


			<h2 class="tie-nav-tab-wrapper nav-tab-wrapper wp-clearfix">
				<?php
				foreach ( $tabs as $key => $value ){
					if( ! empty( $value['url'] ) && ! empty( $value['text'] ) ){
						$class = ( $key == $current_tab ) ? 'nav-tab nav-tab-active button-primary' : 'nav-tab'; ?>
						<a href="<?php echo esc_url( $value['url'] ) ?>" class="<?php echo esc_attr( $class ) ?>"><?php esc_html_e( $value['text'] ); ?></a>
						<?php
					}
				}
				?>
			</h2>

			<?php
		}

	}


	// Instantiate the class
	new TIELABS_WELCOME_PAGE();

}
