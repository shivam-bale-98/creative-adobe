import Rellax from "rellax";
import { splitText } from "../utils";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class CaseStudy {
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
    this.mainContainer = $(".case-studies-list");
  };

  animations = () => {
    if (this.window.width() > 1279) {
      let t1 = gsap.timeline({});

      t1.to(this.mainContainer, {
        opacity: 1,
        duration: 1,
        ease: "circ.out",
      });

      ScrollTrigger.create({
        trigger: this.mainContainer,
        start: "top 85%",
        animation: t1,
      });
    } else {
      let card = this.mainContainer.find(".case-study-card");

      $(card).each((index, el) => {
        let t2 = gsap.timeline({});

        t2.to($(el), {
          y: "0px",
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
    }
  };
}
