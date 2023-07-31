{**
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
 *}
<head> 
  {if $selectedGoogleFontLink}
      <link href="{$selectedGoogleFontLink|escape:'htmlall':'UTF-8'}" rel="stylesheet" type="text/css">
    {/if}  
</head>
<div class="age-verification-container" style="background-color: {$AGE_VERIFICATION_PAGE_COLOR|escape:'htmlall':'UTF-8'}; font-family: '{$AGE_VERIFICATION_GOOGLE_FONT|escape:'htmlall':'UTF-8'}', sans-serif;">
  {if $AGE_VERIFICATION_LOGO}
    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/images/{$AGE_VERIFICATION_LOGO|escape:'htmlall':'UTF-8'}" alt="Logo" class="logo">
  {/if}

<div class="age-verification-container" style="background-color: {$AGE_VERIFICATION_PAGE_COLOR|escape:'htmlall':'UTF-8'}; font-family: {$AGE_VERIFICATION_GOOGLE_FONT|escape:'htmlall':'UTF-8'}, sans-serif;">
  {if $AGE_VERIFICATION_LOGO}
    <img src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/images/{$AGE_VERIFICATION_LOGO|escape:'htmlall':'UTF-8'}" alt="Logo" class="logo">
  {/if}

  <h2 style="color: {$AGE_VERIFICATION_TITLE_COLOR|escape:'htmlall':'UTF-8'};">{$AGE_VERIFICATION_TITLE|escape:'htmlall':'UTF-8'}</h2>
  <p style="color: {$AGE_VERIFICATION_DESCRIPTION_COLOR|escape:'htmlall':'UTF-8'};" class="description">{$AGE_VERIFICATION_DESCRIPTION|escape:'htmlall':'UTF-8'}</p>
  <div class="buttons-container">
  
    <button name="yes-age" id="age-verification-yes" style="background-color: {$AGE_VERIFICATION_BUTTON_COLOR|escape:'htmlall':'UTF-8'};" data-underage="false" data-cookie-lifetime="{$AGE_VERIFICATION_COOKIE_LIFETIME|escape:'htmlall':'UTF-8'}">{$confirm_button_text_18|escape:'htmlall':'UTF-8'}</button>
    <button name="no-age" id="age-verification-no" style="background-color: {$AGE_VERIFICATION_BUTTON_COLOR|escape:'htmlall':'UTF-8'};" data-underage="true" data-cookie-lifetime="{$AGE_VERIFICATION_COOKIE_LIFETIME|escape:'htmlall':'UTF-8'}" data-hide-categories="{$AGE_VERIFICATION_HIDE_CATEGORIES|escape:'htmlall':'UTF-8'}">{$confirm_button_text_under_18|escape:'htmlall':'UTF-8'}</button>

  </div>
</div>

