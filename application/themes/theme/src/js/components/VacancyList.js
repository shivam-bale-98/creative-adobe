import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export default class VacancyList {
	constructor() {
		this.init();
		this.bindEvents();
	}

	init = () => {
		this.window = $(window);
		this.mainContainer = $(".vacancy-listing--page");
	};
	bindEvents = () => {
		gsap.fromTo(
			".head",
			{
				opacity: 0,
			},
			{
				opacity: 1,
				duration: 1.2,
				ease: "sine.out",
				scrollTrigger: {
					trigger: ".head",
					start: "top 90%",
					delay: 0.5,
				},
			},
		);
	};
}
