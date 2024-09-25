import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class Applications {
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
    setTimeout(() => {
      this.animations();
    }, timeout);
  };

  setDomMap = () => {
    this.window = $(window);
    this.body = $("body");
    this.mainContainer = $(".application-list");
    this.hoverTimer = null;
  };

  bindEvents = () => {
    if (this.window.width() >= 1279) {
      let t1 = gsap.timeline();
      const mediaPositioning = (current) => {
        const media = current
          .parent(".applications-content-wrapper")
          .next(".applications-media-wrapper");
        const applicationTop = current.position().top;
        const applicationHeight = current.height();
        let offset =
          applicationTop + applicationHeight / 2 - media.height() / 2.5;

        setTimeout(() => {
          t1.to(media, {
            duration: 0.8,
            y: offset,
            ease: "sine.out",
          });
        }, 50);
      };

      this.mainContainer.each((i, el) => {
        gsap.to(el, {
          scrollTrigger: {
            trigger: el,
            start: "top 60%",
            once: true,
            onEnter: () => {
              // const titles = $(el).find(".application-title");
              const videos = $(el).find("video");

              // gsap.to(titles, 1, { opacity: 1, yPercent: 0, stagger: 0.2 });

              videos.each((ind, vi) => {
                const src = vi.getAttribute("data-src");
                // console.log(src);
                vi.setAttribute("src", src);
              });
            },
          },
        });

        //   console.log($(".applications-content-wrapper .application").first());
        //   mediaPositioning($(el).find(".applications-content-wrapper .application").first());
        mediaPositioning(
          $(el).find(".applications-content-wrapper .application").eq(0)
        );
      });

      const playVideo = (v) => {
        v[0].currentTime = 2;
        v[0].play();
      };

      const applicationTitle = this.mainContainer.find(
        ".application .application-title"
      );
      const p = this.mainContainer.find(".application:not(:nth-child(1)) p");
      p.slideUp();
      let toogleMedia = null;
      let video = null;
      let vidInt = null;
      applicationTitle.hover((e) => {
        clearTimeout(this.hoverTimer);

        this.hoverTimer = setTimeout(() => {
          handleApplicationToggle(e);
        }, 100);
      });

      const handleApplicationToggle = (e) => {
        const current = e.currentTarget;
        const currentEl = $(current);

        const check = currentEl.closest(".application").hasClass("active");
        const application = this.mainContainer.find(".application");
        const activeApplication = currentEl.closest(".application");

        toogleMedia = activeApplication.attr("data-id");

        video = $(`#media-${toogleMedia}`).find("video");
        // console.log(video);
        if (!check) {
          application.removeClass("active");
          application.find(".content p").slideUp(300);
          activeApplication.addClass("active");
          activeApplication.find(".content p").slideDown(300);
          this.mainContainer
            .find(".applications-media-wrapper > div")
            .fadeOut();
          console.log(toogleMedia);
          $(`#media-${toogleMedia}`).fadeIn();

          mediaPositioning(activeApplication);
        }

        if (video.length > 0) {
          clearInterval(vidInt);
          video[0].pause();

          playVideo(video);

          const duration = video[0].duration * 1000;

          vidInt = setInterval(() => {
            playVideo(video);
          }, duration);
        }
      };
    }
  };

  animations = () => {
    if (this.window.width() >= 1279) {
      let applicationCard = this.mainContainer.find(".application");

      $(applicationCard).each((index, el) => {
        let title = $(el).find(".content");
        let line = $(el).find(".line");
        let h6 = $(el).find(".h6");

        let t1 = gsap.timeline({
          onUpdate: () => {
            // $(el).addClass('animated')
            let prog = t1.progress();
            if (prog >= 0.7) {
              t2.play();
            }
          },
        });
        let t2 = gsap.timeline({
          paused: true,
        });

        t1.to($(title), {
          x: 0,
          opacity: 1,
          duration: 1,
          ease: "sine.out",
        });

        t2.to($(line), {
          scaleX: 1,
          duration: 1,
          ease: "power4.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        // ScrollTrigger.create({
        //   trigger: $(el),
        //   start: "top 90%",
        //   animation: t2,
        // });

        let t3 = gsap.timeline({});

        t3.to($(h6), {
          opacity: 1,
          duration: 0.5,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t3,
        });
      });
    } else if (this.window.width() < 1279 && this.window.width() > 767) {
      let applicationCrd = this.mainContainer.find(
        ".application-listing-mobile .application-crd"
      );

      $(applicationCrd).each((ind, el) => {
        let left = $(el).find(".left");
        let right = $(el).find(".right");
        let line = $(el).find(".line");

        let t1 = gsap.timeline({});

        t1.to($(left), {
          x: 0,
          opacity: 1,
          duration: 0.6,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        let t2 = gsap.timeline({
          onComplete: () => {
            t3.play();
          },
        });

        t2.to($(right), {
          x: 0,
          opacity: 1,
          duration: 0.6,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t2,
        });

        let t3 = gsap.timeline({ delay: 0.3, paused: true });

        t3.to($(line), {
          scaleX: 1,
          duration: 1,
          ease: "sine.out",
        });
      });
    } else {
      let applicationCrd = this.mainContainer.find(
        ".application-listing-mobile .application-crd"
      );

      $(applicationCrd).each((ind, el) => {
        let line = $(el).find(".line");

        let t1 = gsap.timeline({
          onUpdate: () => {
            let prog = t1.progress();

            if (prog >= 0.6) {
              t3.play();
            }
          },
        });

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

        let t3 = gsap.timeline({ paused: true });

        t3.to($(line), {
          scaleX: 1,
          duration: 1,
          ease: "power3.out",
        });
      });
    }
  };
}
