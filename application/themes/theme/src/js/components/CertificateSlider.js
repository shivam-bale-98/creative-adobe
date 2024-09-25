import Swiper from "swiper";
import { Navigation } from "swiper/modules";

export default class CertificateSlider {
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
		this.CertificateSlider = $(".certificates-slider .swiper");
	};

	bindEvents = () => {
		new Swiper(".certificates-slider .swiper", {
			modules: [Navigation],
			slidesPerView: 1,
			centeredSlides: true,
			spaceBetween: 20,
			speed: 600,
			initialSlide: 1,
			loop: false,
			navigation: {
				nextEl: ".certificates-slider .swiper-button-next",
				prevEl: ".certificates-slider .swiper-button-prev",
			},
			breakpoints: {
				1900: {
					slidesPerView: 2,
					slidesOffsetAfter: 0,
					spaceBetween: 40,
				},
				991: {
					slidesPerView: 2,
					// slidesPerView: "auto",
					spaceBetween: 40,
				},
				767: {
					slidesPerView: 2,
					spaceBetween: 20,
				},
			},
		});
	};
}
