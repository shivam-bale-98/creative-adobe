import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class ImageTextBlock {
	constructor() {
		this.init();
	}

	init = () => {
		this.setDomMap();
		this.animations();
	};

	setDomMap = () => {
		this.window = $(window);
		this.body = $("body");
		this.imageTextBlock = $(".imageTextBlock");
	};

	animations = () => {
		if (this.window.width() > 767) {
			gsap.set(".bdFadeLeft", { x: -80, opacity: 0 });

			const fadeArray = gsap.utils.toArray(".bdFadeLeft");
			fadeArray.forEach((item, i) => {
				let fadeTl = gsap.timeline({
					scrollTrigger: {
						trigger: item,
						start: "top 90%",
					},
				});
				fadeTl.to(item, {
					x: 0,
					opacity: 1,
					ease: "power2.out",
					duration: 1,
				});
			});

			gsap.set(".bdFadeRight", { x: 80, opacity: 0 });
			const fadeRight = gsap.utils.toArray(".bdFadeRight");
			fadeRight.forEach((item, i) => {
				let fadeTl = gsap.timeline({
					scrollTrigger: {
						trigger: item,
						start: "top 90%",
					},
				});
				fadeTl.to(item, {
					x: 0,
					opacity: 1,
					ease: "power2.out",
					duration: 1,
				});
			});
		}
	};
}
