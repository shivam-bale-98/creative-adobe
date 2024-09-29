import { debounce } from "../utils";
import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class TechnologyCardSlider {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.bindEvents();

    let timeout = 2000;
    if ($(this.body).hasClass("visited")) {
      timeout = 3200;
    }
    setTimeout(() => {
      this.animations();
    }, timeout);
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.technologyBlock = document.querySelectorAll(".techologies-slider");
    this.technologySlider = this.technologyBlock[0].querySelectorAll(
      ".technology-card--slider"
    );

    this.nextEl = this.technologyBlock[0].querySelectorAll(
      ".swiper-button-next"
    );
    this.prevEl = this.technologyBlock[0].querySelectorAll(
      ".swiper-button-prev"
    );
    this.technologyBlockJ = $(".techologies-slider");
  };

  bindEvents = () => {
    new Swiper(this.technologySlider[0], {
      modules: [Navigation],
      slidesPerView: "auto",
      spaceBetween: 30,
      speed: 1000,
      freeMode: true,
      navigation: {
        nextEl: '.techologies-slider  .swiper-button-next ',
        prevEl: '.techologies-slider  .swiper-button-prev',
      },
      breakpoints: {
        991: {
          slidesPerView: 4.5,
          spaceBetween: 100,
        },
        767: {
          // slidesPerView: "auto",
          spaceBetween: 60,
        },
      },
      on: {
        beforeInit: () => {
          var maxHeight = 0;
          let card = this.technologyBlockJ.find('.swiper-slide');
          // Iterate over each card
          setTimeout(()=> {
            $(card).each(function () {
              // Get the height of the current card
              var currentHeight = $(this).height();
  
              // Compare with the current maximum height
              if (currentHeight > maxHeight) {
                // Update the maximum height if needed
                maxHeight = currentHeight;
              }
            });
             console.log(maxHeight)
            $(card).css('height', `${maxHeight}`);
          }, 1000)
        },
        init: () => {
          if (this.window.width() > 1279) {
            // let timeOut = "";
            // let isHovered = false;
            // let cards = this.technologyBlockJ.find(".technology-card");
            // let scaleX = 192 / $(cards).find(".img-wrap").width();
            // let scaleY = 169 / $(cards).find(".img-wrap").height();
            // let translateX = -130 + "px";
            // let translateY = -400 + "px";
            // $(cards)
            //   .find(".img-wrap")
            //   .css("transform", `scaleX(${scaleX}) scaleY(${scaleY})`);
            // const handleMouseEnter = debounce((e) => {
            //   console.log("trigger animation");
            //   let el = e.currentTarget;
            //   if (!$(el).hasClass("active")) {
            //     $(el).addClass("active");
            //   }
            // }, 5);
            // $(cards).on("mouseenter", (e) => {
            //   clearTimeout(timeOut);
            //   console.log("fire");
            //   timeOut = setTimeout(() => {
            //     isHovered = true;
            //     handleMouseEnter(e);
            //   }, 5);
            // });
            // $(cards).on(
            //   "mouseleave",
            //   debounce((e) => {
            //     clearTimeout(timeOut);
            //     isHovered = false;
            //     let el = e.currentTarget;
            //     if ($(el).hasClass("active")) {
            //       $(el).removeClass("active");
            //     }
            //   }, 5)
            // );
          }
        },
      },
    });
  };

  animations = () => {
    let card = this.technologyBlockJ.find(".swiper-slide ");

    let t1 = gsap.timeline({});

    t1.to($(card), {
      y: "0px",
      opacity: 1,
      duration: 1,
      stagger: 0.07,
      ease: "sine.out",
    });

    ScrollTrigger.create({
      trigger: this.technologyBlockJ.find(".swiper"),
      start: "top 80%",
      animation: t1,
    });
  };
}
