<?php
/**
 * Plugin Name: WP Auth0 sync
 * Version: 0.0.1
 * Plugin URI:
 * Description:
 * Author: @cspilhere
 * Author URI:
 */

  include_once "_inc/helpers.php";

  include_once "admin/create-admin-interface.php";

  include_once "auth0-to-wp-sync/enable-external-link.php";

  include_once "wp-to-auth0-intercept/wp-to-auth0-intercept.php";


  add_action('wp_enqueue_scripts', 'Auth0Manager');
  function Auth0Manager() {
    wp_enqueue_script('Auth0Manager', plugin_dir_url( __FILE__ ) . '/Auth0Manager/scripts/bundle.js', null, true, false);
  }


  add_action('wp_footer', 'print_my_inline_script');
  function print_my_inline_script() {
  ?>
  <button id="logoutAuth0">LOGOUT</button>
  <script type="text/javascript">
    var newAuth0Manager = new Auth0Manager({
      domain: 'agriness-test.auth0.com',
      clientID: 's5WUmC8vVehyg3xUn8PgdGnWExjikhr9',
      queryToken: true
    });

    newAuth0Manager.init();

    document.querySelector('#logoutAuth0').addEventListener('click', function() {
      jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {
        'action': 'ajax_wp_auth0_logout'
      }, function(response) {
        newAuth0Manager.logout();
      });
    });

    newAuth0Manager.isLoggedIn(function(param) {
      console.log(param.auth0_userEmail);

      jQuery(document).ready(function() {
        jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {
    			'action': 'ajax_wp_auth0_login',
    			'user_email': param.auth0_userEmail
    		}, function(response) {
          if (!response) { location.reload(); }
    		});
      });

    }); // newAuth0Manager.isLoggedIn(function(param)
  </script>
  <?php
  }


  // Login User
  add_action('wp_ajax_nopriv_ajax_wp_auth0_login', 'ajax_wp_auth0_login');
  add_action('wp_ajax_ajax_wp_auth0_login', 'ajax_wp_auth0_login');
  function ajax_wp_auth0_login() {
    $user = get_wp_user_object($_POST['user_email']);
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true, false);
    if (is_user_logged_in()) {
      echo true;
    } else {
      echo false;
    }
    die();
  } // function ajax_wp_auth0_login


  // Logout User
  add_action('wp_ajax_nopriv_ajax_wp_auth0_logout', 'ajax_wp_auth0_logout');
  add_action('wp_ajax_ajax_wp_auth0_logout', 'ajax_wp_auth0_logout');
  function ajax_wp_auth0_logout() {
    wp_logout();
    wp_set_current_user(0);
    wp_clear_auth_cookie();
    echo 'etaetaeta';
    die();
  } // function ajax_wp_auth0_logout

?>
