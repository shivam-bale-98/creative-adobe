import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";


export default class DownloadSlider {
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
        this.downloadsBlock =  document.querySelectorAll('.download--block');
        this.cardsSlider = this.downloadsBlock[0].querySelectorAll('.download-cards-list');
        
        

        
    }

    bindEvents = () => {
        new Swiper(this.cardsSlider[0], {
            modules: [Navigation],
            slidesPerView: "auto",
            spaceBetween: 20,
            speed: 1000,
            navigation: {
              nextEl: this.NextEl[0],
              prevEl: this.prevEl[0],
            },
          });
    }
}