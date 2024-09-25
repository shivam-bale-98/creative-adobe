import { debounce } from "../utils";
import gsap from "gsap";

export default class Header {
  constructor({ header, htmlBody }) {
    this.header = header;
    this.html = $("html");
    this.htmlBody = htmlBody;
    this.mobileMenu = this.header.find(".mobile-menu");
    this.mobileNav = this.htmlBody.find(".mobile-nav");
    this.megaMenuLinks = this.header.find(".has-mega-menu");
    this.searchBtn = this.header.find("#search");
    this.searchPopUp = this.header.find(".search-popUp");
    this.megaMenu = this.header.find(".mega-menu");
    this.blurOverlay = $(".blur-overlay");
    this.isHovered = false;
    this.hoverEffects();
    if ($(window).width() > 1279 && this.megaMenuLinks.length) {
      this.handleMegaMenu();
    }
    this.bindEvents();
  }

  bindEvents = () => {
    const $container = this.htmlBody;
    $container.on("click", ".mobile-menu", this.handleMobileMenu);

    this.header.find("nav .links li.nav-selected").addClass("active");

    let subMenu = this.mobileNav.find(".nav-items .has-mega-menu >  a");

    $(subMenu).on("click", (e) => {
      e.preventDefault();
      let currentItemLink = e.currentTarget;

      // this.mobileNav.find('.mega-menu').fadeOut(50);
      let t5 = gsap.timeline({
        onComplete: () => {
          $(currentItemLink).next(".mega-menu").css("display", "block");
          $(currentItemLink).next(".mega-menu").addClass("active");
          t6.play();
        },
      });
      t5.to(this.mobileNav.find(".nav-items .link"), {
        opacity: 0,
        duration: 0.6,
        ease: "sine.out",
      });

      let t6 = gsap.timeline({ paused: true });

      t6.to($(currentItemLink).next(".mega-menu"), {
        y: "0px",
        opacity: 1,
        ease: "power3.out",
        duration: 0.8,
      });
    });

    let subMenuClose = this.mobileNav.find(".mega-menu .close");

    $(subMenuClose).on("click", (e) => {
      let currentCloseItem = e.currentTarget;

      let currentCloseMenu = $(currentCloseItem).closest(".mega-menu");

      let t7 = gsap.timeline({
        onComplete: () => {
          $(currentCloseMenu).fadeOut(200);
          $(currentCloseMenu).removeClass("active");
          t8.play();
        },
      });

      t7.to($(currentCloseMenu), {
        y: "60px",
        opacity: 0,
        ease: "power3.out",
        duration: 0.8,
      });

      let t8 = gsap.timeline({ paused: true });

      t8.to(this.mobileNav.find(".nav-items .link"), {
        opacity: 1,
        duration: 0.8,
        ease: "sine.out",
      });
    });

    this.searchBtn.on("click", (e) => {
      e.preventDefault();
      if (!this.searchPopUp.hasClass("active")) {
        this.searchPopUp.addClass("active");
        this.blurOverlay.fadeIn(200);
        this.htmlBody.addClass("active");
        if (this.html.hasClass("lenis")) {
          lenis.stop();
        }
      }
    });

    let searchCloseBtn = this.searchPopUp.find(".close-search");

    $(searchCloseBtn).on("click", () => {
      if (this.searchPopUp.hasClass("active")) {
        this.searchPopUp.removeClass("active");
        this.blurOverlay.fadeOut(200);
        this.htmlBody.removeClass("active");
        if (this.html.hasClass("lenis")) {
          lenis.start();
        }
      }
    });

    this.blurOverlay.on("click", () => {
      if (this.searchPopUp.hasClass("active")) {
        this.searchPopUp.removeClass("active");
        this.blurOverlay.fadeOut(200);
        this.htmlBody.removeClass("active");
        if (this.html.hasClass("lenis")) {
          lenis.start();
        }
      }
    });
  };

  handleMobileMenu = () => {
    if (this.mobileMenu.hasClass("active")) {
      let t3 = gsap.timeline({
        onComplete: () => {
          t4.play();
          if (this.mobileNav.find(".mega-menu").hasClass("active")) {
            this.mobileNav.find(".mega-menu.active").fadeOut(200);
            this.mobileNav.find(".mega-menu.active").removeClass("active");
          }
        },
      });

      let t4 = gsap.timeline({
        paused: true,
        delay: 0,
        onComplete: () => {
          this.mobileMenu.removeClass("active");
          this.mobileNav.removeClass("active");
        },
        onUpdate: () => {
          if (t4.progress() >= 0) {
            this.header.removeClass("active");
          }
          if (t4.progress() >= 0.8) {
            t5.play();
          }
        },
      });

      let t5 = gsap.timeline({ paused: true, delay: 0.1 });
      let t40 = gsap.timeline({
        onUpdate: () => {
          let prog = t40.progress();

          if (prog >= 0) {
            this.htmlBody.removeClass("active");
          }
        },
      });

      if (!this.mobileNav.find(".mega-menu").hasClass("active")) {
        // t3.to(this.mobileNav.find('.nav-items  h4 .link'), {
        //   left: "-40px",
        //   opacity:0,
        //   stagger: 0.08,
        //   duration: 1,
        //   ease: 'sine.out'
        // });

        t3.to(this.mobileNav.find(".nav-items .link"), {
          y: "40px",
          opacity: 0,
          duration: 0.5,
          ease: "power2.out",
        });
      } else {
        t3.to(this.mobileNav.find(".mega-menu.active"), {
          y: "60px",
          opacity: 0,
        });
      }

      t40.to(this.mobileNav.find(".shape"), {
        opacity: 0,
        duration: 0.4,
        ease: "sine.out",
      });

      t4.to(this.mobileNav.find(".anim"), {
        opacity: 0,
        duration: 0.6,
        clearProps: "opacity",
      });

      t5.to(this.header.find(".mobile-menu span"), {
        opacity: 1,
        duration: 0.4,
        ease: "sine.out",
      });
    } else {
      this.mobileMenu.addClass("active");

      this.mobileNav.addClass("active");
      this.header.addClass("active");

      setTimeout(() => {
        this.htmlBody.addClass("active");
      }, 600);

      let t1 = gsap.timeline({ delay: 0.4 });
      let t2 = gsap.timeline({ delay: 0.5 });
      let t3 = gsap.timeline();

      // t1.to(this.mobileNav.find('.nav-items .link'), {
      //   left:"0px",
      //   opacity:1,
      //   duration: 1,
      //   stagger: 0.12,
      //   ease: "power3.out"
      // });

      t1.to(this.mobileNav.find(".nav-items .link"), {
        // left:"0px",
        y: "0px",
        opacity: 1,
        duration: 0.8,
        ease: "power2.out",
      });

      t2.to(this.mobileNav.find(".shape"), {
        opacity: 1,
        duration: 0.5,
        ease: "sine.out",
      });

      t3.to(this.header.find(".mobile-menu span"), {
        opacity: 0,
        duration: 0.2,
        ease: "sine.out",
      });
    }
  };

  handleMegaMenu = () => {
    let menuOpen = this.header.find("nav .links li.has-mega-menu > a");
    let otherLinks = this.header.find("nav .links li:not(.has-mega-menu) > a");

    // let isHovered = false;
    let timeoutId;

    const handleMouseEnter = debounce((e) => {
      if (this.header.hasClass("top")) {
        this.header.addClass("sticky-active");
        // this.header.removeClass('top');
      }
      this.megaMenu.removeClass("active");
      this.htmlBody.addClass("active");

      if (this.html.hasClass("lenis")) {
        lenis.stop();
      }
      setTimeout(() => {
        console.log(this.isHovered);
        let currentMenuItem = e.currentTarget;
        let currentItemMegaMenu = $(currentMenuItem).next(".mega-menu");
        this.header.find("nav .links li").removeClass("active");
        $(currentMenuItem).parent().addClass("active");
        $(currentItemMegaMenu).addClass("active");
        this.blurOverlay.fadeIn(200);
      }, 20);
    }, 400);

    $(menuOpen).on("mouseenter", (e) => {
      this.isHovered = true;
      clearTimeout(timeoutId);

      timeoutId = setTimeout(() => {
        handleMouseEnter(e);
      }, 400);
    });

    $(menuOpen).on("mouseleave", () => {
      // Clear the timeout when the mouse leaves
      // this.htmlBody.removeClass('active');
      clearTimeout(timeoutId);
      // this.isHovered = false;
    });

    this.megaMenu.on(
      "mouseleave",
      debounce(() => {
        // this.isHovered = false;
        clearTimeout(timeoutId);
        this.htmlBody.removeClass("active");
        if (this.megaMenu.hasClass("active")) {
          if (this.header.hasClass("sticky-active")) {
            setTimeout(() => {
              this.header.removeClass("sticky-active");
            }, 100);
          }
          this.megaMenu.removeClass("active");
          this.header.find("nav .links li").removeClass("active");
          this.header.find("nav .links li.nav-selected").addClass("active");
          this.blurOverlay.fadeOut(200);
          if (this.html.hasClass("lenis")) {
            lenis.start();
          }
        }
      }, 400)
    );

    $(otherLinks).on(
      "mouseenter",
      debounce(() => {
        // this.isHovered = false;
        this.htmlBody.removeClass("active");
        if (this.megaMenu.hasClass("active")) {
          if (this.header.hasClass("sticky-active")) {
            setTimeout(() => {
              this.header.removeClass("sticky-active");
            }, 300);
          }
          this.megaMenu.removeClass("active");
          this.header.find("nav .links li").removeClass("active");

          this.blurOverlay.fadeOut(200);
          if (this.html.hasClass("lenis")) {
            lenis.start();
          }

          setTimeout(() => {
            this.header.find("nav .links li.nav-selected").addClass("active");
          }, 500);
        }
      }, 500)
    );
  };

  hoverEffects = () => {
    // let isHovered = false;
    let timeoutId;
    let links = this.header.find(".links li:not(.has-mega-menu) > a");
    let selectedPath = this.header.find(".links li.nav-path-selected");

    const handleLinkEnter = debounce((e) => {
      console.log(this.isHovered, "is-hovered state");
      let $this = $(e.currentTarget);
      $(links).parent().removeClass("active");
      // $(selectedPath).removeClass("nav-path-selected");
      $this.parent().addClass("active");
    }, 100);

    $(links).on("mouseenter", (event) => {
      clearTimeout(timeoutId);

      timeoutId = setTimeout(() => {
        // isHovered = true;
        // if(!this.isHovered) {
        handleLinkEnter(event);
        // }
      }, 100);
    });

    $(links).on("mouseleave", (event) => {
      let $this = $(event.currentTarget);
      $this.parent().removeClass("active");
      $(selectedPath).addClass("active");
      clearTimeout(timeoutId);
      // isHovered = false;
    });
  };
}
