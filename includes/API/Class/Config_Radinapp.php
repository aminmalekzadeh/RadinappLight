<?php


namespace Radinapp_config;


class Config_Radinapp
{
    public function priceWithFormat($value = 0){
        $args = array();
        $args = apply_filters(
            'wc_price_args',
            wp_parse_args(
                $args,
                array(
                    'ex_tax_label'       => false,
                    'currency'           => '',
                    'decimal_separator'  => wc_get_price_decimal_separator(),
                    'thousand_separator' => wc_get_price_thousand_separator(),
                    'decimals'           => wc_get_price_decimals(),
                    'price_format'       => get_woocommerce_price_format(),
                )
            )
        );
        return $this->en_to_fa(apply_filters('formatted_woocommerce_price', number_format($value, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $value, $args['decimals'], $args['decimal_separator'], $args['thousand_separator']));
    }

    protected function en_to_fa($number)
    {
        $en = array("0","1","2","3","4","5","6","7","8","9");
        $fa = array("۰","۱","۲","۳","۴","۵","۶","۷","۸","۹");
        return str_replace($en, $fa, $number);
    }

    public static function Radinapp_current_currency(){
        $args = array();
        $args = apply_filters(
            'wc_price_args',
            wp_parse_args(
                $args,
                array(
                    'ex_tax_label'       => false,
                    'currency'           => '',
                    'decimal_separator'  => wc_get_price_decimal_separator(),
                    'thousand_separator' => wc_get_price_thousand_separator(),
                    'decimals'           => wc_get_price_decimals(),
                    'price_format'       => get_woocommerce_price_format(),
                )
            )
        );
        return html_entity_decode(get_woocommerce_currency_symbol($args['currency']));
    }

}
