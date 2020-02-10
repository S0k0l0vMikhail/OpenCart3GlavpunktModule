<?php


class ControllerExtensionShippingGlavpunkt extends Controller
{
    private $error = array();

    public function index($data)
    {
        $this->load->language('extension/shipping/glavpunkt');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        $data = array();
        $data['text_description'] = $this->language->get('text_description');
        $data['city'] = "{'defaultCity': '" . $this->session->data['shipping_address']['city'] .
                "'," . $this->config->get('shipping_glavpunkt_widget_data') . "}";
        return $this->load->view('extension/shipping/glavpunkt', $data);
    }

    public function setprice()
    {
        $this->load->model('extension/shipping/glavpunkt');

        $params = array(
                'serv' => 'выдача',// тип доставки
                'cityFrom' => $this->config->get('shipping_glavpunkt_home_city'),// город отправки заказа
                'cityTo' => $this->request->post['city_to'],// город доставки заказа
                'weight' => $this->cart->getWeight(),// вес заказа
                'price' => $this->cart->getTotal(),// стоимость заказа
                'punktId' => $this->request->post['id'],// id пункта получения
            );
            
        $json = $this->model_extension_shipping_glavpunkt->getTarif($params);
        $this->session->data['shipping_methods']['glavpunkt']['quote']['glavpunkt']['cost'] = $json['tarif'];
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}