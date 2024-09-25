import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class TwoColList {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.animations();
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.twoColBlock = $(".two-col-list");
  };

  animations = () => {
    if (this.window.width() > 767) {
      let list = this.twoColBlock.find(".wrp");

      $(list).each((index, el) => {
        let left = $(el).find(".left");
        let right = $(el).find(".right");

        let t1 = gsap.timeline({});

        t1.to($(left), {
          x: 0,
          opacity: 1,
          duration: 1,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        let t2 = gsap.timeline({});

        t2.to($(right), {
          x: 0,
          opacity: 1,
          duration: 1,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t2,
        });
      });
    } else {
        let list = this.twoColBlock.find(".wrp");

        $(list).each((index, el)=> {
            let content = $(el).find('.content');

           let  t1 = gsap.timeline({});

            t1.to($(content), {
                y: "0px",
                opacity: 1,
                duration: 1,
                ease: "sine.out"
            });

            ScrollTrigger.create({
                trigger: $(content),
                start: "top 90%",
                animation: t1
            });


            let image = $(el).find('.image');

            let t2 = gsap.timeline({});

            t2.to($(image), {
                y: "0px",
                opacity: 1,
                duration: 1,
                ease: "sine.out"
            });

            ScrollTrigger.create({
                trigger: $(image),
                start: "top 90%",
                animation: t2
            });
        })
    }
  };
}
