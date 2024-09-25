import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export default class sticky {
	constructor() {
		this.init();
		this.bindEvents();
	}

	init = () => {
		this.window = $(window);
		this.detailsContentBlock = $(".details-content");
	};
	bindEvents = () => {
		let innerWidth = $(window).width;
		if (innerWidth >= 991) {
			ScrollTrigger.create({
				trigger: ".bd-position-sticky",
				start: "-100 top",
				end: "10% +20px center",
				pin: ".inner-sticky",
				pinSpacing: false,
				// markers: true,
				onToggle: function (self) {
					$(self.trigger).toggleClass("scrolled", self.isActive);
				},
			});
		} else {
			ScrollTrigger.create({
				trigger: ".bd-position-sticky",
				start: "-100 top",
				end: "90% +110px center",
				pin: ".inner-sticky",
				pinSpacing: false,
				// markers: true,
				onToggle: function (self) {
					$(self.trigger).toggleClass("scrolled", self.isActive);
				},
			});
		}
	};
}
