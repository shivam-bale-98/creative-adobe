import { debounce } from "../utils";
import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class Products {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.bindEvents();
    let timeout = 2000;
    if ($(this.body).hasClass("visited")) {
      timeout = 900;
    }
    if ($(".js-main-products").length) {
      setTimeout(() => {
        this.hpProductsAnimations();
      }, 2000);
    }

    if (this.relatedProducts.length) {
      setTimeout(() => {
        this.relatedProductsAnimation();
      }, timeout);
    }

    this.window.on(
      "resize",
      debounce(() => {
        this.bindEvents();
      }, 200)
    );
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.productListing = $(".products-list");
    this.productListCard = this.productListing.find(".product-card");
    this.relatedProducts = $(".js-related-products");
  };

  bindEvents = () => {
    if (this.window.width() > 1279) {
      this.productListCard.each((ind, el) => {
        if ($(el).find(".content p").length) {
          $(el).find(".content p").slideUp();
        }
      });
      this.productListCard.on(
        "mouseenter",
        debounce(function (e) {
          let $this = $(this);

          if ($this.find(".content p").length) {
            $this.find(".content p").slideDown(300);
          }
        }, 50)
      );

      this.productListCard.on("mouseleave", function (e) {
        let $this = $(this);

        if ($this.find(".content p").length) {
          $this.find(".content p").slideUp(200);
        }
      });
    }

    if ($(".js-main-products").length && this.window.width() <= 767) {
      new Swiper(".js-main-products .swiper", {
        modules: [Navigation],
        slidesPerView: 1.1,
        spaceBetween: 15,
        speed: 1000,
        navigation: {
          nextEl: ".products-list .swiper-button__next",
          prevEl: ".products-list .swiper-button__prev",
        },
      });
    }

    if (this.relatedProducts.length) {
      new Swiper(".js-related-products .swiper", {
        modules: [Navigation],
        slidesPerView: 1.1,
        spaceBetween: 15,
        speed: 1000,
        navigation: {
          nextEl: ".js-related-products .swiper-button-next",
          prevEl: ".js-related-products .swiper-button-prev",
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
            spaceBetween: 40,
          },
        },

        on: {
          init: () => {
            let swiperBtn = this.relatedProducts.find(".swiper-button");
            if ($(swiperBtn).hasClass("swiper-button-lock")) {
              this.relatedProducts.addClass("swiper-disabled");
            }
          },
        },
      });
    }
  };

  hpProductsAnimations = () => {
    if (this.window.width() > 767) {
      let cards = this.productListCard;
      let t1 = gsap.timeline({});
      let t2 = gsap.timeline({ delay: 0.2 });
      $(cards).each((index, el) => {
        let img = $(el).find("img");
        t1.to($(el), 0.8, {
          opacity: 1,
          y: "0px",
          stagger: 0.2,
          ease: "circ.out",
          onComplete: () => {
            // $(el).addClass("animated");
          },
        });

        // t2.to($(img), {
        //   scale: 1,
        //   opacity: 1,
        //   duration: 0.5,
        //   ease: "sine.out",
        // });
        ScrollTrigger.create({
          trigger: $(el),
          start: "top 90%",
          animation: t1,
        });

        // ScrollTrigger.create({
        //   trigger: $(el),
        //   start: "top 90%",
        //   animation: t2,
        // });
      });
    } else {
      let card1 = this.productListing.find(".swiper-slide:nth-child(1)");
      let card2 = this.productListing.find(".swiper-slide:nth-child(2)");
      let cards = [card1, card2];

      let t1 = gsap.timeline({});

      t1.to($(cards), {
        opacity: 1,
        x: "0px",
        stagger: 0.03,
        duration: 0.8,
        ease: "sine.out",
      });

      ScrollTrigger.create({
        trigger: $(card1),
        start: "top 90%",
        animation: t1,
      });
    }
  };

  relatedProductsAnimation = () => {
    let cards = this.relatedProducts.find(".swiper-slide").slice(0, 4);

    let t1 = gsap.timeline({});
    let dur = 1;
    let stag = 0.07;

    if (this.window.width() < 991) {
      cards = this.relatedProducts.find(".swiper-slide").slice(0, 2);
      (dur = 0.7), (stag = 0.05);
    }

    t1.to($(cards), {
      opacity: 1,
      x: "0px",
      stagger: stag,
      duration: dur,
      ease: "sine.out",
    });

    ScrollTrigger.create({
      trigger: this.relatedProducts.find(".swiper"),
      start: "top 90%",
      animation: t1,
    });
  };
}