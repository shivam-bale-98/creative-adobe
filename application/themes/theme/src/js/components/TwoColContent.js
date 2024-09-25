import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class TwoColContent {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    setTimeout(() => {
      this.animation();
    }, 1000);
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.TwoColContent = $(".two-col-section");
    // this.ImageSection = this.TwoColContent.find('.image-col');
    this.twoColContentImages = this.TwoColContent.find(".img-wrap");
    this.twoColContentImage1 = this.TwoColContent.find(".img-wrap:nth-child(1)");
    this.twoColContentImage2 = this.TwoColContent.find(".img-wrap:nth-child(2)");
    this.twoColContentImage3 = this.TwoColContent.find(".img-wrap:nth-child(3)");
  };

  animation = () => {
    if (this.window.width() >= 991) {
      let t1 = gsap.timeline({
        scrollTrigger: {
          trigger: this.TwoColContent.find(".image-col"),
          start: "top 90%",
          end: "top 30%",
          invalidateOnRefresh: true,
          scrub: 1,
          // ease: "sine.out"
          ease: "power3.out",
        },
      });

      t1.to(this.twoColContentImage2, {
        y: "0px",
        duration: 1.2,
      });

      let t2 = gsap.timeline({
        scrollTrigger: {
          trigger: this.TwoColContent.find(".image-col"),
          start: "top 90%",
          end: "top 30%",
          invalidateOnRefresh: true,
          scrub: 1,
          // ease: "sine.out"
          ease: "power2.out",
        },
      });

      t2.to(this.twoColContentImage3, {
        y: "0px",
        duration: 1.2,
      });
    } else if (this.window.width() < 991 && this.window.width() >= 767) {
       let t1 = gsap.timeline();

       t1.to(this.twoColContentImage1, {
        opacity: 1,
        y:0,
        duration: 1,
        ease: "power2.out"
       });

       ScrollTrigger.create({
        trigger: this.twoColContentImages,
        start: "top 80%",
        animation: t1
       });

       let t2 = gsap.timeline({
        delay: 0.2
       });

       t2.to(this.twoColContentImage2, {
        opacity: 1,
        y:0,
        duration: 1,
        ease: "power2.out"
       });

       ScrollTrigger.create({
        trigger: this.twoColContentImages,
        start: "top 80%",
        animation: t2
       });
    } else {
      let t1 = gsap.timeline();

      t1.to(this.twoColContentImages, {
       opacity: 1,
       ease: "power2.out"
      });

      ScrollTrigger.create({
       trigger: this.twoColContentImages,
       start: "top 90%",
       animation: t1
      })
    }
  };
}
