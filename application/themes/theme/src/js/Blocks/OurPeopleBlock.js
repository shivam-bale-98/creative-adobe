import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import Swiper from "swiper";
import { Navigation } from "swiper/modules";

gsap.registerPlugin(ScrollTrigger);

export default class OurPeopleBlock {
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
    this.page = 1;
    this.mainContainer = $(".team-listing");
    this.token = this.mainContainer.find('[name="ccm_token"]');
    this.itemList = this.mainContainer.find(".team--listing");
    this.errorMessage = this.mainContainer.find(".error-message");
    this.loadMoreDiv = this.mainContainer.find(".load--more--btn");
    this.ourTeam = document.querySelectorAll(".team-list");
    this.nextArrow = document.querySelectorAll(
      ".team-list .swiper-button-next"
    );
    this.prevArrow = document.querySelectorAll(
      ".team-list .swiper-button-prev"
    );
  };

  bindEvents = () => {
    this.loadMoreDiv.on("click", this.nextPage);

    if (this.window.width() <= 991) {
      this.slider();
    }

    setTimeout(()=> {
        this.animation();
    }, 200)
  };

  nextPage = () => {
    clearTimeout(this.timer);
    this.timer = setTimeout(this.loadMore, 1000);
  };

  loadMore = () => {
    this.addPage();
    $.ajax({
      dataType: "html",
      type: "GET",
      data: this.formFilters(),
      url: this.getCurrentPagePath(),
      success: this.appendItems,
    });
  };

  resetPage = () => {
    this.page = 1;
  };

  filterSearch = () => {
    this.resetPage();
    $.ajax({
      dataType: "html",
      type: "GET",
      data: this.formFilters(),
      url: this.getCurrentPagePath(),
      success: this.applyFilters,
    });
  };

  applyFilters = (response) => {
    response = JSON.parse(response);
    if (response.success) {
      this.itemList.empty();
      this.itemList.html(response.data);
    }
    this.checkErrorMessage(response.data);
    this.addPushState();
  };

  checkErrorMessage = (value) => {
    value === "" ? this.errorMessage.show() : this.errorMessage.hide();
  };

  addPushState = () => {
    if (window.history.pushState) {
      window.history.pushState("", "", "?");
    }
  };

  getCurrentPagePath = () => {
    return window.location.pathname + "/our_people_block";
  };

  addPage = () => {
    this.page += 1;
  };

  formFilters = () => {
    return {
      page: this.page,
      ccm_token: this.getToken(),
    };
  };

  getToken = () => {
    return this.token ? this.token.val() : "";
  };

  checkLoadMore = (value) => {
    value ? this.loadMoreDiv.show() : this.loadMoreDiv.hide();
  };

  appendItems = (response) => {
    response = JSON.parse(response);
    if (response.success) {
      if (response.data) {
        this.loadMoreDiv.before(response.data);
      }
      this.checkLoadMore(response.loadMoreValue);
    }
    setTimeout(() => {
      this.lazyLoadNewCards();
      this.animation();
    }, 200);
  };

  lazyLoadNewCards = () => {
    let cards = this.mainContainer.find(".swiper-slide");
    $(cards).each((index, el) => {
      let imgWrap = $(el).find(".img-wrap:not(.loaded)");

      $(imgWrap).addClass("loaded");
    });
  };

  slider = () => {
    if (this.ourTeam) {
      const team_slider = new Swiper(this.ourTeam[0], {
        modules: [Navigation],
        slidesPerView: 1.3,
        spaceBetween: 12,
        speed: 1000,
        navigation: {
          nextEl: this.nextArrow[0],
          prevEl: this.prevArrow[0],
        },
        breakpoints: {
          767: {
            slidesPerView: 1.5,
            spaceBetween: 20,
          },
        },
      });
    }
  };

  animation = () => {

    if(this.window.width() > 991) {
      let cards = this.mainContainer.find(".swiper-slide:not(.loaded)");

      $(cards).each((ind, el) => {
        let t1 = gsap.timeline({});
  
        t1.to($(el), {
          y: "0px",
          opacity: 1,
          duration: 1,
          ease: "sine.out",
          delay: 0.05 * ind,
        });
  
        ScrollTrigger.create({
          trigger: $(el),
          start: "top 90%",
          animation: t1,
        });
  
        $(el).addClass('loaded');
      });
    } else {
      let card1 = this.mainContainer.find(".swiper-slide:nth-child(1)");
      let card2 = this.mainContainer.find(".swiper-slide:nth-child(2)");

      let cards = [card1, card2];


      $(cards).each((ind, el)=> {
        let t1 =  gsap.timeline({});

        t1.to($(el), {
          x: "0px",
          opacity: 1,
          duration: 1,
          ease: "sine.out",
          delay: ind * 0.07
        });


        ScrollTrigger.create({
          trigger: $(el),
          start: "top 90%",
          animation: t1
        })
      });
    }
   
  };
}
