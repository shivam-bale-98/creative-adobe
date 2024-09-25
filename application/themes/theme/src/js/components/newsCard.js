import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export default class BrandsListing {
  constructor() {
    this.init();
    this.bindEvents();
  }

  init = () => {
    this.window = $(window);
    this.mainContainer = $(".news-list");
  };
  bindEvents = () => {
    let t_out = 2000;
    if ($(this.body).hasClass("visited")) {
      t_out = 900;
    }
    setTimeout(() => {
      this.gridAnimations();
    }, t_out);
  };

  gridAnimations = () => {
    // let newsListCards = this.mainContainer.find(".news-listing-card");
    let newsListCards = this.mainContainer.find(
      ".news-listing-card:not(.animated)"
    );

    $(newsListCards).each((ind, el) => {
      let cards = $(el);

      let tg2 = gsap.timeline({ delay: 0.07 * ind });
      // let tg20 = gsap.timeline({});

      tg2.to(cards, 0.6, {
        opacity: 1,
        y: "0px",
        ease: "sine.out",
      });
      // tg20.to(cards, 0.7, { , ease: "power2.out", delay: 0.07 * ind });

      ScrollTrigger.create({
        trigger: cards,
        start: "top 90%",
        animation: tg2,
      });

      // ScrollTrigger.create({
      // 	trigger: cards,
      // 	start: "top 98%",
      // 	animation: tg20,
      // });

      cards.addClass("animated");
    });
  };
}
