export default class Label {
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
	};

	bindEvents = () => {
		$(
			'.form-group[data-formidable-type="phone"][data-formidable-handle="phone_number"]',
		).addClass("phone-label");

		$(
			'.form-group[data-formidable-type="file"][data-formidable-handle="attach_cv"]',
		).addClass("attach-cv-label");

		$(".dz-message").text("");
	
	};
}
