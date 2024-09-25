import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import Swiper from "swiper";
import { Navigation } from "swiper/modules";

gsap.registerPlugin(ScrollTrigger);

export default class CsDetail {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    let timeout = 2000;
    if ($(this.body).hasClass("visited")) {
      timeout = 900;
    }
    setTimeout(() => {
      this.animations();
    }, timeout);

    this.bindEvents();
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.CsDetailBlock = $(".cs-details");
    this.relatedCaseStudies = $('.related-case-studies');
  };

  bindEvents = () => {
    if (this.relatedCaseStudies.length) {
			new Swiper(".related-case-studies .swiper", {
				modules: [Navigation],
				slidesPerView: 1.1,
				spaceBetween: 15,
				speed: 1000,
				navigation: {
					nextEl: ".related-case-studies .swiper-button-next",
					prevEl: ".related-case-studies .swiper-button-prev",
				},

				breakpoints: {
					767: {
						slidesPerView: 1.5,
						spaceBetween: 20,
					},
					991: {
						slidesPerView: 2.1,
						spaceBetween: 20,
					},
					1279: {
						slidesPerView: "auto",
						spaceBetween: 60,
					},
				},

				on: {
					init : () => {
						
					}
				},
			});
		}
  };

  animations = () => {
    // let content = this.CsDetailBlock.find(".content");
    // console.log($(content));
    // $(content).each((i, el) => {
    //   let t1 = gsap.timeline({
    //     // onUpdate: () => {
    //     //   if (t1.progress() >= 0.7 && !t2.isActive()) {
    //     //     t2.play();
    //     //   }
    //     // },
    //   });

    //   // t1.to($(el), {
    //   //   opacity: 1,
    //   //   ease: "sine.out",
    //   //   duration: 0.8,
    //   // });

    //   // ScrollTrigger.create({
    //   //   trigger: $(el),
    //   //   start: "top 80%",
    //   //   animation: t1,
    //   // });

    //   // let line = $(el).find(".line");

    //   // let t2 = gsap.timeline({ paused: true, onComplete: () => {
    //   //   $(el).addClass('animated');
    //   // } });
    //   // t2.to($(line), {
    //   //   scaleX: 0,
    //   //   ease: "power4.out",
    //   //   duration: 2,
    //   // });
    // });



    //related cs 
    let card1 = this.relatedCaseStudies.find('.swiper-slide:nth-child(1)');
    let card2 = this.relatedCaseStudies.find('.swiper-slide:nth-child(2)');
    let card3 = this.relatedCaseStudies.find('.swiper-slide:nth-child(3)');
    
    let cards = [card1, card2, card3]
    let c1 = gsap.timeline({});

    c1.to($(cards), {
      x: 0,
      opacity: 1,
      duration: 0.8,
      stagger: 0.33,
      ease: 'power3.out'
    });

    ScrollTrigger.create({
      trigger: this.relatedCaseStudies.find('.swiper'),
      start: 'top 80%',
      animation: c1
    })
  };
}
