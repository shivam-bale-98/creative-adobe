import loadGoogleMapsApi from "load-google-maps-api";
import { mapStyle, mapOptions } from "../constants";
import Swiper from "swiper";
import { Navigation } from "swiper/modules";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { debounce } from "../../../utils";

gsap.registerPlugin(ScrollTrigger);

export default class Maps {
  constructor() {
    this.mainContainer = $('.location-block');
    this.locationBlock = document.querySelectorAll(".location-block");
    this.locationSlider = this.locationBlock[0].querySelectorAll(".swiper");
    this.nextEl = this.locationBlock[0].querySelectorAll(".swiper-button-next");
    this.prevEl = this.locationBlock[0].querySelectorAll(".swiper-button-prev");
    this.mapContainer = document.getElementById("location_map");
    this.mapKey = this.mapContainer.dataset.key;

    this.initCoordinate = null;
    this.markers = [];
    console.log(this.mapContainer);
    console.log(this.mapKey, 'api key');
    if (this.mapContainer) {
      loadGoogleMapsApi({key: this.mapKey,}).then((google) => {
        this.google = google;
        this.init(google);
      });
    }

    this.bindEvents();
  }

  init = (google) => {
    this.myLatlng = new google.LatLng(25.187738, 55.258221);
    this.mapCenter = new google.LatLng(25.146218, 55.229845);
    this.mapIconUrl = `${CCM_REL}/application/themes/channeline/assets/images/pin_icon.svg`;
    this.mapIconUrlActive = `${CCM_REL}/application/themes/channeline/assets/images/pin_icon_active.svg`;
    this.map = new google.Map(this.mapContainer, {
      ...mapOptions(google),
      ...{
        center: this.mapCenter,
        styles: mapStyle,
        zoom: 3,
      },
    });
   
    setTimeout(()=> {
      this.bindEvents();
    }, 1000)
  };

  bindEvents = () => {
    // this.google.event.addListener(this.marker, 'click', this.toggleBounce);
    let markers = [];
    new Swiper(this.locationSlider[0], {
      modules: [Navigation],
      slidesPerView: 1.1,
      spaceBetween: 15,
      speed: 1000,
      freeMode: true,
      slideToClickedSlide: true,
      navigation: {
        nextEl: this.nextEl[0],
        prevEl: this.prevEl[0],
      },

      breakpoints: {
        767: {
          slidesPerView: 1.5,
          spaceBetween: 10,
        },
        991: {
          slidesPerView: 1,
          spaceBetween: 10,
        },
        // 1279: {
        //   slidesPerView: 1,
        //   spaceBetween: 10,
        // },
      },

      on: {
        init: (swiper) => {
          let slides = this.mainContainer.find(".swiper-slide");
          
          setTimeout(()=> {
            
            slides.each((index, ele) => {
              let slide = $(ele);
              slide.addClass("marked");
              let lat = parseFloat(slide.attr("data-lat"));
              let lng = parseFloat(slide.attr("data-long"));
              let marker = null;
              // setMapOnAll(null);
  
              if (lat && lng) {
                if (!this.initCoordinate) 
                this.initCoordinate = { lat, lng };
                
                
                marker = new this.google.Marker({
                  position: { lat, lng },
                  map: this.map,
                  icon: this.mapIconUrl,
                  title: `Maps${index}`,
                  // animation: this.google.Animation.DROP,
                });

                console.log(marker);
  
                slide.attr("data-markerIndex", markers.length);
                markers.push(marker);
  
                if (slide.hasClass("swiper-slide-active")) {
                  marker.setIcon(this.mapIconUrlActive);
                }

                this.google.event.addListener(marker, "click", debounce(() => {
                
                  let str = marker.title;
                  let match = str.match(/\d+/);

                  if (match) {
                    let number = parseInt(match[0], 10);

                    if (swiper.realIndex !== number) {
                      markers.forEach((ele, ind) => {
                        ele.setIcon(this.mapIconUrl);
                      });
                      marker.setIcon(this.mapIconUrlActive);
                      console.log(swiper.realIndex, number);
                      swiper.slideTo(number, 500, false);
                      

                      

                      setTimeout(() => {
                  
                        lat = parseFloat(activeSlide.attr("data-lat"));
                        lng = parseFloat(activeSlide.attr("data-long"));
                        this.map.setCenter({ lat, lng });
                      }, 600);
                    }
                  }

              }, 1000));
              }
            });

            this.map.setCenter(
              this.initCoordinate ?? {
                lat: 25.19674,
                lng: 55.28552,
              }
            );
          }, 2000);


          setTimeout(()=> {
            let minHeight = this.mainContainer.find('.contact-us.swiper').height();
            console.log(minHeight);

            this.mainContainer.find('.contact-us--listing').css('min-height', minHeight);
          }, 3000)

          
        },
        slideChangeTransitionEnd: (swiper) => {
          //  setTimeout(()=> {

          let activeSlide = this.mainContainer.find(".swiper-slide-active");
          let lat = parseFloat(activeSlide.attr("data-lat"));
          let lng = parseFloat(activeSlide.attr("data-long"));
          let marker = markers[activeSlide.attr("data-markerIndex")];

          if (lat && lng) {
            marker.setIcon(this.mapIconUrlActive);
            // marker.setAnimation(this.google.Animation.BOUNCE);
            this.map.setCenter({ lat, lng });
            this.map.setZoom(4);
          }
          //  }, 500)
        },
        slideChangeTransitionStart: () => {
          markers.forEach((ele, ind) => {
            ele.setIcon(this.mapIconUrl);
          });
          this.map.setZoom(3);
        },
      },
    });
  };

  toggleBounce = (markerClicked) => {
    if (markerClicked.getAnimation() != null) {
      markerClicked.setAnimation(null);
    } else {
      markerClicked.setAnimation(this.google.Animation.BOUNCE);
    }
  };
}
