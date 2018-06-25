$(document).ready(function () {
    var manufacturerSlider = new Swiper('.manufacturerslider .swiper-container', {
		slidesPerView: 6,
		loop: true,
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