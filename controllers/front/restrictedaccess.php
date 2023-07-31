<?php
/**
 * FrontAgeVerificationReestrictedAcessController.php
 *
 * @author    Samar Al khalil
 * @copyright Copyright (c) Your Year
 * @license   License (if applicable)
 * @category  Controllers
 * @package   FrontController
 * @subpackage AgeVerificationRestrictedAccess
 */
class AgeVerificationRestrictedAccessModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Display the custom restricted access template
        $this->setTemplate('module:ageverification/views/templates/front/restrictedaccess.tpl');
    }
}
