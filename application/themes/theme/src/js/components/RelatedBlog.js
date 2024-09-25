import Swiper from "swiper";
import { Navigation } from "swiper/modules";

export default class RelatedBlog {
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
  };

  bindEvents = () => {
    new Swiper(".related-blogs .swiper", {
        modules: [Navigation],
        slidesPerView: 1.1,
        spaceBetween: 20,
        speed: 1000,
        slidesOffsetAfter: 37,
        navigation: {
            nextEl: ".related-blogs .swiper-button-next",
            prevEl: ".related-blogs .swiper-button-prev",
        },
        breakpoints: {
            1900: {
              slidesPerView: 3,
              slidesOffsetAfter: 200,
            },
            991: {
              slidesPerView: 2.2,
              spaceBetween: 60,
              slidesOffsetAfter: 150,
            },
    
            767: {
              slidesPerView: 2,
              spaceBetween: 30,
              slidesOffsetAfter: 0,
            },
          },
    });
  };
}
