import GLightbox from "glightbox";

export default class TechnologyVideo {
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
    this.block = $(".technology");
  };

  bindEvents = () => {
    this.block.each((i, el) => {
      if ($(el).find(".glightbox_video").length) {
        const lightbox = GLightbox({
          selector: ".glightbox_video",
          touchNavigation: true,
          skin: 'video-popup',
          closeButton: true
        });

        console.log(lightbox);
      }
    });
  };
}
