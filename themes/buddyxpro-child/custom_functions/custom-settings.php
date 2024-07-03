<?php
/**
 * Custom WooCommerce Settings
 */

if (!class_exists('WC_Custom_Settings_Tab')) {
    class WC_Custom_Settings_Tab extends WC_Settings_Page
    {
        public function __construct()
        {
            $this->id = 'custom-settings';
            $this->label = __('Custom Settings', 'woocommerce');

            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }

        public function add_settings_page($settings_tabs)
        {
            $settings_tabs[$this->id] = __('Custom Settings', 'woocommerce');
            return $settings_tabs;
        }

        public function get_sections()
        {
            $sections = array(
                'custom-section' => __('Custom Section', 'woocommerce'),
            );
            return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
        }

        public function get_settings()
        {
            $settings = apply_filters('woocommerce_custom_settings', array(
                array(
                    'title' => __('Purchase Redirection', 'woocommerce'),
                    'type' => 'title',
                    'desc' => __('Configure the page to redirect users after a purchase.', 'woocommerce'),
                    'id' => 'custom_settings_section'
                ),
                array(
                    'title' => __('Redirect Page', 'woocommerce'),
                    'desc' => __('Select the page to redirect users to after a purchase.', 'woocommerce'),
                    'id' => 'woocommerce_redirect_page_id',
                    'type' => 'single_select_page',
                    'default' => '',
                    'class' => 'wc-enhanced-select-nostroke',
                    'css' => 'min-width:300px;',
                    'desc_tip' => true,
                ),
                array(
                    'type' => 'sectionend',
                    'id' => 'custom_settings_section'
                )
            ));

            return apply_filters('woocommerce_get_settings_' . $this->id, $settings);
        }
    }
}

return new WC_Custom_Settings_Tab();