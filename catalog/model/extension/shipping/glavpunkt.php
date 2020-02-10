<?php

class ModelExtensionShippingGlavpunkt extends Model
{
    function getQuote($address)
    {   

        $this->load->language('extension/shipping/glavpunkt');

        if ($this->session->data['shipping_methods']['glavpunkt']['quote']['glavpunkt']['cost']) {
                // стоимость доставки
                $cost = $this->session->data['shipping_methods']['glavpunkt']['quote']['glavpunkt']['cost'];
            } else {
                $cost = 0;
            }

        $answer = $this->load->controller('extension/shipping/glavpunkt');
        $quote_data['glavpunkt'] = array(
                'code' => 'glavpunkt.glavpunkt',
                'title' => $this->language->get('text_title'),
                'text' => $answer,
                'description' => '',
                'cost' => $cost,
                'tax_class_id' => 0,
            );

        $method_data = array(
            'code' => 'glavpunkt',
            'title' => $this->language->get('text_title'),
            'quote' => $quote_data,
            'sort_order' => $this->config->get('glavpunkt_sort_order'),
            'error' => false
        );

        return $method_data;
    }

    /**
     * Формируем запрос на стоимость расчёта доставки к API Главпункта, ответ - json-массив
     *
     * @param array $params массив с переданными параметрами из метода calculateConcrete()
     *
     * @return array ['result' => корректен ли ответ на запрос, 'serv' => тип доставки, 'tarif' => тариф за доставку,
     * 'cityFrom' => город отправки заказа, 'cityTo' => город доставки заказа, 'price' => стоимость заказа,
     * 'weight' => вес заказа, 'paymentType' => тип оплаты, 'period' => срок доставки груза]
     *
     */
    public function getTarif($params)
    {
        $get = '?' . http_build_query($params, '', '&');// строка запроса с переданными параметрами
        // строка запроса с замененными пробелами (после http_build_query = '+') на %20, для версий php < 5.3
        $query = str_replace('+', '%20', $get);
        $url = 'https://glavpunkt.ru/api/get_tarif' . $query;// ссылка для запроса
        $answer = $this->request($url);// получаем содержимое страницы запроса

        return $answer;
    }

    /**
     * Проверяем подключен ли Curl, отключен ли file_get_contents
     *
     * @param string urlPage урл страницы
     *
     */
    private function request($urlPage)
    {
        if (ini_get('allow_url_fopen')) {
            return $this->requestByFileGetContents($urlPage); // отправка запроса через file_get_contents
        } elseif (function_exists('curl_version')) {
            return $this->requestByCurl($urlPage);// отправка запроса курлом
        } else {
            // не удалось получить тариф, следует проверить настройки расширения curl
            // или разрешить file_get_contents с помощью директивы allow_url_fopen = 1
            return 'DELIVERY_ERROR_CURL_FGC';
        }
    }

    /**
     * Получаем страницу ответа API с помощью file_get_contents
     *
     * @param string urlPagebyFGC урл страницы
     *
     */
    private function requestByFileGetContents($urlPageByFGC)
    {
        $page = json_decode(file_get_contents($urlPageByFGC), true);

        return $page;
    }

    /**
     * Получаем страницу ответа API с помощью Curl
     *
     * @param string urlPagebyCurl урл страницы
     *
     */
    private function requestByCurl($urlPagebyCurl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlPagebyCurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $responce = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            return false;
        } else {
            $pageCurl = json_decode($responce, true);

            return $pageCurl;
        }
    }
}
