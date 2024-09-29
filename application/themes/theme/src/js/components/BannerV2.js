import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class Banner {
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

    if (this.window.width() < 991) {
      this.truncateBreadCrumbs();
    }
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.header = $("header.header");
    this.banner = $(".banner-v2");
  };

  animations = () => {
    let t0 = gsap.timeline({
      onComplete: () => {
        this.header.addClass("loaded");
      },
    });

    t0.to(this.header.find(".header-wrapper"), {
      y: "0px",
      opacity: 1,
      duration: 0.8,
      ease: "sine.out",
    });



    let t1 = gsap.timeline({});
    

    t1.to(this.banner.find(".breadcrumb-wrap"), {
      opacity: 1,
      duration: 0.5,
      ease: "sine.out",
    });

    let t2 = gsap.timeline({});

    t2.to(this.banner.find(".content h1"), {
      opacity: 1,
      duration: 0.8,
      ease: "sine.out",
    });

    if (this.banner.find(".content p").length) {
      let t21 = gsap.timeline({ delay: 0.1 });
      t21.to(this.banner.find(".content p"), {
        opacity: 1,
        duration: 0.8,
        ease: "sine.out",
      });
    }

    if (this.banner.find(".img-wrap").length) {
      let t3 = gsap.timeline({ delay: 0.2 });
      t3.to(this.banner.find(".img-wrap img"), {
        opacity: 1,
        duration: 1.2,
        ease: "sine.out",
      });

      // let w = 200;
      // if (this.window.width() > 1600) {
      //   w = 200;
      // } else if (this.window.width() > 1279 && this.window.width() < 1600) {
      //   w = 120;
      // } else if (this.window.width() > 767 && this.window.width() < 1279) {
      //   w = 60;
      // } else {
      //   w = 40;
      // }

      // let scaleX =
      //   (this.window.width() - w) / this.banner.find(".img-wrap").width();

      // console.log(scaleX);
      // this.banner.find(".img-wrap").css("transform", `scaleX(${scaleX})`);

      // let t4 = gsap.timeline({
      //   scrollTrigger: {
      //     trigger: this.banner.find(".img-wrap"),
      //     scrub: 1,
      //     // pin: true,
      //     // pinSpacing: true,
      //     start: "top 15%",
      //     end: "top 0%",
      //     ease: "power3.out",
      //   },
      // });

      // t4.to(this.banner.find(".img-wrap"), {
      //   scaleX: 1,
      //   borderRadius: 0,
      //   ease: "power3.out",
      // });
    }

    if (this.banner.find(".filters").length) {
      let t5 = gsap.timeline({});
      t5.to(this.banner.find(".filters"), {
        opacity: 1,
        duration: 0.6,
        ease: "sine.out",
      });
    }

    if (this.banner.find(" .search-box").length) {
      let t6 = gsap.timeline({});
      t6.to(this.banner.find(" .search-box"), {
        opacity: 1,
        duration: 0.6,
        ease: "sine.out",
      });
    }

  
    if (this.banner.find(".batch").length) {
      let t7 = gsap.timeline({});
      t7.to(this.banner.find(".batch"), {
        opacity: 1,
        duration: 0.4,
        ease: "sine.out",
      });
    }

    if (this.banner.find(".open--filters").length) {
      let t8 = gsap.timeline({});
      t8.to(this.banner.find(".open--filters"), {
        opacity: 1,
        duration: 0.6,
        ease: "sine.out",
      });
    }
  };

  truncateBreadCrumbs = () => {
    let trim_length = 12;
    
    //trim string
    let breadcrumb = this.banner.find('.breadcrumb-wrap');
    let render = $(breadcrumb).find("li.active");
    let string = $(render).text();
    if (string.length > trim_length) {
      let trimmedString = string.substring(0, trim_length) + "..";
      console.log(trimmedString);
      $(render).text(trimmedString);
    }
  };
}
