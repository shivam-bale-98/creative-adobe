import gsap from "gsap";
import Swiper from "swiper";
import {
  Navigation,
  Thumbs,
  Autoplay,
  FreeMode,
  EffectFade,
  Controller,
  EffectCreative,
} from "swiper/modules";

import { splitText } from "../utils";

Swiper.use([
  EffectFade,
  EffectCreative,
  Autoplay,
  Navigation,
  Controller,
]);

export default class Banner {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    let timeout = 100;
    if ($(this.body).hasClass("visited")) {
      timeout = 0;
    }
    this.slider();
    setTimeout(() => {
      this.animations();
    }, timeout);
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.header = $("header.header");
    this.banner = $(".banner-v1");
    this.section = document.querySelectorAll(".banner-v1");

    this.prevArrow = this.section[0].querySelectorAll(
			".swiper-buttons .swiper-button__prev",
		);
		this.nextArrow = this.section[0].querySelectorAll(
			".swiper-buttons .swiper-button__next",
		);
  };

  slider = () => {
    if (this.section) {
      this.section.forEach((section, index) => {
        const imageSliderContainer = section.querySelectorAll(
					".home_banner_slider",
				)[0];

        const contentSliderContainer = section.querySelectorAll(
					".banner_conent",
				)[0];

        this.imageSlider = new Swiper(imageSliderContainer, {
					modules: [Navigation, Thumbs, Autoplay, EffectCreative, EffectFade],
					spaceBetween: 10,
					sliderPerView: 1,
					speed: 1200,
					allowTouchMove: false,
					effect: "fade",

					// autoplay: {
					// 	delay: 8000,
					// 	disableOnInteraction: false,
					// },
					navigation: {
						nextEl: this.nextArrow[index],
						prevEl: this.prevArrow[index],
					},
				});

        this.contentSlider = new Swiper(contentSliderContainer, {
					modules: [Navigation, Thumbs, Autoplay, EffectCreative, EffectFade],
					spaceBetween: 10,
					sliderPerView: 1,
					speed: 1000,
					allowTouchMove: false,
					effect: "fade",
          fadeEffect: {
            crossFade: true,
          },
					// autoplay: {
					// 	delay: 8000,
					// 	disableOnInteraction: false,
					// },
				});     
        
        
        this.imageSlider.on("slideChange", () => {
          let index = this.contentSlider.realIndex;
        });
    
        this.imageSlider.controller.control = this.contentSlider;
        this.contentSlider.controller.control = this.imageSlider;
      });
    }
  };


  animations = () => {
    // if (this.body.hasClass("visited")) {
    let t1 = gsap.timeline({
      onComplete: () => {
        this.header.addClass("loaded");
      },
    });

    t1.to(this.header.find(".header-wrapper"), {
      y: "0px",
      opacity: 1,
      duration: 0.8,
      ease: "sine.out",
    });

    let h1 = this.banner.find("h1");

    let t2 = gsap.timeline({});

    t2.to($(h1), {
      opacity: 1,
      duration: 1,
      ease: "sine.out",
    });

    let t3 = gsap.timeline();

    t3.to(this.banner.find("h6"), {
      opacity: 1,
      duration: 0.5,
      ease: "sine.out",
    });

    // }
  };
}
