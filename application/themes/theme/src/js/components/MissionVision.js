import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { debounce } from "../utils";

gsap.registerPlugin(ScrollTrigger);
export default class MissionVision {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.hoverEvents();
    this.window.on("resize", () => {
      this.hoverEvents();
    });

    this.animation();
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.mainContainer = $(".mission-vision");
    this.cards = this.mainContainer.find(".m-card");
  };

  hoverEvents = () => {
    if (this.window.width() > 991) {
      let scaleValueX = 216 / this.cards.find(".img-wrap").width();
      let scaleValueY = 216 / this.cards.find(".img-wrap").height();
      let transformY = 50 + "px";
      // console.log(scaleValueX, scaleValueY);
      this.cards
        .find(".img-wrap")
        .css(
          "transform",
          `translateY(-${transformY}) scaleX(${scaleValueX}) scaleY(${scaleValueY})`
        );
    }
    if (this.window.width() > 1279) {
      this.cards.hover(
        debounce((e) => {
          let card = e.currentTarget;

          if (!$(card).hasClass("active")) {
            this.cards.removeClass("active");
            $(card).addClass("active");
          }
        }, 500)
      );
    } else if (this.window.width() > 991 && this.window.width() <= 1279) {
      this.cards.on("click", (e) => {
        let card = e.currentTarget;

        if (!$(card).hasClass("active")) {
          this.cards.removeClass("active");
          $(card).addClass("active");
        }
      });
    }
  };

  animation = () => {
    if (this.window.width() > 991) {
      let img = this.mainContainer.find(".right");

      let t1 = gsap.timeline({});

      t1.to($(img), {
        scale: 1,
        duration: 1,
        ease: "power2.out",
      });

      ScrollTrigger.create({
        trigger: this.mainContainer,
        start: "top 80%",
        animation: t1,
      });
    } else {
        let img = this.mainContainer.find(".img-wrap");

        $(img).each((ind, el)=> {
            let t1 = gsap.timeline({});

            t1.to($(el), {
                y: "0px",
                opacity: 1,
                duration: 1,
                ease: "sine.out"
            });

            ScrollTrigger.create({
                trigger: $(el),
                start: "top 80%",
                animation: t1
            })
        });
    }
  };
}
