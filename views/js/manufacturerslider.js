/*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$(document).ready(function () {
	var sliderElement = document.querySelector('.manufacturerslider');
	var sliderItems = sliderElement.dataset.items || 3;
	var sliderSpeed = sliderElement.dataset.speed || 1500;
	var sliderLoop = sliderElement.dataset.loop || true;
    var manufacturerSlider = new Swiper('.manufacturerslider .swiper-container', {
		slidesPerView: sliderItems,
		autoplay: {
			delay: sliderSpeed
		},
		loop: sliderLoop,
		navigation: {
			nextEl: '.manufacturerslider .swiper-button-next',
			prevEl: '.manufacturerslider .swiper-button-prev',
		},

		// Responsive breakpoints
		breakpoints: {
			1170: {
				slidesPerView: 5
			},
			992: {
				slidesPerView: 4
			},
			600: {
				slidesPerView: 3
			},
			460: {
				slidesPerView: 2
			}
		}
	});
});