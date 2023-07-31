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
{extends file='helpers/form/form.tpl'}

{block name="input_row"}
  {if $input.name == 'AGE_VERIFICATION_HIDDEN_CATEGORIES[]'}
    <div class="row">
      <div class="col-lg-3">
        {$input.label|escape:'htmlall':'UTF-8'}
      </div>
      <div class="col-lg-9">
        {foreach $input.values.query as $category}
          <label class="checkbox-inline">
            <input type="checkbox" name="AGE_VERIFICATION_HIDDEN_CATEGORIES[]" value="{$category.id_option|escape:'htmlall':'UTF-8'}" {$category.checked|escape:'htmlall':'UTF-8'} />
            {$category.name|escape:'htmlall':'UTF-8'}
          </label>
          {foreach $category.products as $product}
            <label class="checkbox-inline ml-3">
              <input type="checkbox" name="AGE_VERIFICATION_HIDDEN_PRODUCTS[]" value="{$product.id_option|escape:'htmlall':'UTF-8'}" {$product.checked|escape:'htmlall':'UTF-8'} />
              {$product.name|escape:'htmlall':'UTF-8'}
            </label>
          {/foreach}
          <br>
        {/foreach}
      </div>
    </div>
  {/if}
{/block}
