import Swiper from "swiper";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

import { Navigation } from "swiper/modules";

gsap.registerPlugin(ScrollTrigger);

export default class Testimonials {
	constructor() {
		this.init();
	}

	init = () => {
		this.setDomMap();
		setTimeout(() => {
			this.slider();
		}, 5000);
	};

	setDomMap = () => {
		this.window = $(window);
		this.body = $("body");
		this.testimonialSlider = $(".testimonials .swiper");
	};

	slider = () => {
		let activeWidth = "";
		let normalWidth = "";
		let slides = "";
		let activeSlide = "";
		let t1 = gsap.timeline({});
		let t2 = gsap.timeline({ paused: true });
		new Swiper(".testimonials .swiper", {
			modules: [Navigation],
			slidesPerView: 1,
			spaceBetween: 20,
			speed: 600,
			slidesOffsetAfter: 0,
			navigation: {
				nextEl: ".testimonials .swiper-button-next",
				prevEl: ".testimonials .swiper-button-prev",
			},
			breakpoints: {
				1900: {
					slidesOffsetAfter: 650,
					slidesPerView: "auto",
					spaceBetween: 50,
				},
				991: {
					slidesPerView: "auto",
					spaceBetween: 30,
					slidesOffsetAfter: 500,
				},

				767: {
					slidesPerView: 2,
					spaceBetween: 30,
					slidesOffsetAfter: 0,
				},
			},
			on: {
				init: (swiper) => {
					if (this.window.width() > 991) {
						setTimeout(() => {
							console.log(swiper.params.slidesOffsetAfter);
							let activeIndex = swiper.activeIndex;
							slides = this.testimonialSlider.find(".swiper-slide");
							let nonActiveSlide = this.testimonialSlider.find(
								".swiper-slide:not(.swiper-slide-active)",
							);

							activeWidth = this.testimonialSlider
								.find(".swiper-slide-active")
								.outerWidth();
							normalWidth = $(nonActiveSlide).outerWidth();

							console.log(activeWidth, normalWidth);

							$(slides).each((index, slide) => {
								if (index === activeIndex) {
									$(slide).css("width", `${activeWidth}px`);
								} else {
									$(slide).css("width", `${normalWidth}px`);
								}
							});

							activeSlide = this.testimonialSlider.find(".swiper-slide-active");
							gsap.to($(activeSlide).find(".content"), {
								scaleX: 1,
								duration: 0.5,
								ease: "sine.out",
							});
							gsap.to($(activeSlide).find(".box"), {
								y: "0px",
								opacity: 1,
								duration: 0.8,
								ease: "sine.out",
								delay: 0.2,
							});
						}, 2000);
					}
					this.animations();
				},

				slideChange: (swiper) => {
					if (this.window.width() > 991) {
						let activeIndex = swiper.activeIndex;
						let slides = this.testimonialSlider.find(".swiper-slide");

						$(slides).each((index, slide) => {
							if (index === activeIndex) {
								$(slide).css("width", `${activeWidth}px`);
							} else {
								$(slide).css("width", `${normalWidth}px`);
							}
						});
					}
				},

				slideChangeTransitionEnd: () => {
					if (this.window.width() > 991) {
						activeSlide = this.testimonialSlider.find(".swiper-slide-active");

						gsap.to($(activeSlide).find(".content"), {
							scaleX: 1,
							duration: 0.5,
							ease: "sine.out",
						});
						gsap.to($(activeSlide).find(".box"), {
							y: "0px",
							opacity: 1,
							duration: 0.8,
							ease: "sine.out",
							delay: 0.4,
						});
					}
				},

				slideChangeTransitionStart: () => {
					if (this.window.width() > 991) {
						gsap.to($(slides).find(".content"), {
							scaleX: 0,
							duration: 0.5,
							ease: "sine.out",
						});

						gsap.to($(slides).find(".box"), {
							y: "2rem",
							opacity: 0,
							duration: 0.5,
							delay: 0.3,
							ease: "sine.out",
						});
					}
				},
			},
		});
	};

	animations = () => {
		if (this.window.width() > 991) {
			let card1 = this.testimonialSlider.find(
				".swiper-slide:nth-child(2) .testimony-card",
			);
			let card2 = this.testimonialSlider.find(
				".swiper-slide:nth-child(3) .testimony-card",
			);

			let cards = [card1, card2];

			let t1 = gsap.timeline({});

			t1.to(cards, {
				opacity: 1,
				x: 0,
				duration: 1,
				stagger: 0.2,
				ease: "sine.out",
			});

			ScrollTrigger.create({
				trigger: $(card1),
				animation: t1,
				start: "top 80%",
			});
		} else {
			let t1 = gsap.timeline({});

			t1.to(this.testimonialSlider, {
				opacity: 1,
				y: 0,
				duration: 1,
				ease: "sine.out",
			});

			ScrollTrigger.create({
				trigger: this.testimonialSlider,
				start: "top 80%",
				animation: t1,
			});
		}
	};
}
