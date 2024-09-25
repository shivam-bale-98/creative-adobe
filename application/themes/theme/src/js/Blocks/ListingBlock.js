import Lenis from "@studio-freight/lenis";
import { debounce } from "../utils";
import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class ListingBlock {
  constructor() {
    this.page = 1;
    this.autoLoad = false;
    this.window = $(window);
    this.body = $("body");
    this.mainContainer = $("");
    this.block = $(".listing--block");
    this.bindData = this.block.find(".bind--data");
    this.keywords = this.block.find("#keywords");
    this.ccmToken = this.block.find('[name="ccm_token"]');
    this.sortFilter = this.block.find(".sort--filter");
    this.dynamicFilters = this.block.find(".block--filter");
    this.loader = this.block.find(".loader");
    this.loadMore = this.block.find(".load--more");
    this.itemsCounter = this.block.find("#itemsCounter").val();
    this.applicationFilters = document.querySelectorAll(
      ".application-listing--page .filter-swiper"
    );
    this.bindEvents();
    this;
  }

  bindEvents = () => {
    this.keywords.keyup(this.delayLoad);
    this.dynamicFilters.on("change", this.filterSearch);
    this.sortFilter.on("change", this.filterSearch);
    this.initiatePagination();
    let timeOut = 2000;
    if ($(this.body).hasClass("visited")) {
      timeOut = 900;
    }
    if (this.block.hasClass("products-listing--page")) {
      this.handleProductsCardHover();

      setTimeout(() => {
        this.handleProductCardAnimations();
      }, timeOut);
    }
    //News Page
    if (this.block.hasClass("news-listing--page")) {
      setTimeout(() => {
        this.handleNewsCardAnimation();
      }, timeOut);
    }
    // Vacancy
    if (this.block.hasClass("vacancy-listing--page")) {
      setTimeout(() => {
        this.handleVacancyCardAnimation();
      }, timeOut);
    }
    // Certificate Cards
    if (this.block.hasClass("certificates-listing-block")) {
      setTimeout(() => {
        this.handleCertificateCardAnimation();
      }, timeOut);
      this.popUpCard();
    }
    
    //applications
    if (this.block.hasClass("application-listing--page")) {
      setTimeout(() => {
        this.handleApplicationCardAnimation();
        this.handleSmoothScrollToApplication();
        if (this.window.width() < 1279) {
          this.filtersSlider();
        }
      }, timeOut);

      this.window.on("scroll", () => {
        this.hadnleStickyFilters();
      });
    }

    setTimeout(() => {
      this.handleElementsAnimation();
      this.handleFiltersAnimation();
    }, timeOut);

    
  };

  delayLoad = () => {
    this.resetPage();
    clearInterval(this.timer);
    this.timer = setTimeout(() => {
      this.filterSearch();
    }, 1000);
  };

  initiatePagination = () => {
    if (this.loadMore.length) {
      let paginationStyle = this.loadMore.attr("data-pagination-style");

      if (paginationStyle === "on_scroll") {
        this.autoLoad = this.loadMore.attr("data-load-more");
        this.toggleLoadMore = (flag) => {
          this.autoLoad = !!flag;
        };
        $(window).scroll(this.windowScroll);
      } else if (paginationStyle === "on_click") {
        this.toggleLoadMore = (flag) => {
          flag ? this.loadMore.show() : this.loadMore.hide();
        };
        this.loadMore.on("click", this.nextPage);
      }
    }
  };

  windowScroll = () => {
    if (this.autoLoad && this.isAtBottom()) {
      this.autoLoad = false;
      this.nextPage();
    }
  };

  isAtBottom = () => {
    var scroll = this.block.scrollTop() + this.block.innerHeight() + 100;
    var scrollHeight = this.block[0].scrollHeight;
    return scroll >= scrollHeight;
  };

  nextPage = () => {
    this.addPage();
    this.showLoader();
    this.requestData();
  };

  addPage = () => {
    this.page++;
  };

  resetPage = () => {
    this.page = 1;
    this.itemsCounter = 0;
  };

  getPath = () => {
    return window.location.pathname + "/listing_block";
  };

  formParams = () => {
    let data = {};

    data["keywords"] = this.keywords.val();
    data["page"] = this.page;
    data["ccm_token"] = this.ccmToken.val();
    data["itemsCounter"] = encodeURIComponent(this.itemsCounter);
    if (this.sortFilter.length)
      data[this.sortFilter.attr("name")] = this.sortFilter.val();

    this.dynamicFilters.each((index, item) => {
      item = $(item);
      let key = item.attr("name");
      let values = item.val();
      data[key] = Array.isArray(values)
        ? values.map((value) => {
            return encodeURIComponent(value);
          })
        : [encodeURIComponent(values)];
    });

    return data;
  };

  clearContainer = () => {
    this.bindData.empty();
  };

  showLoader = () => {
    this.loadMore.hide();
    this.loader.show();
  };

  hideLoader = () => {
    this.loader.hide();
  };

  filterSearch = () => {
    this.loadMore.hide();
    this.resetPage();
    this.clearContainer();
    this.showLoader();
    this.setHistory();
    this.requestData();
  };

  requestData = () => {
    $.ajax({
      dataType: "json",
      type: "GET",
      cache: false,
      data: this.formParams(),
      url: this.getPath(),
      success: this.appendItems,
      error: this.handleError,
    });
  };

  appendItems = (response) => {
    this.bindData.append(response.data);
    this.hideLoader();
    this.toggleLoadMore(response.loadMore);
    this.itemsCounter = response.itemsCounter;

    if (this.block.hasClass("products-listing--page")) {
      this.handleProductsCardHover();

      this.handleProductCardAnimations();
    }

    // News Card
    if (this.block.hasClass("news-listing--page")) {
      setTimeout(() => {
        this.handleNewsCardAnimation();
      }, 1000);
    }

    // Vacancy Card
    if (this.block.hasClass("vacancy-listing--page")) {
      setTimeout(() => {
        this.handleVacancyCardAnimation();
      }, 1000);
    }

    // Certificate Cards
    if (this.block.hasClass("certificates-listing-block")) {
      $(".certificate-main-container")
        .find(".certificate-popup-slider")
        .find(".certificate--popup")
        .append(response.popupElement);

      setTimeout(() => {
        this.handleCertificateCardAnimation();
      }, 1000);
      this.popUpCard();
      // $(".certificate-popup-slider").trigger("updateSwiper");
    }
  };

  setHistory = () => {
    if (window.history.pushState) {
      let url = "?";
      let params = "";

      let data = this.formParams();
      for (let key in data) {
        if (key === "page" || key === "ccm_token") continue;

        if (params.length) params += "&";
        params += `${key}=${data[key]}`;
      }

      url += params;

      window.history.pushState("", "", url);
    }
  };

  handleError = (e) => {
    let response = JSON.parse(e.responseText);
    PNotify.error({
      title: response.title,
      text: response.message,
    });
  };

  handleProductsCardHover = () => {
    if (this.window.width() > 1279) {
      let productCard = this.block.find(".product-card:not(.hovered)");

      $(productCard).each((ind, el) => {
        if ($(el).find(".content p").length) {
          $(el).find(".content p").slideUp();
        }

        $(el).addClass("hovered");
      });
      $(productCard).on(
        "mouseenter",
        debounce(function (e) {
          let $this = $(this);

          if ($this.find(".content p").length) {
            $this.find(".content p").slideDown(500);
          }
        }, 200)
      );

      $(productCard).on("mouseleave", function (e) {
        let $this = $(this);

        if ($this.find(".content p").length) {
          $this.find(".content p").slideUp(500);
        }
      });
    }
  };

  handleProductCardAnimations = () => {
    let cards = this.block.find(".product-card:not(.animated)");

    $(cards).each((index, el) => {
      let t1 = gsap.timeline({});
      t1.to($(el), 1, {
        opacity: 1,
        y: "0px",
        delay: 0.07 * index,
        ease: "circ.out",
      });

      ScrollTrigger.create({
        trigger: $(el),
        start: "top 80%",
        animation: t1,
      });

      $(el).addClass("animated");
    });
  };

  handleElementsAnimation = () => {
    // add elements like input, filters etc here
    let input = this.block.find(".search-wrapper .search-box");
    let t1 = gsap.timeline({});
    t1.to($(input), {
      opacity: 1,
      duration: 0.6,
      ease: "sine.out",
    });
  };
  // Filters
  handleFiltersAnimation = () => {
    // add elements like input, filters etc here
    let input = this.block.find(".search-wrapper .filters");
    let t1 = gsap.timeline({});
    t1.to($(input), {
      opacity: 1,
      duration: 0.6,
      ease: "sine.out",
    });
  };
  // News Card
  handleNewsCardAnimation = () => {
    // let newsListCards = this.mainContainer.find(".news-listing-card");
    console.log('news animation');
    let newsListCards = this.block.find(".news-listing-card:not(.animated)");
    $(newsListCards).each((ind, el) => {
      let cards = $(el);
      let tg2 = gsap.timeline({ delay: 0.07 * ind });
      tg2.to(cards, 0.6, {
        opacity: 1,
        y: "0px",
        ease: "sine.out",
      });
      ScrollTrigger.create({
        trigger: cards,
        start: "top 90%",
        animation: tg2,
      });

      cards.addClass("animated");
    });
  };
  // Vacancy Card
  handleVacancyCardAnimation = () => {
    this.block.find(".vacancy-list-card").each((index, card) => {
      gsap
        .timeline({
          delay: 0.6,

          scrollTrigger: {
            trigger: card,
            start: "top 90%",
          },
        })
        .to(card, { opacity: 1, duration: 1.2, ease: "sine.out" });
    });
  };
  // Certificate Card
  handleCertificateCardAnimation = () => {
    let cards = this.block.find(".card-item:not(.animated)");

    $(cards).each((index, el) => {
      let t1 = gsap.timeline({});
      t1.to($(el), 1, {
        opacity: 1,
        y: "0px",
        delay: 0.07 * index,
        ease: "circ.out",
      });

      ScrollTrigger.create({
        trigger: $(el),
        start: "top 80%",
        animation: t1,
      });

      $(el).addClass("animated");
    });
  };
  //for applications
  handleApplicationCardAnimation = () => {
    let applicationCrd = this.block.find(".application-crd");
    if (this.window.width() > 991) {
      $(applicationCrd).each((ind, el) => {
        let count = $(el).find(".left h6");
        let img = $(el).find(".left .img-wrap");
        let right = $(el).find(".right");
        let line = $(el).find(".line");

        let t1 = gsap.timeline({});

        t1.to($(count), {
          opacity: 1,
          duration: 0.6,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t1,
        });

        let t01 = gsap.timeline({});

        t01.to($(img), {
          opacity: 1,
          y: "0px",
          duration: 1,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 80%",
          animation: t01,
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
          duration: 2,
          ease: "power3.out",
        });
      });
    } else if(this.window.width() < 991 && this.window.width() > 767)  {
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
      $(applicationCrd).each((ind, el) => {
        
        let line = $(el).find(".line");

        let t1 = gsap.timeline({
          onUpdate: () => {
            let p = t1.progress();

            if(p >= 0.7) {
              t3.play();
            }
          }
        });

        t1.to($(el), {
          opacity: 1,
          duration: 0.8,
          ease: "sine.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 85%",
          animation: t1,
        });

        

       

        let t3 = gsap.timeline({ delay: 0.3, paused: true });

        t3.to($(line), {
          scaleX: 1,
          duration: 1,
          ease: "sine.out",
        });
      });
    }
  };

  handleSmoothScrollToApplication = () => {
    let filters = this.block.find(".filters a");

    $(filters).each((i, el) => {
      $(el).on("click", function (e) {
        e.preventDefault();

        var target = e.currentTarget;

        // var $target = $(target);
        let app = $(target).attr("href");
        let distance = Math.abs($(app).offset().top - $(window).scrollTop()); // Calculate distance
        let duration = Math.max(400, Math.min(1600, distance * 2)); // Adjust duration proportionally

        $("html, body")
          .stop()
          .animate(
            {
              scrollTop: $(app).offset().top - 100,
            },
            duration,
            "swing",
            function () {
              // window.location.hash = target;
            }
          );
      });
    });
  };

  filtersSlider = () => {
	this.applicationFilters.forEach((filter, i) => {
		new Swiper(filter, {
			slidesPerView: "auto",
			spaceBetween: 10,
			speed: 1000,
		  });
	})
    
  };

  popUpCard = () => {
    const sliders = document.querySelectorAll(".certificate-popup-slider");
    if (sliders.length > 0) {
      sliders.forEach((slider) => {
        const swiperContainer = slider.querySelector(".swiper");
        const prevButton = slider.querySelector(".swiper-button-prev");
        const nextButton = slider.querySelector(".swiper-button-next");
        if (swiperContainer) {
          const swiper = new Swiper(swiperContainer, {
            slidesPerView: 1,
            speed: 600,
            // autoHeight: true,
            spaceBetween: 20,
            observer: true,
            modules: [Navigation],
            navigation: {
              nextEl: nextButton,
              prevEl: prevButton,
            },
          });
          $(document).on("click", ".card-item",  (e) => {
            let currentCard = e.currentTarget;
            console.log($(currentCard));
            if ($('html').hasClass("lenis")) {
              lenis.stop();
            }
            $('body').addClass("active");
            setTimeout(() => {
              $(".certificate-popup-block").addClass("active");
            }, 100);

            var slideIndex = $(currentCard).data("slideindex");
            console.log(slideIndex);
            swiper.slideTo(slideIndex - 1);
          });

          $(document).on(
            "click",
            ".certificate-popup-block .popup-close,.certificate-popup-block .t-overlay",
             () => {
              // Remove active class from all team-popup-block elements
              $(".certificate-popup-block").removeClass("active");
              
              setTimeout(() => {
                $("body").removeClass("active");
                if ($("html").hasClass("lenis")) {
                  lenis.start();
                }
              }, 500);
            }
          );
        }
      });
    }
  };

  //for applications
  hadnleStickyFilters = () => {
    let stickyFilter = this.block.find(".sticky-filters");

    let normalFilter = this.block.find(".normal-filters");

    let windowTop = this.window.scrollTop();
    let filterTop = $(normalFilter).offset().top + 50;

    //   console.log(windowTop, filterTop);
    if ($(".header").hasClass("sticky-header")) {
      console.log("sticky");
      if (!$(stickyFilter).hasClass("top")) {
        $(stickyFilter).addClass("top");
      }
    } else {
      if ($(stickyFilter).hasClass("top")) {
        $(stickyFilter).removeClass("top");
      }
    }
    if (windowTop >= filterTop) {
      if (!$(stickyFilter).hasClass("active")) {
        $(stickyFilter).addClass("active");
      }
    } else {
      if ($(stickyFilter).hasClass("active")) {
        $(stickyFilter).removeClass("active");
      }
    }


    let applicationCrd = this.block.find(".application-crd");
    let a = $(stickyFilter).find('a');
    
    $(applicationCrd).each((i, el)=> {
      if($(el).position().top <= this.window.scrollTop() + 200) {
        $(a).removeClass('active');

        if(!$(a).eq(i).hasClass('active')) {
          $(a).eq(i).addClass('active');
        }
        
      }
    })
  };
}
