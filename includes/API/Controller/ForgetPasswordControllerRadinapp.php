<?php


namespace digiappsaz\API;


use function register_rest_route;
use WP_REST_Server;

class ForgetPasswordControllerRadinapp extends BaseController_Radinapp
{
    public function __construct()
    {
        parent::__construct();
        $this->resourece_name = 'forgetpass';
    }

    public function register_routes()
    {
        register_rest_route($this->namespace,'/'.$this->resourece_name,[
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this,'update_item')
        ]);
    }

    public function update_item($request)
    {
        global $wpdb, $current_site;
        $email = $request->get_param("email");

        $user_data = get_user_by('email', $email);
        do_action('lostpassword_post');


        if (!$user_data) return [
            'message' => 'کاربری با این ایمیل یافت نشد',
            'status' => 404
        ];

        // redefining user_login ensures we return the right case in the email
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;

        do_action('retreive_password', $user_login);  // Misspelled and deprecated
        do_action('retrieve_password', $user_login);

        $allow = apply_filters('allow_password_reset', true, $user_data->ID);

        if (!$allow)
            return false;
        else if (is_wp_error($allow))
            return false;

        $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
        if (empty($key)) {
            // Generate something random for a key...
            $key = wp_generate_password(20, false);
            do_action('retrieve_password_key', $user_login, $key);
            // Now insert the new md5 key into the db
            $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
        }
        $message = __('بازیابی رمزعبور:') . "\r\n\r\n";
        $message .= network_home_url('/') . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

        if (is_multisite())
            $blogname = $GLOBALS['current_site']->site_name;
        else
            // The blogname option is escaped with esc_html on the way into the database in sanitize_option
            // we want to reverse this for the plain text arena of emails.
            $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

        $title = sprintf(__('[%s] Password Reset'), $blogname);
        add_filter('lostpassword_url', 'mihanpanel_reset_pass_url', 11, 0);
//        $title = apply_filters('retrieve_password_title', $title);
//        $message = apply_filters('retrieve_password_message', $message, $key);

        if ($message && !wp_mail($user_email, $title, $message))
            return [
                'message' => 'خطا در ارسال ایمیل',
                'status' => 500
            ];
        return [
            'message' => 'ایمیل با موفقیت ارسال شد',
            'status' => 200
        ];
    }

}
new ForgetPasswordControllerRadinapp();
