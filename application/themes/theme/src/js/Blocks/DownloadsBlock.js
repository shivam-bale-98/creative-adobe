import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
export default class DownloadsBlock {
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
        this.page           = 1;
        this.mainContainer  = $('.download--block');
        this.token          = this.mainContainer.find('input[name="ccm_token"]');
        this.itemList       = this.mainContainer.find('.downloads--listing');
        this.errorMessage   = this.mainContainer.find(".error-message");
        this.category     = this.mainContainer.find("#download-categories");

        this.categoryFilter = this.mainContainer.find(".filter--section a");
        this.category       = this.categoryFilter.find('.channeline-btn').attr('data-value');

        this.downloadsBlock =  document.querySelectorAll('.download--block');
        this.downloadFilters = this.downloadsBlock[0].querySelectorAll('.filter-swiper');
        this.cardsSlider = this.downloadsBlock[0].querySelectorAll('.download-cards-list');
        
        if(this.window.width() > 767) {
            this.NextEl = this.downloadsBlock[0].querySelectorAll('.js-desktop .swiper-button-next');
            this.prevEl = this.downloadsBlock[0].querySelectorAll('.js-desktop .swiper-button-prev');
            
        } else {
            this.NextEl = this.downloadsBlock[0].querySelectorAll('.js-mobile .swiper-button-next');
            this.prevEl = this.downloadsBlock[0].querySelectorAll('.js-mobile .swiper-button-prev');
        }
    };

    bindEvents = () => {
        this.categoryFilter.on("click", this.categoryClicked);
        setTimeout(()=> {
            this.downdloadSlider();
            this.animation();
        }, 500);

       if(this.window.width() < 767) {
        this.filtersSlider();
       }
    };

    categoryClicked = (e) => {
        let input = e.currentTarget ? e.currentTarget : e;
        this.category = input.getAttribute("data-value");
        this.categoryFilter.removeClass('active');
        if(!$(input).hasClass('active')) {
            $(input).addClass('active');
        }
        this.resetPage();
        this.filterSearch();
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
            this.itemList.html(response.listHTML);
        }
        this.checkErrorMessage(response.data);
        this.addPushState();

        setTimeout(()=> {
            this.downdloadSlider();
            this.animation();
        }, 500)
    };

    checkErrorMessage = (value) => {
        (value ==='') ? this.errorMessage.show() : this.errorMessage.hide();
    };

    addPushState = () => {
        if (window.history.pushState) {
            window.history.pushState('', '',
                '?category=' + this.getCategory()
            );
        }
    }

    getCurrentPagePath = () => {
        return CCM_DISPATCHER_FILENAME + "/api/v1/blocks/download_block";
    };

    addPage = () => {
        this.page += 1;
    };

    formFilters = () => {
        return {
            page      : this.page,
            category  : this.getCategory(),
            ccm_token : this.getToken(),
        };
    };

    getCategory = () => {
        return this.category ? encodeURIComponent(this.category) : "";
    };

    getToken = () => {
        return this.token ? (this.token.val()) : "";
    };


    downdloadSlider = () => {
        new Swiper(this.cardsSlider[0], {
            modules: [Navigation],
            slidesPerView: "auto",
            spaceBetween: 20,
            speed: 1000,
            navigation: {
              nextEl: this.NextEl[0],
              prevEl: this.prevEl[0],
            },
          });
    }


    filtersSlider = () => {
        new Swiper(this.downloadFilters[0], {
            slidesPerView: "auto",
            spaceBetween: 10,
            speed: 1000,
          });
    }


    animation = () => {
        let card = this.mainContainer.find('.download-cards-list .swiper-slide');

        $(card).each((ind, el) => {
            let t1 = gsap.timeline({delay: 0.05* ind, onComplete: ()=> {
                // gsap.set(el, { clearProps: 'transform' });

                // $(el).addClass('loaded');
            }});

            t1.to($(el), {
                y: "0px",
                opacity: 1,
                duration: 0.8,
                ease: 'sine.out',
            });

            ScrollTrigger.create({
                trigger: this.mainContainer.find('.download-cards-list'),
                start: "top 90%",
                animation: t1
            })
        });
    }
}