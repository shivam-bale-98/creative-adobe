import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class Clients {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    setTimeout(() => {
      this.animations();
    }, 2000);
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.clients = $(".our-clients");
  };

  animations = () => {
    let dur = 0
    if (this.window.width() > 991) {
        dur = 1.2;
    } else {
      dur = 0.8;
    }
      // let cardsgroup = this.clients.find(".card-group");
      // let totalAnimations = cardsgroup.length;
      // let animationsCompleted = 0;

      // $(cardsgroup).each((i, el) => {
      //   let cards = $(el).find(".card--1");

      //   let t1 = gsap.timeline({
      //     // onComplete: () => {
      //     //   animationsCompleted++;
      //     //   if (animationsCompleted === totalAnimations) {
      //     //       this.clients.addClass('animated');
      //     //   }
      //     // },
      //     onUpdate: () => {
      //       // let prog = t1.progress();

      //       // if (prog >= 0.99) {
      //       //   animationsCompleted++;
      //       //   if (animationsCompleted === totalAnimations) {
      //       //     this.clients.addClass("animated");
      //       //   }
      //       // }
      //     },
      //   });

      //   // t1.to($(cards), {
      //   //   y: "0px",
      //   //   opacity: 1,
      //   //   ease: "sine.out",
      //   //   duration: 1,
      //   // });

      //   // ScrollTrigger.create({
      //   //   trigger: $(el),
      //   //   start: "top 85%",
      //   //   animation: t1,
      //   // });
      // });
    // } else {
      let container = this.clients.find(".right");

      let t1 = gsap.timeline({});

      t1.to($(container), {
        // y: 0,
        opacity: 1,
        ease: "sine.out",
        duration: dur,
      });

      ScrollTrigger.create({
        trigger: $(container),
        start: "top 80%",
        animation: t1,
      });
    // }
  };

  chunkArray = (array, size) => {
    const chunkedArray = [];
    for (let i = 0; i < array.length; i += size) {
      chunkedArray.push(array.slice(i, i + size));
    }
    return chunkedArray;
  };
}
