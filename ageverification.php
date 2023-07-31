<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */


if(!defined('_PS_VERSION_')) {
    exit;
}

class AgeVerification extends Module
{
    private $errors = [];
    public function __construct()
    {
        $this->name ='ageverification';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'samar Alkhalil';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];


        parent::__construct();

        $this->bootstrap = true;
        $this->displayName = $this->l("Age Verification");
        $this->description = $this->l('Module de vérification d\'âge pour restreindre l\'accès à certains produits.');
        $this->confirmUninstall = $this->l('Êtes-vous sur de vouloir supprimer ce module');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayWrapperTop') ||
            !$this->registerHook('Header') ||
            !$this->registerHook('actionDispatcherBefore') ||
            !$this->registerHook('moduleRoutes') ||
            !Configuration::updateValue('AGE_VERIFICATION_TITLE', 'Avez-vous 18 ans ou plus ?') ||
            !Configuration::updateValue('AGE_VERIFICATION_DESCRIPTION', 'This website contains products intended for adults only. Please confirm that you are 18 years or older.') ||
            !Configuration::updateValue('AGE_VERIFICATION_COOKIE_LIFETIME', 1) || // Durée de vie du cookie en jours
            !Configuration::updateValue('AGE_VERIFICATION_LOGO', Tools::getValue('AGE_VERIFICATION_LOGO')) ||
            !Configuration::updateValue('AGE_VERIFICATION_PAGE_COLOR', '#f0f0f0')  ||// Couleur de la page
            !Configuration::updateValue('AGE_VERIFICATION_TITLE_COLOR', '#333333') || // Couleur du titre
            !Configuration::updateValue('AGE_VERIFICATION_DESCRIPTION_COLOR', '#666666') ||// Couleur de la description
            !Configuration::updateValue('AGE_VERIFICATION_GOOGLE_FONT', '') || // Police GoogleFonts
            !Configuration::updateValue('AGE_VERIFICATION_BUTTON_COLOR', Tools::getValue('AGE_VERIFICATION_BUTTON_COLOR')) ||
            !Configuration::updateValue('AGE_VERIFICATION_HIDE_CATEGORIES', false) ||
            !Configuration::updateValue('AGE_VERIFICATION_HIDDEN_CATEGORIES', []) ||
            !Configuration::updateValue('AGE_VERIFICATION_HIDDEN_PRODUCTS', [])

        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->unregisterHook('displayWrapperTop') ||
            !$this->unregisterHook('Header') ||
            !$this->unregisterHook('actionDispatcherBefore') ||
            !$this->unregisterHook('moduleRoutes') ||
            !Configuration::deleteByName('AGE_VERIFICATION_TITLE') ||
            !Configuration::deleteByName('AGE_VERIFICATION_DESCRIPTION') ||
            !Configuration::deleteByName('AGE_VERIFICATION_COOKIE_LIFETIME') || // Durée de vie du cookie en jours
            !Configuration::deleteByName('AGE_VERIFICATION_LOGO') ||
            !Configuration::deleteByName('AGE_VERIFICATION_PAGE_COLOR')  ||// Couleur de la page
            !Configuration::deleteByName('AGE_VERIFICATION_TITLE_COLOR') || // Couleur du titre
            !Configuration::deleteByName('AGE_VERIFICATION_DESCRIPTION_COLOR') ||// Couleur de la description
            !Configuration::deleteByName('AGE_VERIFICATION_GOOGLE_FONT') ||// Police GoogleFonts
            !Configuration::deleteByName('AGE_VERIFICATION_BUTTON_COLOR') ||
            !Configuration::deleteByName('AGE_VERIFICATION_HIDE_CATEGORIES') ||
            !Configuration::deleteByName('AGE_VERIFICATION_HIDDEN_CATEGORIES') ||
            !Configuration::deleteByName('AGE_VERIFICATION_HIDDEN_PRODUCTS')
        ) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit_' . $this->name)) {
            // Form submitted, process the data
            $output .= $this->postProcess();

        }
        // Load the necessary JavaScript file for the configuration page
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');

        // Render the form

        $output .= $this->renderForm();
        return $output;
    }

    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $googleFonts = $this->getGoogleFonts();

        // Fetch all the categories and their products
        $categories = Category::getSimpleCategories($this->context->language->id);


        $products = Product::getProducts($this->context->language->id, 0, 0, 'name', 'ASC');

        $selectedCategoryIds = explode(',', Configuration::get('AGE_VERIFICATION_HIDDEN_CATEGORIES'));
        $selectedProductIds = explode(',', Configuration::get('AGE_VERIFICATION_HIDDEN_PRODUCTS'));

        // Create arrays to store the options for category and product checkboxes
        $categoryOptions = array_map(function ($category) use ($selectedCategoryIds) {
            return [
                'id_option' => $category['id_category'],
                'name' => $category['name'],
                'selected' => in_array($category['id_category'], $selectedCategoryIds),
            ];
        }, $categories);

        // Set the 'selected' key to false for any category not present in $selectedCategoryIds
        foreach ($categoryOptions as &$categoryOption) {
            if (!isset($categoryOption['selected'])) {
                $categoryOption['selected'] = false;
            }
        }
        unset($categoryOption); // Unset the reference to the last element

        $productOptions = array_map(function ($product) use ($selectedProductIds) {
            return [
                'id_option' => $product['id_product'],
                'name' => $product['name'],
                'selected' => in_array($product['id_product'], $selectedProductIds),
            ];
        }, $products);

        // Set the 'selected' key to false for any product not present in $selectedProductIds
        foreach ($productOptions as &$productOption) {
            if (!isset($productOption['selected'])) {
                $productOption['selected'] = false;
            }
        }
        unset($productOption); // Unset the reference to the last element

        $categoryTitle = $this->l('Masquer les categories');
        $categoryCheckboxes = '';
        foreach ($categoryOptions as $category) {
            $categoryCheckboxes .= '<div class="checkbox">';
            $categoryCheckboxes .= '<label>';
            $categoryCheckboxes .= '<input type="checkbox" name="AGE_VERIFICATION_HIDDEN_CATEGORIES[]" value="' . $category['id_option'] . '"';
            if ($category['selected']) {
                $categoryCheckboxes .= ' checked="checked"';
            }
            $categoryCheckboxes .= '> ' . $category['name'];
            $categoryCheckboxes .= '</label>';
            $categoryCheckboxes .= '</div>';
        }

        // Product Checkboxes
        $productTitle = $this->l('Masquer les prduits');
        $productCheckboxes = '';
        foreach ($productOptions as $product) {
            $productCheckboxes .= '<div class="checkbox">';
            $productCheckboxes .= '<label>';
            $productCheckboxes .= '<input type="checkbox" name="AGE_VERIFICATION_HIDDEN_PRODUCTS[]" value="' . $product['id_option'] . '"';
            if ($product['selected']) {
                $productCheckboxes .= ' checked="checked"';
            }
            $productCheckboxes .= '> ' . $product['name'];
            $productCheckboxes .= '</label>';
            $productCheckboxes .= '</div>';
        }


        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Age Verification Configuration'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Titre'),
                    'name' => 'AGE_VERIFICATION_TITLE',
                    'size' => 200,
                    'validate' => 'isGeneric',
                    'required' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'AGE_VERIFICATION_DESCRIPTION',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Durée de vie du cookie (jours)'),
                    'name' => 'AGE_VERIFICATION_COOKIE_LIFETIME',
                    'suffix' => $this->l('jours'),
                    'class' => 'fixed-width-xs',
                    'validate' => 'isInt',
                ],
                [
                    'type' => 'file',
                    'label' => $this->l('Logo'),
                    'name' => 'AGE_VERIFICATION_LOGO',
                    'display_image' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur de la page'),
                    'name' => 'AGE_VERIFICATION_PAGE_COLOR',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur du titre'),
                    'name' => 'AGE_VERIFICATION_TITLE_COLOR',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur de la description'),
                    'name' => 'AGE_VERIFICATION_DESCRIPTION_COLOR',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Google Font'),
                    'name' => 'AGE_VERIFICATION_GOOGLE_FONT',
                    'options' => [
                        'query' => array_map(function ($font, $link) {
                            return [
                                'id' => $font,
                                'name' => $font,
                                'font_link' => $link, // Store the font link in the 'font_link' field
                            ];
                        }, array_keys($googleFonts), $googleFonts),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('Choisi un font google pour la page de verification de l\'âge.'),
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Couleur du bouton'),
                    'name' => 'AGE_VERIFICATION_BUTTON_COLOR',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Masquer des catégories spécifiques'),
                    'name' => 'AGE_VERIFICATION_HIDE_CATEGORIES',
                    'is_bool'=>true,
                    'size' => 20,
                    'values' => array(
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Activé')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Désactivé')
                        ]
                    ),
                    'required' => true,
                ],
                [
                    'type' => 'html',
                    'name' => 'AGE_VERIFICATION_HIDDEN_CATEGORIES[]',
                    'html_content' => '<h2>' . $categoryTitle . '</h2>' . $categoryCheckboxes,
                ],
                [
                    'type' => 'html',
                    'name' => 'AGE_VERIFICATION_HIDDEN_PRODUCTS[]',
                    'html_content' => '<h2>' . $productTitle . '</h2>' . $productCheckboxes,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ],
            'enctype' => 'multipart/form-data',
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit_' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        // Chargez les valeurs actuelles de la configuration
        $helper->fields_value['AGE_VERIFICATION_TITLE'] = Configuration::get('AGE_VERIFICATION_TITLE', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_DESCRIPTION'] = Configuration::get('AGE_VERIFICATION_DESCRIPTION', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_COOKIE_LIFETIME'] = Configuration::get('AGE_VERIFICATION_COOKIE_LIFETIME', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_LOGO'] = Configuration::get('AGE_VERIFICATION_LOGO');
        $helper->fields_value['AGE_VERIFICATION_PAGE_COLOR'] = Configuration::get('AGE_VERIFICATION_PAGE_COLOR', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_TITLE_COLOR'] = Configuration::get('AGE_VERIFICATION_TITLE_COLOR', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_DESCRIPTION_COLOR'] = Configuration::get('AGE_VERIFICATION_DESCRIPTION_COLOR', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_GOOGLE_FONT'] = Configuration::get('AGE_VERIFICATION_GOOGLE_FONT', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_BUTTON_COLOR'] = Configuration::get('AGE_VERIFICATION_BUTTON_COLOR', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_HIDE_CATEGORIES'] = Configuration::get('AGE_VERIFICATION_HIDE_CATEGORIES', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_HIDDEN_CATEGORIES'] = Configuration::get('AGE_VERIFICATION_HIDDEN_CATEGORIES', $default_lang);
        $helper->fields_value['AGE_VERIFICATION_HIDDEN_PRODUCTS'] = Configuration::get('AGE_VERIFICATION_HIDDEN_PRODUCTS', $default_lang);

        return $helper->generateForm($fields_form);
    }


    public function postProcess()
    {
        if(Tools::isSubmit('saving')) {
            if(empty(Tools::getValue('AGE_VERIFICATION_TITLE')) ||
               empty(Tools::getValue('AGE_VERIFICATION_DESCRIPTION')) ||
               empty(Tools::getValue('AGE_VERIFICATION_COOKIE_LIFETIME'))

            ) {
                return $this->displayError('Une valeur est vide');
            } else {

                if ($this->processImageUpload()) {
                    $logoName = Tools::getValue('AGE_VERIFICATION_LOGO');

                }

                if(empty($logoName)) {
                    $logoName = configuration::get('AGE_VERIFICATION_LOGO');

                }
                // Get the selected category and product IDs from the submitted form data
                $selectedCategoryIds = [];
                $selectedProductIds = [];
                if (Tools::getValue('AGE_VERIFICATION_HIDDEN_CATEGORIES') && is_array(Tools::getValue('AGE_VERIFICATION_HIDDEN_CATEGORIES'))) {
                    $selectedCategoryIds = Tools::getValue('AGE_VERIFICATION_HIDDEN_CATEGORIES');
                }

                if (Tools::getValue('AGE_VERIFICATION_HIDDEN_PRODUCTS') && is_array(Tools::getValue('AGE_VERIFICATION_HIDDEN_PRODUCTS'))) {
                    $selectedProductIds = Tools::getValue('AGE_VERIFICATION_HIDDEN_PRODUCTS');
                }

                Configuration::updateValue('AGE_VERIFICATION_TITLE', Tools::getValue('AGE_VERIFICATION_TITLE'));
                Configuration::updateValue('AGE_VERIFICATION_DESCRIPTION', Tools::getValue('AGE_VERIFICATION_DESCRIPTION'));
                Configuration::updateValue('AGE_VERIFICATION_COOKIE_LIFETIME', Tools::getValue('AGE_VERIFICATION_COOKIE_LIFETIME'));
                Configuration::updateValue('AGE_VERIFICATION_LOGO', $logoName);
                Configuration::updateValue('AGE_VERIFICATION_PAGE_COLOR', Tools::getValue('AGE_VERIFICATION_PAGE_COLOR'));
                Configuration::updateValue('AGE_VERIFICATION_TITLE_COLOR', Tools::getValue('AGE_VERIFICATION_TITLE_COLOR'));
                Configuration::updateValue('AGE_VERIFICATION_DESCRIPTION_COLOR', Tools::getValue('AGE_VERIFICATION_DESCRIPTION_COLOR'));
                Configuration::updateValue('AGE_VERIFICATION_GOOGLE_FONT', Tools::getValue('AGE_VERIFICATION_GOOGLE_FONT'));
                Configuration::updateValue('AGE_VERIFICATION_BUTTON_COLOR', Tools::getValue('AGE_VERIFICATION_BUTTON_COLOR'));
                Configuration::updateValue('AGE_VERIFICATION_HIDE_CATEGORIES', Tools::getValue('AGE_VERIFICATION_HIDE_CATEGORIES'));
                Configuration::updateValue('AGE_VERIFICATION_HIDDEN_CATEGORIES', implode(',', $selectedCategoryIds));
                Configuration::updateValue('AGE_VERIFICATION_HIDDEN_PRODUCTS', implode(',', $selectedProductIds));
                $ageVerified = 'hello world';
                $cookie = Context::getContext()->cookie;
                $cookie->ageVerified =  $ageVerified;


                return $this->displayConfirmation('Sauvegarde réussie');
            }
        }
    }

    private function getGoogleFonts()
    {
        $url = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD7gXABk_mJq18GpNoMyhXXmRAgRZU2WJk&sort=popularity';

        try {
            $response = Tools::file_get_contents($url);
            $fonts = json_decode($response, true);


            if (isset($fonts['items'])) {
                $fontList = [];
                foreach ($fonts['items'] as $font) {
                    // Store the font name as the key and the font menu name as the value
                    $fontLink = 'https://fonts.googleapis.com/css2?family=' . urlencode($font['family']) . '&display=swap';
                    // Store the font name as the key and the formatted link as the value
                    $fontList[$font['family']] = $fontLink;
                }
                return $fontList;
            }
        } catch (Exception $e) {
            // Log the error for debugging purposes
            PrestaShopLogger::addLog($e->getMessage(), 3, null, 'AgeVerification', 1, true);

            // Return an empty array to avoid errors in the configuration form
            return [];
        }

        return [];
    }

    protected function processImageUpload()
    {

        $uploadDir = _PS_MODULE_DIR_ . 'ageverification/views/img/images/';
        $fileName = 'ageverificationLogo' . md5(uniqid()) . '.' . pathinfo($_FILES['AGE_VERIFICATION_LOGO']['name'], PATHINFO_EXTENSION);

        $targetFile = $uploadDir . $fileName;

        $allowedExtensions = array('jpg', 'jpeg', 'png');
        if (!in_array(pathinfo($_FILES['AGE_VERIFICATION_LOGO']['name'], PATHINFO_EXTENSION), $allowedExtensions)) {
            $this->errors[] = $this->l('Invalid file format. Allowed formats are jpg, jpeg, and png.');
            return false;
        }

        if (!move_uploaded_file($_FILES['AGE_VERIFICATION_LOGO']['tmp_name'], $targetFile)) {
            return false;
        }

        $_POST['AGE_VERIFICATION_LOGO'] = $fileName;
        return true;



    }

    public function hookDisplayWrapperTop($params)
    {

        // Check if the user has already verified their age
        $isAgeVerified = isset($_COOKIE['ageVerified']) && $_COOKIE['ageVerified'] === 'true';

        // If the user hasn't verified their age, display the age verification pop-up
        if (!$isAgeVerified) {
            $googleFonts = $this->getGoogleFonts();
            $selectedGoogleFont = Configuration::get('AGE_VERIFICATION_GOOGLE_FONT');
            $selectedGoogleFontLink = isset($googleFonts[$selectedGoogleFont]) ? $googleFonts[$selectedGoogleFont] : null;
            $this->context->smarty->assign(array(
                'AGE_VERIFICATION_TITLE' => Configuration::get('AGE_VERIFICATION_TITLE', (int) $this->context->language->id),
                'AGE_VERIFICATION_DESCRIPTION' => Configuration::get('AGE_VERIFICATION_DESCRIPTION', (int) $this->context->language->id),
                'AGE_VERIFICATION_LOGO' => Configuration::get('AGE_VERIFICATION_LOGO'),
                'AGE_VERIFICATION_PAGE_COLOR' => Configuration::get('AGE_VERIFICATION_PAGE_COLOR'),
                'AGE_VERIFICATION_TITLE_COLOR' => Configuration::get('AGE_VERIFICATION_TITLE_COLOR'),
                'AGE_VERIFICATION_DESCRIPTION_COLOR' => Configuration::get('AGE_VERIFICATION_DESCRIPTION_COLOR'),
                'AGE_VERIFICATION_GOOGLE_FONT' => Configuration::get('AGE_VERIFICATION_GOOGLE_FONT'),
                'AGE_VERIFICATION_COOKIE_LIFETIME' => Configuration::get('AGE_VERIFICATION_COOKIE_LIFETIME'),
                'AGE_VERIFICATION_BUTTON_COLOR' => Configuration::get('AGE_VERIFICATION_BUTTON_COLOR'),
                'AGE_VERIFICATION_HIDE_CATEGORIES' => Configuration::get(('AGE_VERIFICATION_HIDE_CATEGORIES')),
                'selectedGoogleFontLink' => $selectedGoogleFontLink,
                'fontList' => $googleFonts,
                'confirm_button_text_18' => $this->l("J'ai 18 ans et plus"),
                'confirm_button_text_under_18' => $this->l("J'ai moins de 18 ans"),
                'module_dir' => $this->_path,
            ));

            return $this->display(__FILE__, 'views/templates/front/age_verification.tpl');
        }

        // If the user has already verified their age, return nothing
        return '';
    }

    public function hookHeader()
    {

        // Check if the user has already verified their age
        $isAgeVerified = isset($_COOKIE['ageVerified']) && $_COOKIE['ageVerified'] === 'true';
        // If the user hasn't verified their age, load the CSS and JavaScript
        if (!$isAgeVerified) {
            $this->context->controller->addCSS($this->_path . 'views/css/age_verification.css');
            $this->context->controller->addJS($this->_path . 'views/js/age_verification.js');
        }
    }

    public function hookActionDispatcherBefore($params)
    {
        $this->isUnderage();
    }

    private function isUnderage()
    {

        if (isset($_COOKIE['underage']) && $_COOKIE['underage'] === 'true') {

            // Check if the underage restriction is enabled in the back office

            if(Configuration::get('AGE_VERIFICATION_HIDE_CATEGORIES') && Configuration::get('AGE_VERIFICATION_HIDE_CATEGORIES') == 1) {

                // Get hidden category and product IDs from the database
                $hiddenCategoryIds = explode(',', Configuration::get('AGE_VERIFICATION_HIDDEN_CATEGORIES'));
                $hiddenProductIds = explode(',', Configuration::get('AGE_VERIFICATION_HIDDEN_PRODUCTS'));

                // Check the requested category ID from the URL
                $categoryId = (int)Tools::getValue('id_category');

                // Check if the requested category is hidden
                if (in_array($categoryId, $hiddenCategoryIds)) {
                    // Redirect the user to a restricted access page
                    Tools::redirect($this->context->link->getModuleLink('ageverification', 'restrictedaccess'));
                }

                // Check the requested product ID from the URL
                $productId = (int)Tools::getValue('id_product');

                // Check if the requested product is hidden
                if (in_array($productId, $hiddenProductIds)) {
                    // Redirect the user to a restricted access page
                    Tools::redirect($this->context->link->getModuleLink('ageverification', 'restrictedaccess'));
                }

            }

            if (
                !Tools::getValue('controller')
                || (Tools::getValue('controller') !== 'restrictedaccess' && !($this->context->controller instanceof AdminController))
            ) {
                Tools::redirect($this->context->link->getModuleLink('ageverification', 'restrictedaccess'));
                exit;
            }



        }

    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-ageverification-restrictedaccess' => [
                'controller' => 'restrictedaccess',
                'rule' => 'restricted-access',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'ageverification',
                ],
            ],
        ];
    }


}
