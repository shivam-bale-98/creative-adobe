import Swiper from "swiper";
import gsap from "gsap";
// import { Navigation, EffectFade, Autoplay } from "swiper/modules";

export default class SearchSlider {
	constructor() {
		this.init();
	}

	init = () => {
		this.setDomMap();
		this.bindEvents();
		// setTimeout(()=>)
	};

	setDomMap = () => {
		this.window = $(window);
		this.body = $("body");
	};

	bindEvents = () => {
		const imageSlider = new Swiper(".searchSlider", {
			direction: "horizontal",
			slidesPerView: "auto",
			speed: 800,
			// loop: true,
			spaceBetween: 10,
			breakpoints: {
				991: {
					// direction: "vertical",
					// slidesPerView: "auto",
				},

				767: {
					// slidesPerView: 1.5,
				},
			},
		});

		// On Click Add Active Class
		const tabs = document.querySelectorAll(".channeline-tab");
		tabs.forEach((tab) => {
			tab.addEventListener("click", function () {
				tabs.forEach((t) => t.classList.remove("active"));
				tab.classList.add("active");
			});
		});
	};
}
