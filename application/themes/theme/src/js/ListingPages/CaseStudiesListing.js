import { max767, min1279, min991 } from "../utils";

import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import Swiper from "swiper";
gsap.registerPlugin(ScrollTrigger);
import LazyLoading from "../components/LazyLoading";

export default class CaseStudiesListing {
  constructor() {
    this.init();
    this.bindEvents();
  }

  init = () => {
    this.page = 1;
    this.window = $(window);
    this.body = $("body");
    this.mainContainer = $(".case-studies-main-container");
    this.parallax = this.mainContainer.find(".parallax:not(.animated)");
    this.openFilterBtn = this.mainContainer.find(".open--filters");
    this.token = this.mainContainer.find('input[name="ccm_token"]');
    this.itemList = this.mainContainer.find(".case-studies--listing");
    this.errorMessage = this.mainContainer.find(".error-message");
    this.loadMoreDiv = this.mainContainer.find(".load--more--btn");
    this.clearFilterBtn = this.mainContainer.find(".clear--filter");
    this.searchWrapper = this.mainContainer.find(".search-wrapper");
    this.keywords = this.mainContainer.find("#keywords");
    this.rangeFilter = this.mainContainer.find(".range");
    this.productFilter = this.mainContainer.find(".product");
    this.applicationFilter = this.mainContainer.find(".applications");
    this.specificationFilter = this.mainContainer.find(".specification");
    this.locationFilter = this.mainContainer.find(".location");
    this.yearFilter = this.mainContainer.find(".year");
    this.loader = this.mainContainer.find(".loader");
    this.itemsCounter = this.mainContainer.find("#itemsCounter").val();
    this.selected = {
      range: "",
      product: "",
      applications: "",
      specification: "",
      location: "",
      year: ""
    };
    this.keyword = "";
    this.bannerFilters = this.mainContainer.find(".select--filters");
    this.mobileFilters = this.mainContainer.find(".filter--section-mobile");

    this.isClearFilter = false;
  };

  bindEvents = () => {
    this.loadMoreDiv.on("click", this.nextPage);
    this.keywords.keyup(this.delayFilter);

    this.filters = [
      { filter: this.rangeFilter, key: 'range' },
      { filter: this.productFilter, key: 'product' },
      { filter: this.applicationFilter, key: 'applications' },
      { filter: this.specificationFilter, key: 'specification' },
      { filter: this.locationFilter, key: 'location' },
      { filter: this.yearFilter, key: 'year' }
    ];

    this.filters.forEach(({ filter, key }) => {
      filter.on("change", (e) => { if(!this.isClearFilter) {this.checkFilters(); this.filterClicked(e, key);} });
    });

    this.clearFilterBtn.on("click", this.clearFilters);

    // this.bannerFilters.on("change", this.filterSearch);

    let timeout = 2000;
    if ($(this.body).hasClass("visited")) {
      timeout = 3200;
    }
    setTimeout(() => {
      this.fadeEachCard();

      if (this.window.width() < 1279) {
        this.openFilters();
      }
    }, timeout);
  };


  filterClicked = (e, key) => {

    const input = e.currentTarget ? e.currentTarget : e;
    this.selected[key] = $(input).data("value") || $(input).val();
    this.resetPage();
    this.filterSearch();
  };


  clearContainer = () => {
    this.itemList.empty();
  };

  delayFilter = () => {
    this.resetPage();
    clearInterval(this.timer);
    this.timer = setTimeout(() => {
      this.filterSearch();
    }, 1000);
  };

  resetPage = () => {
    this.page = 1;
    this.itemsCounter = 0;
  };

  filterSearch = () => {
    this.loadMoreDiv.hide();
    this.resetPage();
    this.showLoader();
    this.clearContainer();
    $.ajax({
      dataType: "html",
      type: "GET",
      data: this.formFilters(),
      url: this.getCurrentPagePath(),
      success: this.applyFilters,
    });
  };

  applyFilters = (response) => {
    this.hideLoader();
    response = JSON.parse(response);
    let data;
    if (response.success) {
      data = $(response.data);
      this.itemList.empty();
      this.itemList.append(data);
    }
    this.itemsCounter = response.itemsCounter;
    this.checkLoadMore(response.loadMoreValue);
    this.checkErrorMessage(response.data);
    this.addPushState();

    setTimeout(() => {
      new LazyLoading();
      if (this.window.width() > 1279) {
        this.parallaxAnim();
      }
      this.fadeEachCard();
    }, 500);
  };

  showLoader = () => {
    this.loadMoreDiv.hide();
    this.loader.show();
  };

  checkErrorMessage = (value) => {
    !value ? this.errorMessage.show() : this.errorMessage.hide();
  };

  addPushState = () => {
    if (window.history.pushState) {
      window.history.pushState(
        "",
        "",
        "?keywords=" +
          this.getKeywords() +
          "&range=" +
          this.getRange() +
          "&product=" +
          this.getProduct() +
          "&application=" +
          this.getApplication() +
          "&specification=" +
          this.getSpecification() +
          "&location=" +
          this.getLocation() +
          "&year=" +
          this.getYear()
      );
    }
  };

  getCurrentPagePath = () => {
    return location.origin + location.pathname;
  };

  checkLoadMore = (value) => {
    value ? this.loadMoreDiv.parent().show() : this.loadMoreDiv.parent().hide();
  };

  addPage = () => {
    this.page += 1;
  };

  nextPage = () => {
    this.showLoader();
    clearTimeout(this.timer);
    this.timer = setTimeout(this.loadMore, 1000);
  };

  hideLoader = () => {
    this.loader.hide();
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

  formFilters = () => {
    return {
      page: this.page,
      keywords: this.getKeywords(),
      range: this.getRange(),
      product: this.getProduct(),
      application: this.getApplication(),
      specification: this.getSpecification(),
      location: this.getLocation(),
      year: this.getYear(),
      ccm_token: this.getToken(),
      itemsCounter: encodeURIComponent(this.itemsCounter),
    };
  };

  getKeywords = () => {
    return this.keywords.val() ? encodeURIComponent(this.keywords.val()) : "";
  };

  getRange = () => {
    return this.selected['range'] ? encodeURIComponent(this.selected['range']) : "";
  };

  getProduct = () => {
    return this.selected['product'] ? encodeURIComponent(this.selected['product']) : "";
  };

  getApplication = () => {
    return this.selected['applications']
      ? encodeURIComponent(this.selected['applications'])
      : "";
  };

  getSpecification = () => {
    return this.selected['specification']
      ? encodeURIComponent(this.selected['specification'])
      : "";
  };

  getLocation = () => {
    return this.selected['location']
      ? encodeURIComponent(this.selected['location'])
      : "";
  };

  getYear = () => {
    return this.selected['year'] ? encodeURIComponent(this.selected['year']) : "";
  };

  getToken = () => {
    return this.token ? this.token.val() : "";
  };

  appendItems = (response) => {
    this.hideLoader();
    response = JSON.parse(response);
    if (response.success) {
      if (response.data) {
        this.itemList.append(response.data);
      }
      this.checkLoadMore(response.loadMoreValue);
      this.itemsCounter = response.itemsCounter;
    }

    setTimeout(() => {
      new LazyLoading();
      if (this.window.width() > 1279) {
        this.parallaxAnim();
      }
      this.fadeEachCard();
    }, 500);
  };

  parallaxAnim = () => {
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

          container.classList.add("animated");
        });
      }
    }
  };

  fadeEachCard = () => {
    if (this.window.width() > 1279) {
      let card = this.mainContainer.find(".case-study-card:not(.opaque)");

      $(card).each((i, el) => {
        let anchor = $(el).find("a");

        let t1 = gsap.timeline({});

        t1.to($(anchor), {
          opacity: 1,
          duration: 0.8,
          ease: "power2.out",
        });

        ScrollTrigger.create({
          trigger: $(el),
          start: "top 90%",
          animation: t1,
        });

        $(el).addClass("opaque");
      });
    } else {
      let card = this.mainContainer.find(".case-study-card:not(.opaque)");

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
          start: "top 90%",
          animation: t2,
        });

        $(el).addClass("opaque");
      });
    }
  };

  openFilters = () => {
    // this.mainContainer.find('.banner-filters.desktop').detach();

    let mobile_filters = this.mainContainer.find(".filter--section-mobile");

    this.openFilterBtn.on("click", () => {
      if (!$(mobile_filters).hasClass("active")) {
        $(mobile_filters).addClass("active");
        this.body.addClass("active");
      }
    });

    //mobile filters
    let mobileFilters = $(".filter--section-mobile").find(".select-box");

    let input = $(mobileFilters).find(".selected-field");

    $(input).on("click", (e) => {
      console.log($(e.currentTarget));
      let $this = $(e.currentTarget);

      let currentContainer = $this.parent(".select-box");

      if (!$($this).hasClass("active")) {
        $(input).removeClass("active");
        $(mobileFilters).find("ul").slideUp(500);
        $($this).addClass("active");
        $(currentContainer).find("ul").slideDown(500);
      } else {
        $($this).removeClass("active");
        $(currentContainer).find("ul").slideUp(500);
        
      }
    });

    let options = $(mobileFilters).find("ul li");

    $(options).on("click", (e) => {
      let currentOption = $(e.currentTarget);
      let value = $(currentOption).data("value");
      let text = $(currentOption).text();
      let parentTab = $(currentOption).closest(".select-box");
      // console.log($(parentTab), value);

      let currentSelectInput = $(parentTab).find("select");

      console.log($(currentSelectInput));

      $(currentSelectInput).val(value).change();
      $(parentTab).find(".selected-field span").text(text);
      $(parentTab).find(".selected-field").removeClass("active");
      $(currentOption).parent("ul").slideUp(500);
    });

    let closeBtn = $(mobile_filters).find(".close");

    $(closeBtn).on("click", () => {
      if ($(mobile_filters).hasClass("active")) {
        $(mobile_filters).removeClass("active");
        this.body.removeClass("active");
        if ($(mobileFilters).find(".selected-field").hasClass("active")) {
          $(mobileFilters).find(".selected-field").removeClass("active");
          $(mobileFilters).find(".selected-field").next("ul").slideUp(500);
        }
      }
    });
  };

  clearFilters = () => {
    this.isClearFilter = true;

    this.searchWrapper.removeClass("active");

    this.filters.forEach(({filter, key}) => {
      filter.val(null).trigger("change");
      this.selected[key] = "";
    });

    this.filterSearch();

    this.isClearFilter = false;

    if(this.window.width() < 1279) this.mobileFilters.find(".select-box .selected-field > span").text('All');
  }

  checkFilters = () => {
    let isFilterApplied = false;
    this.filters.forEach(({filter}) => { filter.each((index, input) => {if($(input).val()) isFilterApplied = true; }); });
    if(isFilterApplied) this.searchWrapper.addClass("active");
    else this.searchWrapper.removeClass("active");
  }
}
