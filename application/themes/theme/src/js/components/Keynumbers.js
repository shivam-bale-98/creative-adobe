import Swiper from "swiper";
import gsap from "gsap";
import { Navigation, EffectFade, Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/effect-fade";

export default class Keynumbers {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    if(this.window.width() > 767) {
      this.slider();
    } else {
      this.mobileSlider();
    }
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    // this.Keynumbers = $(".key-numbers");
    this.keyNumbersSliders = document.querySelectorAll(
      ".key-numbers .key-numbers--wrappper .swiper"
    );

    this.keyNumbersSliderMobile = document.querySelectorAll(
      ".key-numbers .key-numbers--mobile .swiper"
    );
    // this.pagination = this.keyNumbersSliders.querySelectorAll('.pagination');
  };

  slider = () => {
    // this.keyNumbersSliders.forEach((keySlider) => {
     let keySliderLeft =  new Swiper('.key-numbers .key-numbers--wrappper .left .swiper', {
        modules: [Navigation, EffectFade, Autoplay, Pagination],
        slidesPerView: 1,
        speed: 2000,
        // loop: true,
        effect: "fade",
        fadeEffect: {
          crossFade: true,
        },
        // autoplay: true,
        touchEventsTarget: "container",
        allowTouchMove: false,
        pagination: {
          el: ".key-numbers .key-numbers--wrappper .left .swiper .pagination",
          clickable: true,
        },
      });
    // });

    let keySliderRight =  new Swiper('.key-numbers .key-numbers--wrappper .right .swiper', {
      modules: [Navigation, EffectFade, Autoplay, Pagination],
      slidesPerView: 1,
      speed: 2000,
      // loop: true,
      effect: "fade",
      fadeEffect: {
        crossFade: true,
      },
      // autoplay: true,
      touchEventsTarget: "container",
      allowTouchMove: false,
      
    });

    keySliderLeft.on("slideChange", () => {
      let index = keySliderLeft.realIndex;
      keySliderRight.slideTo(index); // Move imageSlider to the corresponding slide
    });

    // keySliderRight.on("slideChange", () => {
    //   let index = keySliderRight.realIndex;
    //   keySliderLeft.slideTo(index); // Move contentSlider to the corresponding slide
    // });
  };

  mobileSlider = () => {
    new Swiper('.key-numbers .key-numbers--mobile .swiper', {
      modules: [Navigation, EffectFade, Autoplay, Pagination],
      slidesPerView: "auto",
      speed: 800,
      initialSlide: 1,
      centeredSlides: true,
      pagination: {
        el: ".key-numbers--mobile .pagination",
        // dynamicmaBullets: true,
        clickable: true,
      },
    });
  };
}
