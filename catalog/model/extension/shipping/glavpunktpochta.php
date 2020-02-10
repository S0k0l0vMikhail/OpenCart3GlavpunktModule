<?php
/**
 * Модель службы доставки "Главпункт Почта РФ" на странице оформления заказа
 *
 * Class ModelExtensionShippingGlavpunktpochta
 */
class ModelExtensionShippingGlavpunktpochta extends Model
{
    function getQuote($address)
    {
        $this->language->load('extension/shipping/glavpunktpochta');

        if ($this->config->get('shipping_glavpunktpochta_status') == 1) {
            $status = true;
        } else {
            $status = false;
        }
        $method_data = array();
        
        return $method_data;
    }
}