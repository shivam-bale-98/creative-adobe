import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class ThreeCol {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
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
    this.threeColBlock = $(".three-col-block");
  };

  animations = () => {
    let cards = this.threeColBlock.find(".three-col-card");

    if (this.window.width() > 991) {
      $(cards).each((i, el) => {
        let img = $(el).find(".img-wrap");
        let title = $(el).find(".content h4");
        let desc = $(el).find(".content p");
        let line = $(el).find(".line");

        let t1 = gsap.timeline({});
        t1.to($(img), {
          x: "0px",
          opacity: 1,
          duration: 0.8,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        let t2 = gsap.timeline({});
        t2.to($(title), {
          opacity: 1,
          duration: 0.8,
          ease: "power2.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t2,
        });

        let t3 = gsap.timeline({ delay: 0.2 });
        t3.to($(desc), {
          opacity: 1,
          duration: 0.8,
          ease: "power2.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t3,
        });

        let t4 = gsap.timeline();
        t4.to($(line), {
          scaleX: 1,
          duration: 2,
          ease: "power3.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t4,
        });
      });
    } else if(this.window.width() < 991 && this.window.width() > 767 ) {
      $(cards).each((i, el) => {
        let img = $(el).find(".img-wrap");
        let t1 = gsap.timeline({onComplete: ()=> {
            t4.play();
        }});
        t1.to($(img), {
          x: "0px",
          opacity: 1,
          duration: 0.8,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        let content = $(el).find(".content");
        let t2 = gsap.timeline({});
        t2.to($(content), {
          y: "0px",
          opacity: 1,
          duration: 0.8,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t2,
        });

        let line = $(el).find(".line");

        let t4 = gsap.timeline({paused: true});
        t4.to($(line), {
          scaleX: 1,
          duration: 1,
          ease: "power3.out",
        });

        
      });
    } else {
      $(cards).each((i, el) => {
      
        let t1 = gsap.timeline({onComplete: ()=> {
            t4.play();
        }});
        t1.to($(el), {
          opacity: 1,
          duration: 0.8,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        

        let line = $(el).find(".line");

        let t4 = gsap.timeline({paused: true});
        t4.to($(line), {
          scaleX: 1,
          duration: 1,
          ease: "power3.out",
        });

        
      });
    }
  };
}
