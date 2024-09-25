import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import "swiper/css";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);
export default class CertificatePopupSlider {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.bindEvents();

    let t_out = 3200;
    if ($("body").hasClass("visited")) {
      t_out = 2000;
    }
    // setTimeout(() => {
    // 	this.animations();
    // }, t_out);
  };

  setDomMap = () => {
    this.window = $(window);
    this.html = $("html");
    this.body = $("body");
    this.description = $(".banner-v2");
  };

  bindEvents = () => {
    const sliders = document.querySelectorAll(".certificate-popup-slider");
    if (sliders.length > 0) {
      sliders.forEach((slider) => {
        const swiperContainer = slider.querySelector(".swiper");
        const prevButton = slider.querySelector(".swiper-button-prev");
        const nextButton = slider.querySelector(".swiper-button-next");
        if (swiperContainer) {
          const swiper = new Swiper(swiperContainer, {
            slidesPerView: 1,
            speed: 600,
            // autoHeight: true,
            spaceBetween: 0,
            observer: true,
            loop: false,
            modules: [Navigation],
            navigation: {
              nextEl: nextButton,
              prevEl: prevButton,
            },

            on: {
              //   init: (swiper) => {
              //   },
              slideChange: (swiper) => {
                // console.log(swiper.activeIndex);
              },
            },
          });

          $(document).on("click", ".card-item", function () {
            $("body").addClass("active");
            if ($("html").hasClass("lenis")) {
              lenis.stop();
            }

            setTimeout(() => {
              $(".certificate-popup-block").addClass("active");
            }, 100);

            // swiper.init();
            var slideIndex = $(this).data("slideindex");
            console.log(slideIndex);
            if (slideIndex === 1) {
              console.log('start');
            }

            if (slideIndex === swiper.slides.length) {
              console.log('end');
              console.log(swiper.navigation.nextEl);
              setTimeout(()=> {
                $(".certificate-popup-block .swiper-button-next").addClass('swiper-button-disabled');
                
              }, 100);

            }

            swiper.slideTo(slideIndex - 1); 
          });

          $(document).on(
            "click",
            ".certificate-popup-block .popup-close,.certificate-popup-block .t-overlay",
            function () {
              // Remove active class from all team-popup-block elements

              $(".certificate-popup-block").removeClass("active");

              setTimeout(() => {
                $("body").removeClass("active");
                if ($("html").hasClass("lenis")) {
                  lenis.start();
                }
              }, 500);
            }
          );

          // $(document).on("updateSwiper", ".certificate-popup-slider",
          //   function () {
          //     swiper.init();
          //   }
          // );
        }
      });
    }
  };

  // animations = () => {
  //   let results = this.description.find(".content p");

  //   gsap
  //     .timeline({
  //       delay: 1.4,
  //       scrollTrigger: {
  //         trigger: results,
  //         start: "top 90%",
  //       },
  //     })
  //     .to(results, 1, {
  //       opacity: 1,
  //       ease: "sine.out",
  //     });
  // };
}
