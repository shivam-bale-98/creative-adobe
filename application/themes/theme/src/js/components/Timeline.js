import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class Timeline {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.sliders();
    if (this.window.width() > 767) {
      this.window.resize(() => {
        this.resizeHandler();
      });
    }
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.timelineJ = $(".timeline-slider.not-animated");
    this.timeline = document.querySelectorAll(".timeline-slider");
    this.yearSwiper = this.timeline[0].querySelectorAll(".year-slider");
    this.timelineCardSlider = this.timeline[0].querySelectorAll(".card-slider");
    this.navigation = this.timeline[0].querySelectorAll(".swiper-buttons");

    this.swiperPrev = this.timeline[0].querySelectorAll(".swiper-button-prev");
    this.swiperNext = this.timeline[0].querySelectorAll(".swiper-button-next");
  };

  sliders = () => {
    const yearSwiper = new Swiper(this.yearSwiper[0], {
      modules: [Navigation],
      slidesPerView: "auto",
      spaceBetween: 110,
      initialSlide: 2,
      speed: 1000,
      slideToClickedSlide: true,
      centeredSlides: true,
      breakpoints: {
        767: {
          spaceBetween: 80,
        },
        991: {
          spaceBetween: 150,
        },
        1300: {
          spaceBetween: 200,
        },
      },
      navigation: {},
    });

    const cardSlider = new Swiper(this.timelineCardSlider[0], {
      modules: [Navigation],
      slidesPerView: 1,
      spaceBetween: "20",
      autoHeight: true,
      initialSlide: 2,
      speed: 1000,
      centeredSlides: true,
      breakpoints: {
        767: {
          spaceBetween: 140,
          slidesPerView: "auto",
          autoHeight: false
        },
        991: {
          spaceBetween: 190,
          slidesPerView: "auto",
          autoHeight: false
        },
        1279: {
          spaceBetween: 350,
          slidesPerView: "auto",
          autoHeight: false
        },
        1400: {
          spaceBetween: 440,
          slidesPerView: "auto",
          autoHeight: false
        },
        1600: {
          spaceBetween: 540,
          slidesPerView: "auto",
          autoHeight: false
        },
      },
      navigation: {
        nextEl: this.swiperNext[0],
        prevEl: this.swiperPrev[0],
      },
      on: {
        init: () => {
          let timeOut = 3000;
          setTimeout(()=> {
            this.animations();
          },  timeOut)
          if (this.window.width() > 767) {
            this.resizeHandler();
          }
        },

      },
    });

    cardSlider.on("slideChange", () => {
      let index = cardSlider.realIndex;
      yearSwiper.slideTo(index); // Move imageSlider to the corresponding slide
    });

    yearSwiper.on("slideChange", () => {
      let index = yearSwiper.realIndex;
      cardSlider.slideTo(index); // Move contentSlider to the corresponding slide
    });
  };

  animations = () => {
     if(this.window.width() > 767) {
      let line = this.timelineJ.find(".line .animate");

      let t1 = gsap.timeline({});
  
      t1.to($(line), {
        scaleX: 1,
        duration: 2,
        ease: "sine.out",
      });
  
      ScrollTrigger.create({
        trigger: this.timelineJ,
        start: "top 90%",
        animation: t1,
      });
  
      let t2 = gsap.timeline({ delay: 0.2 });
  
      t2.to(this.timelineJ.find(".year-slider"), {
        opacity: 1,
        duration: 1,
        ease: "sine.out",
      });
  
      ScrollTrigger.create({
        trigger: this.timelineJ.find(".year-slider"),
        start: "top 90%",
        animation: t2,
      });
  
      let activeCard = this.timelineJ.find(".card-slider .swiper-slide-active");
  
      let t3 = gsap.timeline({
        onComplete: () => {
          // t4.play();
          t5.play();
        },
      });
  
      t3.to($(activeCard), {
        y: "0px",
        opacity: 1,
        duration: 1,
        ease: "power2.out",
      });
  
      ScrollTrigger.create({
        trigger: $(activeCard),
        start: "top 80%",
        animation: t3,
      });
  
      let prevCard = this.timelineJ.find(".card-slider .swiper-slide-prev");
      let nextCard = this.timelineJ.find(".card-slider .swiper-slide-next");
      let cards = [nextCard, prevCard];
  
      $(cards).each((index, el) => {
        let t4 = gsap.timeline({delay: 0.2});
  
        t4.to($(el), {
          x: "0px",
          opacity: 1,
          duration: 0.8, 
          ease: "power2.out",
        });
  
        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t4,
        });
      });
  
      let navs = this.timelineJ.find(".swiper-buttons");
  
      let t5 = gsap.timeline({ paused: true, onComplete: ()=> {
        this.timelineJ.removeClass('not-animated')
      } });
  
      t5.to($(navs), {
        opacity: 1,
        duration: 0.6,
        ease: "power2.out",
      });
     } else {
      let line = this.timelineJ.find(".line .animate");

      let t1 = gsap.timeline({});
  
      t1.to($(line), {
        scaleX: 1,
        duration: 1.5,
        ease: "sine.out",
      });
  
      ScrollTrigger.create({
        trigger: this.timelineJ,
        start: "top 90%",
        animation: t1,
      });

      let t2 = gsap.timeline({ delay: 0.2 });
  
      t2.to(this.timelineJ.find(".year-slider"), {
        opacity: 1,
        duration: 1,
        ease: "sine.out",
      });
  
      ScrollTrigger.create({
        trigger: this.timelineJ.find(".year-slider"),
        start: "top 90%",
        animation: t2,
      });

      let activeCard = this.timelineJ.find(".card-slider .swiper-slide-active");

      let t3 = gsap.timeline({
        onComplete: ()=> {
          this.timelineJ.removeClass('not-animated')
        }
      });
  
      t3.to($(activeCard), {
        y: "0px",
        opacity: 1,
        duration: 0.8,
        ease: "power2.out",
      });
  
      ScrollTrigger.create({
        trigger: $(activeCard),
        start: "top 80%",
        animation: t3,
      });

      let navs = this.timelineJ.find(".swiper-buttons");
  
      let t5 = gsap.timeline({
        
      });
  
      t5.to($(navs), {
        opacity: 1,
        duration: 0.8,
        ease: "power2.out",
      });

      ScrollTrigger.create({
        trigger: $(navs),
        start: "top 80%",
        animation: t5,
      });

      
     }
  };

  resizeHandler = () => {
    let cardHeight = this.timelineJ
      .find(".card-slider .swiper-slide .img-wrap")
      .height();

    console.log(cardHeight);
    let top = cardHeight / 2 - 44 + "px";

    this.timelineJ.find(".swiper-buttons").css("top", top);
  };
}
