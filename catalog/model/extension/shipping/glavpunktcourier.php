<?php

/**
 * Модель модуля доставки "Курьерская доставка Главпункт" на странице оформления заказа
 *
 * Отвчает за вывод всех полей и получении цены
 *
 * Class ControllerCheckoutGlavpunktCourier
 * @author SergeChepikov
 */
class ModelExtensionShippingGlavpunktcourier extends Model
{
    function getQuote($address)
    {
        $this->language->load('extension/shipping/glavpunktcourier');

        if ($this->config->get('shipping_glavpunktcourier_status') == 1) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        
        return $method_data;
    }

    private function sendRequest($dataToRequest)
    {
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, 'https://glavpunkt.ru/api/get_tarif?');
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, 'serv=' . $dataToRequest['serv'] .
                '&cityFrom=' . $dataToRequest['cityFrom'] .
                '&cityTo=' . $dataToRequest['cityTo'] .
                '&weight=' . $dataToRequest['weight'] .
                '&price=' . $dataToRequest['price']);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');
            $res = curl_exec($curl);
            curl_close($curl);
            $result_of_request = json_decode($res, true);
        } else {
            $url = 'https://glavpunkt.ru/api/get_tarif?serv=курьерская доставка&cityFrom=' .
                $dataToRequest['cityFrom'] . '&cityTo=' . $dataToRequest['cityTo'] . '&weight=' . $dataToRequest['weight'] . '&price=' . $dataToRequest['price'];
            $result_of_request = json_decode(file_get_contents($url, true));
        }

        return $result_of_request;
    }
}
