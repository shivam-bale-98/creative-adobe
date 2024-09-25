import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
gsap.registerPlugin(ScrollTrigger);
export default class SearchListing {
  constructor() {
    this.init();
    this.bindEvents();
  }

  init = () => {
    this.window = $(window);
    this.page = 1;
    this.mainContainer = $(".search-main-container");
    this.token = this.mainContainer.find('input[name="ccm_token"]');
    this.itemList = this.mainContainer.find(".search--listing");
    this.errorMessage = this.mainContainer.find(".error-message");
    this.filterOptions = this.mainContainer.find(".swiper-slide");
    this.keywords = this.mainContainer.find("#keywords");
    this.searchResultDiv = this.mainContainer.find(".search-result");
    this.searchCount = this.mainContainer.find(".search--count");
    this.searchIcon = this.mainContainer.find("#search--btn");
    this.loadMoreDiv = this.mainContainer.find(".load--more--btn");
    this.filterCategory = this.filterOptions.find(".active").attr("data-value");
    this.loader = this.mainContainer.find(".loader");
    this.autoLoad = true;
  };

  bindEvents = () => {
    this.filterOptions.find("a").on("click", this.filtersChanged);
    this.keywords.on("keyup", this.delayFilter);
    this.searchIcon.on("click", this.delayFilter);
    this.window.on("scroll", this.windowScroll);
    this.loadMoreDiv.on("click", this.nextPage);
    let t_out = 2000;
    if ($(this.body).hasClass("visited")) {
		t_out = 0;
    }
    setTimeout(() => {
      this.animations();
      this.searchCardAnims();
    }, t_out);
  };

  filtersChanged = (e) => {
    let input = e.currentTarget ? e.currentTarget : e;
    this.filterCategory = input.getAttribute("data-value");
    this.resetPage();
    this.filterSearch();
  };

  delayFilter = () => {
    this.showLoader();
    this.resetPage();
    clearInterval(this.timer);
    this.timer = setTimeout(() => {
      this.filterSearch();
    }, 100);
  };

  resetPage = () => {
    this.page = 1;
  };

  nextPage = () => {
    this.showLoader();
    clearTimeout(this.timer);
    this.timer = setTimeout(this.loadMore, 500);
  };

  hideLoader = () => {
    this.loader.hide();
  };

  showLoader = () => {
    this.loadMoreDiv.hide();
    this.loader.show();
  };

  filterSearch = () => {
    this.loadMoreDiv.hide();
    this.resetPage();
    this.showLoader();

    $.ajax({
      dataType: "html",
      type: "GET",
      data: this.formFilters(),
      url: this.getCurrentPagePath(),
      success: this.applyFilters,
    });
  };

  checkLoadMore = (value) => {
    if (value) {
      this.loadMoreDiv.addClass("d-flex");
      this.loadMoreDiv.show();
    } else {
      this.loadMoreDiv.removeClass("d-flex");
      this.loadMoreDiv.hide();
    }
  };

  applyFilters = (response) => {
    this.hideLoader();
    response = JSON.parse(response);
    let data;
    data = $(response.data);
    response.total == 0
      ? this.searchResultDiv.hide()
      : this.searchCount.html(response.countMessage);
    this.itemList.empty();
    this.itemList.append(data);
    this.autoLoad = response.loadMoreValue;
    this.checkErrorMessage(response.data);
    this.checkLoadMore(response.hasNextPage);
    this.addPushState();

    setTimeout(() => {
      this.searchCardAnims();
    }, 100);
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

  checkErrorMessage = (value) => {
    value === "" ? this.errorMessage.show() : this.errorMessage.hide();
  };

  addPushState = () => {
    if (window.history.pushState) {
      window.history.pushState(
        "",
        "",
        "?keywords=" + this.getKeywords() + "&search=" + this.getCategory()
      );
    }
  };

  getCurrentPagePath = () => {
    return location.origin + location.pathname;
  };

  addPage = () => {
    this.page += 1;
  };

  formFilters = () => {
    return {
      page: this.page,
      keywords: this.getKeywords(),
      search: this.getCategory(),
      ccm_token: this.getToken(),
    };
  };

  getKeywords = () => {
    return this.keywords.val() ? encodeURIComponent(this.keywords.val()) : "";
  };

  getCategory = () => {
    return this.filterCategory ? encodeURIComponent(this.filterCategory) : "";
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
      this.autoLoad = response.loadMoreValue;
      this.checkLoadMore(response.hasNextPage);

      setTimeout(() => {
        this.searchCardAnims();
      }, 100);
    }
  };

  animations = () => {
    let filters = this.mainContainer.find(".searchSlider");

	if($(filters).length) {
		let t1 = gsap.timeline({});

		t1.to($(filters), {
		  duration: 0.6,
		  opacity: 1,
		  ease: "sine.out",
		});
	}
    

    let results = this.mainContainer.find(".search-wrapper .search-result p");
    let t2 = gsap.timeline({
		onComplete: ()=> {
			this.mainContainer.find(".search--listing").addClass('b')
		}
	});

    t2.to($(results), 0.5, {
      opacity: 1,
      ease: "sine.out",
    });




  };

  searchCardAnims = () => {
    let title_trim__length = 28;
    let desc_trim__length = 129;
    // if (this.window.width() >= 991) {
    //   trim_length = 4;
    // } else {
    //   trim_length = 6;
    // }
    let searchCard = this.mainContainer.find(
      ".search--listing .search_card:not(.animated)"
    );

    // let cardArray = $(brandGridCards).toArray();

    $(searchCard).each((ind, el) => {
      let cards = $(el);

      //truncation
      let title = $(el).find("h4");
      let title_string = $(title).text();
      if (title_string.length >= title_trim__length) {
        let trimmedTitleString =
          title_string.substring(0, title_trim__length) + "..";
        // console.log(trimmedTitleString);
        $(title).text(trimmedTitleString);
      }

      let desc = $(el).find("p");
      let desc_string = $(desc).text();
      if (desc_string.length >= desc_trim__length) {
        let trimmedDescString =
          desc_string.substring(0, desc_trim__length) + "..";
        // console.log(trimmedDescString);
        $(desc).text(trimmedDescString);
      }

      //animations
      let tg2 = gsap.timeline({});
    //   let tg20 = gsap.timeline({});

      tg2.to($(cards), 0.9, {
        opacity: 1,
        ease: "sine.out",
        delay: 0.06 * ind,
      });

      

      ScrollTrigger.create({
        trigger: $(cards),
        start: "top 90%",
        animation: tg2,
      });

      

      $(cards).addClass("animated");
    });
  };
}
