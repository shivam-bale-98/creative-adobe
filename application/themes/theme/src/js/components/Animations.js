import { splitText, fadeAnim, bottomFadeAnim } from "../utils";
import { max767, min1279, min991 } from "../utils";

import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);
export default class Animations {
  constructor() {
    this.init();
  }

  init = () => {
    this.setDomMap();
    this.bindEvents();
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.titleReveal = $(".title-reveal h2, .title-reveal h3");
    this.fadeText = $(".fade-text");
    this.bottomFadeEle = $(".fadeBottom");
    this.scaleEle = $(".scale-ele");
    this.staggerCards = $(".stagger-cards");
    this.innerParallaxElement = $(".parallax-inner");
    this.parallax = $(".parallax");
  };

  bindEvents = () => {
    if (this.fadeText.length) {
      this.fadeText.each((i, el) => {
        const delay = $(el).attr("data-delay") ? $(el).attr("data-delay") : 0;
        const duration = $(el).attr("data-duration")
          ? $(el).attr("data-duration")
          : 0;
        const start = $(el).attr("data-start")
          ? $(el).attr("data-start")
          : "80%";
        const markers = $(el).attr("data-markers")
          ? $(el).attr("data-markers")
          : false;
        const triggerDisable = $(el).attr("data-trigger-disable")
          ? $(el).attr("data-trigger-disable")
          : false;

        let trigger = {
          trigger: $(el),
          start: `top ${start}`,
          markers: markers,
        };

        if (triggerDisable) {
          trigger = null;
        }

        fadeAnim(
          el,
          {},
          { delay: delay, duration: duration, y: 0, x: 0, ease: "sine.out" },
          trigger
        );
      });
    }

    if (this.titleReveal.length) {
      this.titleReveal.each((i, el) => {
        const parent = $(el).parent();
        let delay, stagger, duration, start, markers, triggerDisable;
        if ($(el).hasClass("text-bottom-top-reveal")) {
          delay = $(el).attr("data-delay") ? $(el).attr("data-delay") : 0;
          duration = $(el).attr("data-duration")
            ? $(el).attr("data-duration")
            : 0;
          stagger = $(el).attr("data-stagger")
            ? $(el).attr("data-stagger")
            : 0.05;
          start = $(el).attr("data-start") ? $(el).attr("data-start") : "90%";
          markers = $(el).attr("data-markers")
            ? $(el).attr("data-markers")
            : false;
          triggerDisable = parent.attr("data-trigger-disable")
            ? parent.attr("data-trigger-disable")
            : false;

          if (this.window.width() <= 767) {
            delay = $(el).attr("data-mobileDelay")
              ? $(el).attr("data-mobileDelay")
              : 0;
            duration = $(el).attr("data-mobileDuration")
              ? $(el).attr("data-mobileDuration")
              : 0;
          }
        } else {
          delay = parent.attr("data-delay") ? parent.attr("data-delay") : 0;
          duration = parent.attr("data-duration")
            ? parent.attr("data-duration")
            : 0;
          stagger = parent.attr("data-stagger")
            ? parent.attr("data-stagger")
            : 0.05;
          start = parent.attr("data-start") ? parent.attr("data-start") : "90%";
          markers = parent.attr("data-markers")
            ? parent.attr("data-markers")
            : false;
          triggerDisable = parent.attr("data-trigger-disable")
            ? parent.attr("data-trigger-disable")
            : false;

          if (this.window.width() <= 767) {
            delay = parent.attr("data-mobileDelay")
              ? $(el).attr("data-mobileDelay")
              : 0;
            duration = parent.attr("data-mobileDuration")
              ? parent.attr("data-mobileDuration")
              : 0;
          }
        }

        let trigger = {
          trigger: parent,
          start: `top ${start}`,
          markers: markers,
        };

        if (triggerDisable) {
          trigger = null;
        }

        const heading = splitText($(el), "lines,words,chars");
        $(el).css({ opacity: 1 });

        fadeAnim(
          heading.chars,
          {},
          {
            delay: delay,
            duration: duration,
            stagger: stagger,
            y: 0,
            x: 0,
            ease: "power4.out",
          },
          trigger
        );
      });
    }

    if (this.bottomFadeEle.length) {
      this.bottomFadeEle.each((i, el) => {
        const parent = $(el).parent();
        const delay = $(el).attr("data-delay") ? $(el).attr("data-delay") : 0;
        const duration = $(el).attr("data-duration")
          ? $(el).attr("data-duration")
          : 0;
        const start = $(el).attr("data-start")
          ? $(el).attr("data-start")
          : "80%";
        const scale = $(el).attr("data-y") ? $(el).attr("data-y") : 20;
        const markers = $(el).attr("data-markers")
          ? $(el).attr("data-markers")
          : false;
        const triggerDisable = $(el).attr("data-trigger-disable")
          ? $(el).attr("data-trigger-disable")
          : false;

        const trigger = {
          trigger: $(el),
          start: `top ${start}`,
          markers: markers,
        };

        if (triggerDisable) {
          trigger = null;
        }

        let tl = {
          delay: delay,
          duration: duration - 0.2,
        };

        bottomFadeAnim(el, tl, scale, trigger);
      });
    }

    if (this.innerParallaxElement.length) {
      const parallax = document.querySelectorAll(".parallax-inner");
      console.log(parallax);
      if (parallax.length && min1279.matches) {
        parallax.forEach((container) => {
          let distance = container.getAttribute("data-parallax");
          let parallaxTimeline = gsap.timeline({
            scrollTrigger: {
              trigger: container,
              start: "top bottom",
              scrub: true,
              end: "bottom top",
            },
          });
          parallaxTimeline.fromTo(
            container,
            {
              yPercent: `-${distance}`,
            },
            {
              yPercent: `${distance}`,
              ease: "none",
              onComplete: () => {
                ScrollTrigger.refresh();
              },
            }
          );
        });
      }
    }

    if (this.parallax.length) {
      const parallax = document.querySelectorAll(".parallax:not(.animated)");
      if (parallax.length && min1279.matches) {
        parallax.forEach((container) => {
          let distance = container.getAttribute("data-parallax");
          let parallaxTimeline = gsap.timeline({
            scrollTrigger: {
              trigger: container,
              start: "top bottom",
              scrub: true,
              end: "bottom top",
            },
          });
          parallaxTimeline.to(container, {
            y: `${distance}%`,
            ease: "none",
            onComplete: () => {
              ScrollTrigger.refresh();
            },
          });

          container.classList.add('animated');
        });
      }
    }
  };
}
