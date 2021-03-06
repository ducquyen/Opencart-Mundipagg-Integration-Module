<?php
/**
 * ControllerExtensionPaymentMundipagg
 *
 * @package Mundipagg
 */
class ControllerExtensionPaymentMundipagg extends Controller
{
    /**
     * @var array
     */
    private $error = array();

    /**
     * @var array
     */
    private $data = array();

    /**
     * Load basic data and call postRequest or getRequest methods accordingly
     *
     * @return void
     */
    public function index()
    {
        $this->load->language('extension/payment/mundipagg');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->postRequest();
        } else {
            $this->getRequest();
        }
    }

    /**
     * This method is called when the module is being installed
     *
     * First, it save an event and then call the install method from
     * module model
     *
     * @return void
     */
    public function install()
    {
        $this->load->model('extension/payment/mundipagg');
        $this->model_extension_payment_mundipagg->install();
    }

    /**
     * This method is called when the module is being removed
     *
     * It calls the methods from model responsible for delete all
     * data created and used by module
     *
     * @return void
     */
    public function uninstall()
    {
        $this->load->model('extension/payment/mundipagg');
        $this->model_extension_payment_mundipagg->uninstall();
    }

    /**
     * Deal with post requests, in other words, settings being changed
     *
     * @return void
     */
    private function postRequest()
    {
        // validate data here
        if (!$this->validate()) {
            return;
        }

        $this->saveSettings($this->request->post);

        $this->session->data['success'] = $this->language->get('misc')['success'];
        http_response_code(302);
        $this->response->addHeader('Location: '.
            str_replace(
                ['&amp;', "\n", "\r"],
                ['&', '', ''],
                $this->url->link(
                    'marketplace/extension',
                    'user_token=' . $this->session->data['user_token'] . '&type=payment',
                    true
                )
            ));
        $this->response->setOutput(true);
    }

    /**
     * Deal with get requests
     *
     * Get layout components, set language, get bread crumbs
     * and show the form with the previously saved settings
     *
     * @return void
     */
    private function getRequest()
    {
        $this->setLayoutComponents();
        $this->setLanguageData();
        $this->setBreadCrumbs();
        $this->setFormControlData();
        $this->getSavedSettings();

        $this->response->setOutput(
            $this->load->view(
                'extension/payment/mundipagg',
                $this->data
            )
        );
    }

    /**
     * This method saves module settings
     *
     * @param array $postRequest Settings posted from admin panel
     * @return void
     */
    private function saveSettings($postRequest)
    {
        // save credit card payment information
        $this->load->model('extension/payment/mundipagg');
        $this->model_extension_payment_mundipagg->savePaymentInformation(
            $postRequest['creditCard']
        );

        unset($postRequest['creditCard']);

        // save module settings
        $modules = array(
            'payment_mundipagg' => '/^payment_mundipagg/'
        );

        // use array_walk
        foreach ($modules as $module => $pattern) {
            $this->model_setting_setting->editSetting(
                $module,
                $this->getSettingsFor($pattern, $postRequest)
            );
        }
    }

    /**
     * It gets the request and return an array with the correct information to be stored
     *
     * @param string $pattern Contain the pattern to be searched for
     * @param array $request Post request from admin panel
     * @return array
     */
    private function getSettingsFor($pattern, $request)
    {
        return array_filter(
            $request,
            function ($key) use ($pattern) {
                return preg_match($pattern, $key);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Sets opencart dashboard layout components
     *
     * It puts opencart header, left column and footer
     *
     * @return void
     */
    private function setLayoutComponents()
    {
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['footer'] = $this->load->controller('common/footer');
    }

    /**
     * Get language data from language file
     *
     * @return void
     */
    private function setLanguageData()
    {
        $this->data['general'] = $this->language->get('general');
        $this->data['credit_card'] = $this->language->get('credit_card');
        $this->data['boleto'] = $this->language->get('boleto');
        $this->data['misc'] = $this->language->get('misc');
    }

    /**
     * Set bread crumbs, which will be shown in the dashboard panel
     *
     * @return void
     */
    private function setBreadCrumbs()
    {
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link(
                'common/dashboard',
                'user_token=' . $this->session->data['user_token'],
                true
            ));

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link(
                'marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=payment',
                true
            ));

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link(
                'extension/payment/mundipagg',
                'user_token=' . $this->session->data['user_token'],
                true
            ));
    }

    /**
     * Set the action to the save/cancel buttons from dashboard
     *
     * @return void
     */
    private function setFormControlData()
    {
        $this->data['action'] = $this->url->link(
            'extension/payment/mundipagg',
            'user_token=' . $this->session->data['user_token'],
            true
        );

        $this->data['cancel'] = $this->url->link(
            'marketplace/extension',
            'user_token=' . $this->session->data['user_token'] . '&type=payment',
            true
        );
    }


    /**
     * Get the previously saved module settings
     *
     * @return void
     */
    private function getSavedSettings()
    {
        $this->data['settings'] = array(
            'general_status'          => $this->config->get('payment_mundipagg_status'),
            'general_prod_secret_key' => $this->config->get('payment_mundipagg_prod_secret_key'),
            'general_test_secret_key' => $this->config->get('payment_mundipagg_test_secret_key'),
            'general_prod_pub_key'    => $this->config->get('payment_mundipagg_prod_public_key'),
            'general_test_pub_key'    => $this->config->get('payment_mundipagg_test_public_key'),
            'general_test_mode'       => $this->config->get('payment_mundipagg_test_mode'),
            'general_log_enabled'     => $this->config->get('payment_mundipagg_log_enabled'),
            'general_payment_title'   => $this->config->get('payment_mundipagg_title'),

            'credit_card_status'        => $this->config->get('payment_mundipagg_credit_card_status'),
            'credit_card_payment_title' => $this->config->get('payment_mundipagg_credit_card_payment_title'),
            'credit_card_invoice_name'  => $this->config->get('payment_mundipagg_credit_card_invoice_name'),
            'credit_card_operation'     => $this->config->get('payment_mundipagg_credit_card_operation'),

            'boleto_enabled'      => $this->config->get('payment_mundipagg_boleto_status'),
            'boleto_title'        => $this->config->get('payment_mundipagg_boleto_title'),
            'boleto_name'         => $this->config->get('payment_mundipagg_boleto_name'),
            'boleto_bank'         => $this->config->get('payment_mundipagg_boleto_bank'),
            'boleto_instructions' => $this->config->get('payment_mundipagg_boleto_instructions'),
            'boleto_due_date'     => $this->config->get('payment_mundipagg_boleto_due_date')
        );

        $this->load->model('extension/payment/mundipagg');
        $this->data['creditCard'] = $this->model_extension_payment_mundipagg->getCreditCardInformation();
        $this->data['boletoInfo'] = $this->model_extension_payment_mundipagg->getBoletoInformation();
    }

    /**
     * Set error messages
     *
     * TODO: make this method cover more possible errors
     *
     * @return void
     */
    private function setError()
    {
        if (isset($this->error['error_service_key'])) {
            $data['error_service_key'] = $this->error['error_service_key'];
        } else {
            $data['error_service_key'] = '';
        }
    }

    /**
     * Simple validation
     *
     * Validates user permission
     * TODO: think about (and implement) other validation necessities
     *
     * @return Boolean
     */
    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/mundipagg')) {
            // put error message inside language file
            $this->error['warning'] = 'Permission error';
        }

        return !$this->error;
    }
}
