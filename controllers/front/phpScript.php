<?php
/**
 * index.php
 *
 * @author    Samar Al Khalil
 * @copyright Copyright (c)
 * @license   License (if applicable)
 * @category  Classes
 * @package   YourPackageName
 * @subpackage Classes
 */
// Ensure PrestaShop is loaded and able to process the request

class AgeVerificationPhpScriptModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();


        if(Tools::isSubmit('yes-age')) {


            $context = Context::getContext();
            $cookie = $context->cookie;

            $cookie->__set('ageVerified', true);
            $cookie->__set('underage', false);

        }

    }
}
