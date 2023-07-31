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
document.addEventListener('DOMContentLoaded', function () {
    let ageVerificationContainer = document.querySelector('.age-verification-container');
    let yesButton = document.getElementById('age-verification-yes');
    let noButton = document.getElementById('age-verification-no');
  
    // Check if the user has already verified their age
    let isAgeVerified = document.cookie.indexOf('ageVerified=true') !== -1;

    let ageVerified = isAgeVerified ? 'true' : 'false';
    let underage = "";
  
    yesButton.addEventListener('click', function () {
      let cookieLifetime = yesButton.getAttribute('data-cookie-lifetime');
      setAgeVerifiedCookie(false, cookieLifetime);
      underage = 'false';
      window.location.href = prestashop.urls.base_url;
    });
  
    noButton.addEventListener('click', function () {
      let cookieLifetime = noButton.getAttribute('data-cookie-lifetime');
      setAgeVerifiedCookie(true, cookieLifetime);
      underage = 'true';
      // Check if AGE_VERIFICATION_HIDE_CATEGORIES is set to 0
      let hideCategories = noButton.getAttribute('data-hide-categories');
      const restrictedaccess = prestashop.urls.base_url + 'module/ageverification/restrictedaccess';
      
      // If AGE_VERIFICATION_HIDE_CATEGORIES is set to 0, redirect to restricted access page
      if (hideCategories === '0') {
        window.location.href = restrictedaccess;
      }
    });

    axios.post('/module/ageverification/ageVerificationPhpScript', {
      ageVerified: ageVerified,
      underage: underage,
    })
    .then(response => {
      console.log('Cookies transferred to PHP successfully.');
    })
    .catch(error => {
      console.error('Error transferring cookies to PHP:', error);
    });
 
    if (!isAgeVerified) {
      // If the user hasn't clicked either button, show the age verification pop-up
      ageVerificationContainer.style.display = 'flex';
    }
  
    function setAgeVerifiedCookie(isUnderage, cookieLifetime) {
      // Set a cookie to remember the user's age verification status
      let expirationDate = new Date();
      expirationDate.setDate(expirationDate.getDate() + parseInt(cookieLifetime));
      document.cookie = 'ageVerified=true; expires=' + expirationDate.toUTCString() + '; path=/';
      document.cookie = 'underage=' + (isUnderage ? 'true' : 'false') + '; expires=' + expirationDate.toUTCString() + '; path=/';
      
      // Hide the age verification pop-up
      ageVerificationContainer.style.display = 'none';
    }
  });
  