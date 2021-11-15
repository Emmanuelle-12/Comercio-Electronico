<?php

/**
 * Plugin Name: Pasarela
 * Plugin URI: pendiente
 * Description: Practica de una pasarela de pago para Woocommerce
 * Author: Samuel Perez y Enmanuel Berrios
 * Author URI: pendiente
 * Version: 0.1
 * Text Domain: Sippayment
 * Domain Path: /i18n/languages/
 */

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

add_action('plugins_loaded', 'noob_payment_init', 11);

function noob_payment_init()
{
    if (class_exists('WC_Payment_Gateway')) {
        class WC_Noob_pay_Gateway extends WC_Payment_Gateway
        {
            public function __construct()
            {
                $this->id   = 'noob_payment';
                $this->icon = apply_filters('woocommerce_noob_icon', plugins_url('/imagenes/SIPpay.png', __FILE__));
                $this->has_fields = false;
                $this->method_title = __('SIPpay', 'noob-pay-woo');
                $this->method_description = __('Sistema de pago de targeta de credito', 'noob-pay-woo');

                $this->title = $this->get_option('title');
                $this->description = $this->get_option('description');
                $this->instructions = $this->get_option('instructions', $this->description);

                $this->init_form_fields();
                $this->init_settings();

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                // add_action( 'woocommerce_thank_you_' . $this->id, array( $this, 'thank_you_page' ) );
            }

            public function init_form_fields()
            {
                $this->form_fields = apply_filters('woo_noob_pay_fields', array(
                    'enabled' => array(
                        'title' => __('Enable/Disable', 'noob-pay-woo'),
                        'type' => 'checkbox',
                        'label' => __('Habilitar o Desabilitar metodo de pago', 'noob-pay-woo'),
                        'default' => 'no'
                    ),
                    'title' => array(
                        'title' => __('Noob Payments Gateway', 'noob-pay-woo'),
                        'type' => 'text',
                        'default' => __('Noob Payments Gateway', 'noob-pay-woo'),
                        'desc_tip' => true,
                        'description' => __('Add a new title for the Noob Payments Gateway that customers will see when they are in the checkout page.', 'noob-pay-woo')
                    ),
                    'description' => array(
                        'title' => __('Noob Payments Gateway Description', 'noob-pay-woo'),
                        'type' => 'textarea',
                        'default' => __('Por favor pague con anticipacion para recibir el pedido', 'noob-pay-woo'),
                        'desc_tip' => true,
                        'description' => __('Add a new title for the Noob Payments Gateway that customers will see when they are in the checkout page.', 'noob-pay-woo')
                    ),
                    'instructions' => array(
                        'title' => __('Instructions', 'noob-pay-woo'),
                        'type' => 'textarea',
                        'default' => __('Default instructions', 'noob-pay-woo'),
                        'desc_tip' => true,
                        'description' => __('Instructions that will be added to the thank you page and order email', 'noob-pay-woo')
                    ),
                    // 'enable_for_virtual' => array(
                    //     'title'   => __( 'Accept for virtual orders', 'woocommerce' ),
                    //     'label'   => __( 'Accept COD if the order is virtual', 'woocommerce' ),
                    //     'type'    => 'checkbox',
                    //     'default' => 'yes',
                    // ),
                ));
            }

            public function process_payments($order_id)
            {

                $order = wc_get_order($order_id);

                $order->update_status('on-hold',  __('Awaiting Noob Payment', 'noob-pay-woo'));

                // if ( $order->get_total() > 0 ) {
                // Mark as on-hold (we're awaiting the cheque).
                // } else {
                // $order->payment_complete();
                // }

                // $this->clear_payment_with_api();

                $order->reduce_order_stock();

                WC()->cart->empty_cart();

                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url($order),
                );
            }

            // public function clear_payment_with_api() {

            // }

            public function thank_you_page()
            {
                if ($this->instructions) {
                    echo wpautop($this->instructions);
                }
            }
        }
    }
}

add_filter('woocommerce_payment_gateways', 'add_to_woo_noob_payment_gateway');

function add_to_woo_noob_payment_gateway($gateways)
{
    $gateways[] = 'WC_Noob_pay_Gateway';
    return $gateways;
}
