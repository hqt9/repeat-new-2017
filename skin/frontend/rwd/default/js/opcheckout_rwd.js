/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    design
 * @package     rwd_default
 * @copyright   Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */


Checkout.prototype.chekForm = function(section){
    
    $j('#review-save')[0].disabled = true;
    var sections = $j('.section');
    var currentSection = $j('#opc-' + section);
    var currentStep = currentSection.find('.step');
    var index = 0;
    sections.map(function(i,elem){
        if ($j(elem).attr('id') == currentSection.attr('id')) {
            index = i
        }
    })
    currentStep.scrollTop(0);
    switch(section){
        case 'billing':
            checkBillingForm();
            break;
        case 'shipping':
            checkShippingForm();
            break;
        case 'shipping_method':
            $j('#opc-shipping_method .step-back .btn-done').removeClass('disabled');
            break;
        case 'payment':
            checkPaymentForm();
            break;
        case 'review':
            $j('#review-save')[0].disabled = false;
    }
    sections.map(function(i,elem){
        if (i >= index) {
            $j(elem).find('.step-result').html('');
        }
    })
}


Checkout.prototype.gotoSection = function (section, reloadProgressBlock) {
    this.chekForm(section)
    // Adds class so that the page can be styled to only show the "Checkout Method" step
    if ((this.currentStep == 'login' || this.currentStep == 'billing') && section == 'billing') {
        $j('body').addClass('opc-has-progressed-from-login');
    }

    if (reloadProgressBlock) {
        this.reloadProgressBlock(this.currentStep);
    }
    this.currentStep = section;
    var sectionElement = $('opc-' + section);
    sectionElement.addClassName('allow');
    this.accordion.openSection('opc-' + section);

    // Scroll viewport to top of checkout steps for smaller viewports
    if (Modernizr.mq('(max-width: ' + bp.xsmall + 'px)')) {
        $j('html,body').animate({scrollTop: $j('#checkoutSteps').offset().top}, 800);
    }

    if (!reloadProgressBlock) {
        this.resetPreviousSteps();
    }
};

