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

  add_action('after_setup_theme', 'do_login_with_jwt', 0);
  function do_login_with_jwt() {
    if(!empty($_GET['idToken'])) {
      $wp_auth0_sync_options = get_option('wp_auth0_sync');
      $auth0_client = $wp_auth0_sync_options["wp_auth0_sync_client"];
      $url = "https://" . $auth0_client . ".auth0.com/tokeninfo?id_token=".$_GET['idToken'];
      $valid_result = auth0_curl_get($url, $_GET['idToken']);

      $user_email = json_decode($valid_result)->email;

      create_user_after_sync(json_decode($valid_result));

      $user = get_user_by( 'email', $user_email);
      $user_id = $user->id;
      wp_set_current_user($user->ID);
      wp_set_auth_cookie($user_id, false, false );
      //exit;
    }
  }

  add_action('admin_enqueue_scripts','jquery');
  function jquery() {
    wp_enqueue_script('jquery');
  }

  add_action('admin_print_scripts', 'WPAuth0SyncManual');
  function WPAuth0SyncManual() {
    ?>
    <script type="text/javascript">
    var everythingLoaded = setInterval(function() {
      if (/loaded|complete/.test(document.readyState)) {
        clearInterval(everythingLoaded);
        var loadingtimeout = setTimeout(function() {
          jQuery(document).ready(function() {
            document.querySelector('#WPAuth0SyncManual').addEventListener('click', function(event) {
              document.querySelector('#WPAuth0SyncManual').disabled = true;
              document.querySelector('#syncstatus').innerText = "Sincronizando...";
              jQuery.get("<?php echo get_site_url() . '/?WPAuth0Sync=sync'; ?>", function(response) {
                document.querySelector('#WPAuth0SyncManual').disabled = true;
                document.querySelector('#syncstatus').innerText = "Concluído: " + JSON.parse(response).total + " usuário(s) sincronizados.";
                setTimeout(function() {
                  document.querySelector('#WPAuth0SyncManual').disabled = false;
                  document.querySelector('#syncstatus').innerText = "";
                }, 5000);
              });
            });
          });
          clearTimeout(loadingtimeout);
        }, 1000);
      }
    }, 10);
    </script>
    <?php
  }

  add_action('wp_enqueue_scripts', 'Auth0Manager');
  function Auth0Manager() {
    wp_enqueue_script('Auth0Manager', 'http://cdn.central.agriness.com/agrinessAuth0Manager.min.js', null, true, false);
  }

  add_action('wp_footer', 'inlineJs');
  function inlineJs() {
    $wp_auth0_sync_options = get_option("wp_auth0_sync");
    $login_selector = $wp_auth0_sync_options["wp_auth0_sync_login_selector"];
    $logout_selector = $wp_auth0_sync_options["wp_auth0_sync_logout_selector"];
    $logoutUrl = $wp_auth0_sync_options["wp_auth0_sync_logoutUrl"];
    ?>

    <script type="text/javascript">

      var newAuth0Manager = new Auth0Manager({
        domain: 'agriness.auth0.com',
        clientID: 'qQvCGzfSQsxFxGomUUXOnDo0HzMrLrUE',
        loginSelector: '<?php echo $login_selector; ?>',
        logoutSelector: '<?php echo $logout_selector; ?>',
        logoutUrl:'<?php echo $logoutUrl; ?>'
      });

      newAuth0Manager.init();

      newAuth0Manager.onLoggedIn(function() {
        var loggedInWp = '<?php echo $user = wp_get_current_user()->exists() ?>';
        if(!loggedInWp) {
          newAuth0Manager.signin();
        }
      });

      newAuth0Manager.onLoggedOut(function() {

        var loggedInWp = '<?php echo $user = wp_get_current_user()->exists() ?>';

        if(loggedInWp) {
          document.body.style.display = "none";

          jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", {
            'action': 'ajax_wp_auth0_logout'
          }, function(response) {
            location.reload();
          });
        }

        console.log('param.auth0_userEmail');
      }); // newAuth0Manager.onLoggedOut

    </script>
    <?php
  }

  // Logout User
  add_action('wp_ajax_nopriv_ajax_wp_auth0_logout', 'ajax_wp_auth0_logout');
  add_action('wp_ajax_ajax_wp_auth0_logout', 'ajax_wp_auth0_logout');
  function ajax_wp_auth0_logout() {
    wp_logout();
    wp_set_current_user(0);
    wp_clear_auth_cookie();
    echo 'logout';
    die();
  } // function ajax_wp_auth0_logout
?>
