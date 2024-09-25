import Swiper from "swiper";
import { Navigation } from "swiper/modules";

export default class OurTeam {
	constructor() {
		this.init();
	}

	init = () => {
		this.setDomMap();
	
		if (this.window.width() <= 991) {
			this.slider();
		}
	};

	setDomMap = () => {
		this.window = $(window);
		this.body = $("body");
		this.ourTeam = document.querySelectorAll(".team-list");
		this.nextArrow = document.querySelectorAll(
			".team-list .swiper-button-next",
		);
		this.prevArrow = document.querySelectorAll(
			".team-list .swiper-button-prev",
		);
	};

	slider = () => {
		if (this.ourTeam) {
			const team_slider = new Swiper(this.ourTeam[0], {
				modules: [Navigation],
				slidesPerView: 1.3,
				spaceBetween: 12,
				speed: 1000,
				navigation: {
					nextEl: this.nextArrow[0],
					prevEl: this.prevArrow[0],
				},
				breakpoints: {
					767: {
						slidesPerView: 1.5,
						spaceBetween: 20,
					},
				},
			});
		}
	};
}
