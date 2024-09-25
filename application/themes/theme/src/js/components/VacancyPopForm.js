import gsap from "gsap";
import Lenis from "@studio-freight/lenis";

import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export default class VacancyPopForm {
	constructor() {
		this.init();
		this.bindEvents();
	}

	init = () => {
		this.window = $(window);
		this.body = $("body");
		this.mainContainer = $(".vacancy-details");
		this.html = $("html");
		// this.htmlBody = htmlBody;
	};

	bindEvents = () => {
		this.popUpForm();
	};

	popUpForm = () => {
		let popUp = this.mainContainer.find(".vacancy-form-popUp");

		let openPopUpBtn = this.mainContainer.find(".channeline-btn");
		let closePopUpBtn = $(popUp).find(".close");

		$(openPopUpBtn).on("click", function () {
			var idVal = $(this).attr("id");
			var popUpID = idVal.split("_")[1];

			$(`#popUp-${popUpID}`).addClass("active");
			$("body").addClass("overflow-hidden");
			//Lenis.stop();
			console.log("Pop-up button clicked. Pop-up ID:", popUpID);
			// if (this.html.hasClass("lenis")) {
			// }
		});

		$(".vacancy-form-popUp .close").on("click", function () {
			$(popUp).removeClass("active");
			$("body").removeClass("overflow-hidden");
			//Lenis.start();
		});
	};
}
