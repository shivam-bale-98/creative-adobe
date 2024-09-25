import Swiper from "swiper";
import {
	Navigation,
	Thumbs,
	Autoplay,
	FreeMode,
	EffectFade,
	EffectCreative,
} from "swiper/modules";

export default class GallerySliderBlock {
	constructor() {
		this.init();
	}

	init = () => {
		this.setDomMap();
		this.bindEvents();
	};

	setDomMap = () => {
		this.window = $(window);
		this.body = $("body");

		this.sliderWrapper = document.querySelectorAll(".gallery-slider");
		this.imageSlider = document.querySelectorAll(".gallery-main-slider");
		// Arrows
		this.prevArrow = document.querySelectorAll(
			".swiper-buttons .swiper-button-prev",
		);
		this.nextArrow = document.querySelectorAll(
			".swiper-buttons .swiper-button-next",
		);
	};
	bindEvents = () => {
		if (this.sliderWrapper) {
			this.sliderWrapper.forEach((slider, index) => {
				const imageSliderContainer = slider.querySelectorAll(
					".gallery-main-slider",
				)[0];

				const thumbSliderContainer = slider.querySelectorAll(
					".gallery-thumb-slider",
				)[0];
				// Gallery thumbs
				const galleryThumbs = new Swiper(thumbSliderContainer, {
					spaceBetween: 10,
					slidesPerView: 4,
					freeMode: true,
					watchSlidesVisibility: true,
					watchSlidesProgress: true,
				});
				// Gallery Main
				const imageSlider = new Swiper(imageSliderContainer, {
					modules: [Navigation, Thumbs, Autoplay, EffectCreative, EffectFade],
					spaceBetween: 10,
					sliderPerView: 1,
					speed: 1000,
					allowTouchMove: false,
					effect: "fade",

					autoplay: {
						delay: 8000,
						disableOnInteraction: false,
					},
					navigation: {
						nextEl: this.nextArrow[index],
						prevEl: this.prevArrow[index],
					},

					thumbs: {
						swiper: galleryThumbs,
					},
				});
			});
		}
	};
}
